<?php

namespace App\Http\Controllers;

use App\Models\Primary_Skill;
use App\Models\Primary_Skill_User;
use App\Models\Resource_Plan;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $skills;
    public function index(Request $request)
    {
        // validation
        $this->validate($request, [
            // 'page'              => 'nullable|integer',
            // 'perPage'           => 'nullable|integer',
            'manage_by'         => 'required|string',
            'reporting_to'      => 'required|string',
            'year'              => 'required|date_format:Y',
            'month'             => 'required|date_format:m',
        ]);
        $query = Primary_Skill::query();
        if ($request->manage_by && $request->reporting_to) {
            $query->whereHas('users', function ($query) use ($request) {
                $query->where('manage_by', $request->manage_by)
                    ->where('reporting_to', $request->reporting_to);
            });
        }
        $this->skills = [];
        $query->with('users.resourcePlan', function ($query) use ($request) {
            $query->where('year', $request->year)
                ->where('month', $request->month);

            $fullTime  = 0;
            $partTime  = 0;
            $available = 0;
            foreach ($query->get() as $skill) {
                if ($skill->planned_hours > 120) {
                    $fullTime = $fullTime + 1;
                } elseif ($skill->planned_hours > 40 && $skill->planned_hours < 119) {
                    $partTime = $partTime + 1;
                } elseif ($skill->planned_hours > 0 && $skill->planned_hours < 39) {
                    $available = $available + 1;
                }
            }
            $totalCount = $fullTime + $partTime +  $available;
            $this->skills = ['fulltime' => $fullTime, 'parttime' => $partTime, 'available' => $available, 'total_count' => $totalCount];
        });
        // dd($this->skills);
        $data = $query->get();
        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page    = $request->page;
            $perPage = $request->perPage;
            $query   = $query->skip($perPage * ($page - 1))->take($perPage);
        }
        return response()->json([
            'count' => $count,
            'skill' => $this->skills,
            'data'  => $data
        ]);
    }
}
