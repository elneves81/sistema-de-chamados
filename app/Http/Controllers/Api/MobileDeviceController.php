<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;

class MobileDeviceController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'platform' => 'nullable|string|in:android,ios,web',
            'token' => 'required|string|max:4096',
            'device_info' => 'nullable|array',
        ]);

        $user = $request->user();

        $record = DeviceToken::updateOrCreate(
            ['user_id' => $user->id, 'token' => $data['token']],
            [
                'platform' => $data['platform'] ?? null,
                'device_info' => $data['device_info'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->json(['success' => true, 'device' => $record], 201);
    }
}
