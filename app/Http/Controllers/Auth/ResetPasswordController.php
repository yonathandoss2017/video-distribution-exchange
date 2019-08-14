<?php

namespace App\Http\Controllers\Auth;

use Hash;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordResets;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords {
        ResetsPasswords::reset as defaultReset;
    }

    /**
     * Where to redirect users after reset password.
     *
     * @var string
     */
    //TODO how to redirect?
    protected $redirectTo = '/manage';

    /**
     * First step when reset password.
     *
     * @param Request $request
     *
     * @return $this
     */
    public function reset(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        $request->session()->regenerate();
        $response = $this->defaultReset($request);
        if (null === Session('errors')) {
            $user = User::where('email', $request->email)->first();
            if (null == $user->activated_at) {
                $user->activated_at = Carbon::now();
                $user->save();
            }
            session()->flash('success', __('passwords.pwd_updated_success'));
            $request->session()->put('organization', $user->organizations()->first()->id);
        } else {
            return redirect('login')
                ->with('invalid_token', trans('signup.invalid_token'));
        }

        return $response;
    }

    /**
     * Display the activate password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param string|null $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showActivateResetForm($url_encoded_email = null, $token = null)
    {
        $message = $this->validateToken($url_encoded_email, $token);
        if ($message) {
            return redirect()->route('login')
                    ->with('invalid_token', $message);
        }

        return view('auth.passwords.activate_reset')->with([
            'token' => $token,
            'decoded_email' => urldecode($url_encoded_email),
                ]);
    }

    /**
     * Show form when click to activate link for forgot password.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null              $token
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $url_encoded_email, $token = null)
    {
        $message = $this->validateToken($url_encoded_email, $token);
        if ($message) {
            return redirect()->route('login')
                    ->with('invalid_token', $message);
        }

        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    private function validateToken($url_encoded_email, $token)
    {
        $invalidTokenMsg = trans('signup.invalid_token');
        if (empty($token)) {
            return $invalidTokenMsg;
        }

        $email = urldecode($url_encoded_email);
        $passwordRecord = PasswordResets::whereEmail($email)->first();
        if (!$passwordRecord) {
            return trans('passwords.user');
        }

        if (!Hash::check($token, $passwordRecord->token)) {
            return $invalidTokenMsg;
        }

        return null;
    }
}
