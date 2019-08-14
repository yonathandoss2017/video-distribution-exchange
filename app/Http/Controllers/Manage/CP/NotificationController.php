<?php

namespace App\Http\Controllers\Manage\CP;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Models\LicenseNotification;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    /**
     * Show users who can be enabled and disabled.
     *
     * @param $property_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function notifications($property_id)
    {
        $users = User::join('role_user', 'role_user.user_id', 'users.id')
            ->where('role_user.organization_id', Organization::Organization()->id)
            ->Where(function ($query) use ($property_id) {
                $query->where('role_user.property_id', $property_id)
                    ->orwhere('role_user.property_id', 0);
            })
            ->select('users.*')
            ->with(['licenseNotifications' => function ($query) use ($property_id) {
                return $query->where('property_id', $property_id);
            }])
            ->paginate(10);

        return view('manage.cp.email-notifications', compact('property_id', 'users'));
    }

    /**
     * Change notification status to enable.
     *
     * @param $property_id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable_notifications($property_id, Request $request)
    {
        if (empty($request->id)) {
            session()->flash('warning', __('manage/cp/settings/notification_settings.select_entry'));

            return back();
        }
        $ids = explode(',', $request->id);
        foreach ($ids as $id) {
            $license_notification = LicenseNotification::where([
                'user_id' => $id,
                'property_id' => $property_id,
            ])->first();
            if ($license_notification) {
                $license_notification->update([
                    'status' => LicenseNotification::ENABLE,
                ]);
            } else {
                LicenseNotification::create([
                    'user_id' => $id,
                    'property_id' => $property_id,
                    'status' => LicenseNotification::ENABLE,
                ]);
            }
        }

        return back();
    }

    /**
     * Change notification status to disable.
     *
     * @param $property_id
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable_notifications($property_id, Request $request)
    {
        if (empty($request->id)) {
            session()->flash('warning', __('manage/cp/settings/notification_settings.select_entry'));

            return back();
        }
        $ids = explode(',', $request->id);
        foreach ($ids as $id) {
            $license_notification = LicenseNotification::where([
                'user_id' => $id,
                'property_id' => $property_id,
            ])->first();
            if ($license_notification) {
                $license_notification->update([
                    'status' => LicenseNotification::DISABLE,
                ]);
            } else {
                LicenseNotification::create([
                    'user_id' => $id,
                    'property_id' => $property_id,
                    'status' => LicenseNotification::DISABLE,
                ]);
            }
        }

        return back();
    }
}
