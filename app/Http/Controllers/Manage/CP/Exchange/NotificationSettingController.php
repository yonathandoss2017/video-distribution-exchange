<?php

namespace App\Http\Controllers\Manage\CP\Exchange;

use App\Models\User;
use App\Models\Property;
use App\Models\PropertyCP;
use Illuminate\Http\Request;
use App\Models\LicenseNotification;
use App\Http\Controllers\Controller;

class NotificationSettingController extends Controller
{
    public function index(PropertyCP $property)
    {
        $users = User::whereHas('roles', function ($q) use ($property) {
            $q->where('property_id', $property->id);
            $q->orWhereRaw('(organization_id = '.$property->organization_id.' and property_id = '.Property::ID_FOR_ADMIN.')');
        })
            ->with(['roles' => function ($q) use ($property) {
                $q->where('property_id', $property->id);
                $q->orWhereRaw('(organization_id = '.$property->organization_id.' and property_id = '.Property::ID_FOR_ADMIN.')');
            }])
            ->with('licenseNotifications')
            ->paginate(10);

        return view('manage.cp.exchange.notification-settings.index', [
            'property' => $property,
            'property_id' => $property->id,
            'users' => $users,
        ]);
    }

    public function update(PropertyCP $property, User $user, Request $request)
    {
        $request->validate([
            'notification_status' => 'required|boolean',
        ]);
        $notification = LicenseNotification::firstOrNew([
            'property_id' => $property->id,
            'user_id' => $user->id,
        ]);
        $notification->status = $request->get('notification_status');
        $notification->save();

        return response()->json([
            'status' => 'success',
            'data' => $notification,
        ]);
    }
}
