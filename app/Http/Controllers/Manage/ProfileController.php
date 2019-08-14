<?php

namespace App\Http\Controllers\Manage;

use Auth;
use Hash;
use Session;
use Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function profileSetting()
    {
        return view('manage.profile.index');
    }

    public function updateProfile(Request $request)
    {
        if (Auth::Check()) {
            $userId = Auth::user()->id;
            $email = Auth::user()->email;
            $password = $request->password;

            $rules = $this->rules($password);
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return back()->withInput()->withErrors($validator);
            }

            $profile = User::find($userId);
            $profile->name = $request->name;
            if (!empty($password)) {
                $profile->password = Hash::make($password);
            }
            $profile->save();

            if (!empty($password)) {
                session()->flash('success', __('manage/user_profile.relogin_after_change_pwd'));
            } else {
                session()->flash('success', __('manage/user_profile.update_success'));
            }

            return redirect()->route('manage.profile');
        } else {
            session()->flash('error', __('manage/user_profile.update_failed'));

            return back();
        }
    }

    public function rules($password)
    {
        if (!empty($password)) {
            $rules['password'] = 'min:6';
            $rules['confirm_password'] = 'same:password';
        }
        $rules['name'] = 'required|string|max:255|min:2';

        return $rules;
    }
}
