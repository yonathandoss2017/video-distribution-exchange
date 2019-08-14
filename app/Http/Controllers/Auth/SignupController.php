<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use App\Models\Property;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_bak()
    {
        return view('auth.signup_backup');
    }

    public function create()
    {
        return view('auth.signup');
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
            'organization_name' => 'required',
            'organization_address' => 'required',
            'organization_country' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'agree' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'remarks' => $request->email,
                'longid' => md5($request->email),
            ]);
            $property_id = Property::where('name', '8sian international')->first()->id;
            $organization = Organization::Create([
                'name' => $request->organization_name,
                'address' => $request->organization_address,
                'country' => $request->organization_country,
            ]);
            $role = Role::where('name', Role::ORGANIZATION_ADMIN)->first();
            $user->roles()->attach($role->id, ['organization_id' => $organization->id, 'property_id' => Property::ID_FOR_ADMIN]);
            $user->sendVerifyEmail(App::getLocale());
            DB::commit();

            return redirect('signup_success');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors();
        }
    }

    /**
     * Show success page after signup.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function signupSuccess()
    {
        return view('auth.signup_success');
    }
}
