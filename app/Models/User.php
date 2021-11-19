<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    const PARENT = 'parent', DRIVER = 'driver', ADMIN = 'admin';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'phone_number',
        'password',
        'user_type',
        'latitude',
        'longitude',
        'permanent_latitude',
        'permanent_longitude',
        'student_name',
        'driver_id',
        'arriving_time',
        'is_dismissal',
        'dismissal_note',
        'standard',
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /***
     * @return mixed
     */
    public static function getParents()
    {
        return User::where('user_type', self::PARENT)->with('driver')->get();
    }

    /***
     * @return mixed
     */
    public static function getDrivers()
    {
        return User::where('user_type', self::DRIVER)->get();
    }

    /***
     * @param $id
     * @return mixed
     */
    public static function getById($id)
    {
        return User::findOrFail($id);
    }

    /***
     * @param $id
     * @param $params
     * @return mixed
     */
    public static function edit($parentId, $params)
    {
        $user = User::getById($parentId);
        $user->update($params);
        return $user;
    }

    public static function getDriverDetail($id)
    {
        return User::where('id', $id)->with('driver')->get();
    }

    /***
     * @param $id
     */
    public static function deleteUser($id)
    {
        User::where('id', '=', $id)->delete();
    }

    /***
     * @return BelongsTo
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id', 'id');
    }


    /***
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /***
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
