<?php

namespace App\Http\Controllers;

use App\Models\Primary_Skill;
use App\Models\Primary_Skill_User;
use App\Models\Resource_Plan;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // validation
        $this->validate($request, [
            'page'              => 'nullable|integer',
            'perPage'           => 'nullable|integer',
            'manage_by'         => 'required|string',
            'reporting_to'      => 'required|string',
            'year'              => 'required|date_format:Y',
            'month'             => 'required|date_format:m',
        ]);
        $query = User::query()->with('skills:name');
        if ($request->year && $request->month) {
            $query->whereHas('resourcePlan', function ($query) use ($request) {
                $query->where('year', $request->year)
                    ->where('month', $request->month);
            });
            if ($request->manage_by && $request->reporting_to) {
                $query->where('manage_by', $request->manage_by)
                    ->where('reporting_to', $request->reporting_to);
            }
        }
        /* Pagination */
        $count = $query->count();
        if ($request->page && $request->perPage) {
            $page    = $request->page;
            $perPage = $request->perPage;
            $query   = $query->skip($perPage * ($page - 1))->take($perPage);
        }
        $data = $query->get();
        return response()->json([
            'count' => $count,
            'data' => $data
        ]);
    }
}
