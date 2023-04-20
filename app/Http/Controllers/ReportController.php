<?php

namespace App\Http\Controllers;

use App\Models\PrimarySkill;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function skillReport(Request $request)
    {
        // validation
        $this->validate($request, [
            'manage_by'         => 'required|string|exists:users,manage_by',
            'reporting_to'      => 'required|string|exists:users,reporting_to',
            'year'              => 'required|date_format:Y|exists:resource_plans,year',
            'month'             => 'required|date_format:m|exists:resource_plans,month',
        ]);
        $primarySkills = PrimarySkill::query();
        if ($request->manage_by && $request->reporting_to) {
            $skill = $primarySkills->whereHas('users', function ($query) use ($request) {
                $query->where('manage_by', $request->manage_by)
                    ->where('reporting_to', $request->reporting_to);
            });
        }
        $skills = $skill->get();
        $totalAvailable = 0;
        $totalfullTime  = 0;
        $totalpartTime  = 0;
        foreach ($skills as $key => $skill) {
            $users = $skill->users()->get();
            $fullTime  = 0;
            $partTime  = 0;
            $available = 0;
            foreach ($users as $user) {
                $resources = $user->resourcePlan()
                    ->where('year', $request->year)
                    ->where('month', $request->month)->sum('planned_hours');
                if ($resources > 120) {
                    $fullTime = $fullTime + 1;
                    $totalfullTime = $totalfullTime + 1;
                } elseif ($resources > 40 && $resources < 119) {
                    $partTime = $partTime + 1;
                    $totalpartTime = $totalpartTime + 1;
                } elseif ($resources > 0 && $resources < 39) {
                    $available = $available + 1;
                    $totalAvailable = $totalAvailable + 1;
                }
                //total count
                $totalCount = $fullTime + $partTime +  $available;
                $totalSkillCount = $totalfullTime + $totalpartTime +  $totalAvailable;

                $skills[$key]['skill_count'] = [
                    'full_time'     => $fullTime,
                    'part_time'     => $partTime,
                    'available'     => $available,
                    'total_count'   => $totalCount
                ];
            }
        }

        /* Count */
        $count = $primarySkills->count();
        return response()->json([
            'count'             => $count,
            'skills'            => $skills,
            'total_full_time'   => $totalfullTime,
            'total_part_time'   => $totalpartTime,
            'total_available'   => $totalAvailable,
            'total_skill_count' => $totalSkillCount,
        ]);
    }
}
