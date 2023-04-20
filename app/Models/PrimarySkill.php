<?php

namespace App\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class PrimarySkill extends Model
{
    use Uuids;
    protected $table = 'primary_skills';

    /* Primary skill belongs to many user */
    public function users()
    {
        return $this->belongsToMany(User::class, 'primary_skill_users', 'primary_skill_id', 'user_id')->select('id', 'name', 'manage_by', 'reporting_to');
    }
}
