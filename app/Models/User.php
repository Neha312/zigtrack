<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Http\Traits\Uuids;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Uuids;
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*User belongs to many primary skill*/
    public function skills()
    {
        return $this->belongsToMany(PrimarySkill::class, 'primary_skill_users', 'user_id', 'primary_skill_id');
    }
    /*User has many resource plan*/
    public function resourcePlan()
    {
        return $this->hasMany(ResourcePlan::class, 'user_id')->select('id', 'user_id', 'project_id', 'year', 'month', 'planned_hours');
    }
}
