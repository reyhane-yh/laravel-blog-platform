<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikeRequest;
use App\Http\Resources\UserResource;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{
    public function toggleLike(LikeRequest $request, $type, $id)
    {
        // Get the validated data
        $validated = $request->validated();

        // Get the authenticated user
        $user = Auth::user();

        // Get the likeable model
        $likeable = $this->getLikeableModel($validated['type'], $validated['id']);

        if (!$likeable) {
            return response()->json([
                'message' => 'Invalid likeable type.'],
                400);
        }

        $status = Like::toggleLike($user, $likeable);

        $message = ucfirst($validated['type']) . " " . $status . " successfully.";

        return response()->json([
            'message' => $message
        ]);
    }


    public function usersList(LikeRequest $request)
    {
        // Get the validated data
        $validated = $request->validated();

        $likeable = $this->getLikeableModel($validated['type'], $validated['id']);

        if (!$likeable) {
            return response()->json([
                'message' => 'Invalid likeable type.'],
                400);
        }

        $users = Like::usersList($likeable);

        log::info($users);

        return UserResource::collection($users);
    }

    private function getLikeableModel($type, $id)
    {
        return match ($type) {
            'post' => Post::find($id),
            default => null,
        };
    }
}
