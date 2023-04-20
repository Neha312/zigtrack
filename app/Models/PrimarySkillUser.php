<?php

namespace App\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class PrimarySkillUser extends Model
{
    use Uuids;
    protected $table = 'primary_skill_users';
}
