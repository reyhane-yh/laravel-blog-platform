<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'password',

        'is_admin',
    ];

    protected $hidden = [
        'password',
        //'remember_token',
    ];

    protected function casts(): array
    {
        return [
            //'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts()
    {
        return $this->hasMany(
            Post::class,
            'author_id');
    }

    public static function findByUsername($username)
    {
        return self::where('username', $username)->first();
    }

    public static function createUser($data)
    {
        return self::create([
            'is_admin' => $data['is_admin'] ?? false,
            'username' => $data['username'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);}
}
