<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use app\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RedirectsUsers;

class ActivateUserController extends Controller
{
    use RedirectsUsers;

    /**
     * Where to redirect users after activate.
     *
     * @var string
     */
    protected $redirectTo = '/manage';

    /**
     * Verify that the user is active.
     *
     * @param null $token
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate($token = null)
    {
        $user_activates = DB::table('user_activations')->where('token', $token)->first();
        $if_expires = false;
        if ($user_activates) {
            $expiresAt = Carbon::parse($user_activates->created_at)->addMinutes(config('auth.activations.users.expire'));
            $if_expires = $expiresAt->isPast();
        }
        if (!$user_activates || $if_expires) {
            //expired
            if (Auth::id() > 0) {
                Auth::logout();
            }

            return redirect('login')
                ->with('invalid_token', trans('signup.invalid_token'));
        }
        $email = $user_activates->email;
        $user = User::where('email', $email)->first();
        if (null !== $user->activated_at) {
            //activated
            Auth::login($user);

            return redirect()->intended($this->redirectPath());
        }
        $user->activated_at = Carbon::now();
        $user->save();
        DB::table('user_activations')->where('token', $token)->delete();
        Auth::login($user);
        session()->flash('success', trans('signup.successful_activate'));

        return redirect()->intended($this->redirectPath());
    }
}
