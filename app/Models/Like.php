<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type',
        'created_at',
    ];

    public $timestamps = false;

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function existingLike(User $user, $likeable)
    {
        return $likeable->likes()
            ->where('user_id', $user->id)->first();
    }

    public static function addLike(User $user, $likeable)
    {
        return $likeable->likes()
            ->create(['user_id' => $user->id, 'created_at' => now()]);
    }

    public static function toggleLike(User $user, $likeable)
    {
        $existingLike = self::existingLike($user, $likeable);

        if ($existingLike) {
            $existingLike->delete();
            return 'unliked';

        } else {
            self::addLike($user, $likeable);
            return 'Liked';
        }
    }
    public static function usersList($likeable)
    {
        return $likeable->likes()
            ->with('user')->get()->pluck('user');
    }
}
