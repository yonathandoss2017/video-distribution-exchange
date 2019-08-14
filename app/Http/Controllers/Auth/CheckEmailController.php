<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ApiController;

class CheckEmailController extends ApiController
{
    public function action(Request $request)
    {
        $email = $request->get('email');

        $user = (new User())->findUserByEmail($email);

        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        }

        return response()->json([
            'status' => false,
        ]);
    }
}
