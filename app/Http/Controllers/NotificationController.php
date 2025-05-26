<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;

class NotificationController extends Controller
{
    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        $notifications = $user->notifications;

        return NotificationResource::collection($notifications);
    }
}
