<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        AuthenticatesUsers::login as defaultLogin;
        AuthenticatesUsers::logout as performLogout;
    }

    /**
     * @return string
     */
    protected function redirectTo()
    {
        $user = Auth::user();
        if ($user->onlySpManageRight()) {
            return route('marketplace.index');
        } else {
            return route('manage.property.select');
        }
    }

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * First step when user login.
     *
     * @param Request $request
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $user = $this->guard()->getProvider()->retrieveByCredentials($this->credentials($request));
        if ($user) {
            if ($user->is_disabled) {
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('auth.disable'),
                    ]);
            } elseif (null == $user->activated_at) {
                return redirect()->back()
                    ->withInput($request->only($this->username(), 'remember'))
                    ->withErrors([
                        $this->username() => __('auth.active'),
                        'inactive' => $request->email,
                    ]);
            }
        }

        return $this->defaultLogin($request);
    }

    /**
     * rewrite redirect to login page.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->performLogout($request);
        session()->flash('logout', trans('auth.logout'));

        return redirect('/login');
    }
}
