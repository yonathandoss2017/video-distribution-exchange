<?php

namespace App\Http\Controllers\Manage;

use App\Models\Role;
use App\Models\User;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:organization-admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $current_org = Organization::Organization();
        $users = Organization::join(\DB::raw('(select min(created_at) as created_at, min(organization_id) as organization_id, user_id from role_user where organization_id = '.$current_org->id.' group by user_id ) temp'), 'organizations.id', 'temp.organization_id')
            ->join('users', 'temp.user_id', 'users.id')
            ->where('organizations.id', $current_org->id)
            ->orderBy('users.created_at', 'desc')
            ->select(['users.id', 'users.name', 'users.email', 'users.activated_at', 'users.is_disabled'])
            ->paginate(10);

        return view('manage.user.user-list', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $properties = Organization::Organization()->properties;
        $cpRoles = Role::getRoles(Property::TYPE_CP);
        $spRoles = Role::getRoles(Property::TYPE_SP);
        $types = [
            Property::TYPE_SP_PLUS => 'SP+',
            Property::TYPE_SP => 'SP',
            Property::TYPE_CP => 'CP',
        ];

        return view('manage.user.add-user', compact('properties', 'types', 'cpRoles', 'spRoles'));
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
            'name' => 'required',
            'email' => 'required|email',
        ]);

        DB::beginTransaction();
        $store_success = false;
        $organization = Organization::Organization();
        try {
            //find or create a user
            $user = User::findOrCreate($request->email, $request->name);

            //set all the roles
            $noRole = true;
            $exist = $user->organizations()->where('role_user.organization_id', $organization->id)->first();
            if ($exist) {
                DB::rollBack();
                session()->flash('error', __('manage/organization/user.user_already_exists'));

                return back();
            }

            if ('yes' == $request->input('is_admin')) {
                $role = Role::where('name', Role::ORGANIZATION_ADMIN)->first();
                $user->roles()->attach($role->id, ['organization_id' => $organization->id, 'property_id' => Property::ID_FOR_ADMIN]);
                $noRole = false;
            } else {
                $properties = $organization->properties;
                foreach ($properties as $property) {
                    if ($request->filled($property->id.'-role')) {
                        $role = $request->input($property->id.'-role');
                        if ($role > 0) {
                            $user->roles()->attach($role, ['organization_id' => $organization->id, 'property_id' => $property->id]);
                            $noRole = false;
                        }
                    }
                }
            }

            if ($noRole) {
                DB::rollBack();
                session()->flash('error', __('manage/organization/user.missing_access_level'));

                return back();
            }
            $store_success = true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        if ($store_success) {
            $user->sendAdminNotification(__('manage/organization/user.notify_user_authorized', ['name' => $organization->name]), null, App::getLocale());
        }

        return redirect('manage/users');
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
        if ($user == Auth::user()) {
            return back();
        }
        $types = [
            Property::TYPE_SP_PLUS => 'SP+',
            Property::TYPE_SP => 'SP',
            Property::TYPE_CP => 'CP',
        ];
        $cpRoles = Role::getRoles(Property::TYPE_CP);
        $spRoles = Role::getRoles(Property::TYPE_SP);
        $properties = Organization::Organization()->properties()->with(['roles' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
        $isAdmin = $user->isAdmin();

        return view('manage.user.edit-user', compact('user', 'isAdmin', 'properties', 'cpRoles', 'spRoles', 'types'));
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
        if ($user == Auth::user()) {
            return back();
        }
        DB::beginTransaction();
        $update_success = false;
        /*
         * Do not use sync here, it will remove clear user's role in other organization's properties.
         */
        $organization = Organization::Organization();
        try {
            $role = Role::where('name', Role::ORGANIZATION_ADMIN)->first();
            $noRole = true;
            if ('yes' == $request->input('is_admin')) {
                $user->organizations()->detach($organization->id);
                $user->roles()->attach($role->id, ['organization_id' => $organization->id, 'property_id' => Property::ID_FOR_ADMIN]);
                $noRole = false;
            } else {
                $properties = $organization->properties;
                $user->organizations()->detach($organization->id);
                foreach ($properties as $property) {
                    if ($request->filled($property->id.'-role')) {
                        $role = $request->input($property->id.'-role');
                        if ($role > 0) {
                            $user->roles()->attach($role, ['organization_id' => $organization->id, 'property_id' => $property->id]);
                            $noRole = false;
                        }
                    }
                }
            }

            if ($noRole) {
                DB::rollBack();
                session()->flash('error', __('manage/organization/user.missing_access_level'));

                return back();
            }
            $update_success = true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        if ($update_success) {
            $user->sendAdminNotification(__('manage/organization/user.notify_user_authorized', ['name' => $organization->name]), null, App::getLocale());
        }

        return redirect('manage/users');
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
        $user->organizations()->detach(Organization::Organization()->id);
        Session::flash('success', __('manage/organization/user.user_success_deleted'));

        return back();
    }
}
