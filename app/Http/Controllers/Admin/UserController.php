<?php

namespace App\Http\Controllers\Admin;

use Storage;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Mockery\Exception;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $org_role_id = Role::where('name', Role::ORGANIZATION_ADMIN)->value('id');
        $property_role_id = Role::whereIn('name', [Role::PROPERTY_MANAGER, Role::CONTENT_UPLOADER, Role::CENSOR])->pluck('id')->toArray();
        $search = $request->get('search');
        $users = User::when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%'.$search.'%');
        })
            ->with([
                'organizations' => function ($query) use ($org_role_id) {
                    return $query->wherePivot('role_id', $org_role_id);
                },
                'organizations.properties',
                'properties' => function ($query) use ($property_role_id) {
                    return $query->wherePivotIn('role_id', $property_role_id);
                },
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $cp_count = Property::where('type', Property::TYPE_CP)->count();
        $sp_count = Property::where('type', Property::TYPE_SP)->count();

        $users->map(function ($user) use ($cp_count, $sp_count) {
            $user->cp_count = 0;
            $user->sp_count = 0;
            //super admin
            if ($user->isSuperAdmin()) {
                $user->cp_count += $cp_count;
                $user->sp_count += $sp_count;
            } else {
                //super operator
                $isOperator = false;
                if ($user->isOperator()) {
                    $user->cp_count += $cp_count;
                    $isOperator = true;
                }

                //org admin
                if ($user->organizations->count() > 0) {
                    $user->organizations->map(function ($organization) use (&$user, $isOperator) {
                        $sp = $organization->properties->filter(function ($property) {
                            return Property::TYPE_SP == $property->type;
                        });
                        $user->sp_count += $sp->count();

                        if (!$isOperator) {
                            $cp = $organization->properties->filter(function ($property) {
                                return Property::TYPE_CP == $property->type;
                            });
                            $user->cp_count += $cp->count();
                        }
                    });
                }

                //property manager
                if ($user->properties->count() > 0) {
                    $user->properties->map(function ($property) use ($isOperator, &$user) {
                        if (Property::TYPE_SP == $property->type) {
                            ++$user->sp_count;
                        } elseif (Property::TYPE_CP == $property->type) {
                            if (!$isOperator) {
                                ++$user->cp_count;
                            }
                        }
                    });
                }
            }

            return $user;
        });

        return view('admin.pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $system_roles = Role::whereIn('name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR])->get();

        return view('admin.pages.user.create', compact('system_roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users|email',
            'name' => 'required',
            'password' => 'required|confirmed',
            'password_confirmation' => 'required',
            'role' => 'integer|exists:roles,id',
        ]);

        $activated_at = null;
        $is_disabled = false;
        if (1 == $request->is_active) {
            $activated_at = Carbon::now();
        } elseif (2 == $request->is_active) {
            $is_disabled = true;
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'remarks' => $request->email,
                'longid' => md5($request->email),
                'activated_at' => $activated_at,
                'is_disabled' => $is_disabled,
            ]);

            if ($request->role) {
                $user->roles()->attach($request->role, ['organization_id' => Organization::ID_FOR_SUPER_ADMIN, 'property_id' => Property::ID_FOR_ADMIN]);
            }
            DB::commit();
            session()->flash('success', __('admin/user.created_user'));
        } catch (Exception $e) {
            DB::rollback();
            session()->flash('error', __('admin/user.created_user_failed'));

            return back()->withInput();
        }

        return redirect('admin/user');
    }

    /**
     * Display the specified resource.
     *
     * @param  $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        $system_roles = Role::whereIn('name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR])->get();
        $user_system_role = $user->roles()->whereIn('name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR])->first();
        if ($user_system_role) {
            $user_system_role_id = $user_system_role->id;
        } else {
            $user_system_role_id = 0;
        }

        $user = $this->getUserRights($user);
        $types = [
            Property::TYPE_SP => 'SP',
            Property::TYPE_CP => 'CP',
        ];

        return view('admin.pages.user.edit', compact('user', 'system_roles', 'user_system_role_id', 'types'));
    }

    private function getUserRights($user)
    {
        $org_role_id = Role::where('name', Role::ORGANIZATION_ADMIN)->value('id');
        $property_role_id = Role::whereIn('name', [Role::PROPERTY_MANAGER, Role::CONTENT_UPLOADER, Role::CENSOR])->pluck('id')->toArray();

        $user->load([
            'properties' => function ($query) use ($property_role_id) {
                return $query->wherePivotIn('role_id', $property_role_id);
            },
            'properties.organization',
            'properties.roles' => function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            },
            'organizations' => function ($query) use ($org_role_id) {
                return $query->wherePivot('role_id', $org_role_id);
            },
            'roles' => function ($query) {
                return $query->whereIn('roles.name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR, Role::DPP_ADMIN]);
            },
        ]);

        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User                     $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // dd($request);
        $can_select_role = Role::whereIn('name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR])->select('id')->pluck('id')->toArray();

        $this->validate($request, [
            'name' => 'required',
            'password' => 'min:6|confirmed',
            'is_active' => 'required',
            'role' => [
                'integer',
                Rule::in($can_select_role),
            ],
        ], [
            'role.in' => trans('admin/user.invalid_user_role'),
        ]);

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        if (1 == $request->is_active && !$user->isActive()) {
            $user->activated_at = Carbon::now();
            $user->is_disabled = false;
        } elseif (0 == $request->is_active) {
            $user->activated_at = null;
            $user->is_disabled = false;
        } elseif (2 == $request->is_active) {
            $user->activated_at = null;
            $user->is_disabled = true;
        }

        $user_role = $user->roles()->whereIn('name', [Role::SUPER_ADMIN, Role::SUPER_OPERATOR])->first();

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->save();

            if ($request->role) {
                if ($user_role) {
                    $user->roles()->updateExistingPivot($user_role->id, ['role_id' => $request->role]);
                } else {
                    $user->roles()->attach($request->role, ['organization_id' => Organization::ID_FOR_SUPER_ADMIN, 'property_id' => Property::ID_FOR_ADMIN]);
                }
            } else {
                if ($user_role) {
                    $user->roles()->detach($user_role->id);
                }
            }

            DB::commit();
            session()->flash('success', __('admin/user.updated_user'));
        } catch (Exception $exception) {
            DB::rollback();
            session()->flash('error', __('admin/user.updated_user_failed'));

            return back()->withInput();
        }

        return redirect('admin/user');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $roleUsers = $user->roles()->withPivot('user_id', 'role_id', 'organization_id', 'property_id', 'created_at', 'updated_at')->get();
        $roleUserBackup = [];
        foreach ($roleUsers as $roleUser) {
            array_push($roleUserBackup, $roleUser->pivot->user_id.','.$roleUser->pivot->role_id.','.$roleUser->pivot->organization_id.','.$roleUser->pivot->property_id.','.$roleUser->pivot->created_at.','.$roleUser->pivot->updated_at);
        }
        $roleUserBackup = implode('|', $roleUserBackup);
        DB::beginTransaction();
        try {
            $user->role_user_backup = $roleUserBackup;
            $user->save();
            // delete
            $user->delete();
            $user->roles()->detach();
            $user->licenseNotifications()->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', __('admin/user.deleted_user_failed'));
        }

        // redirect
        Session::flash('success', __('admin/user.deleted_user'));

        return redirect()->route('admin.user.index');
    }
}
