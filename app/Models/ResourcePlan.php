<?php

namespace App\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;

class ResourcePlan extends Model
{
    use  Uuids;
    protected $table = 'resource_plans';

    protected $fillable = [
        'id', 'user_id', 'project_id', 'year', 'month', 'planned_hours', 'assignment', 'expected_assignmnet_start_date', 'expected_assignmnet_end_date'
    ];
    /*Resource plan belong to user*/
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
