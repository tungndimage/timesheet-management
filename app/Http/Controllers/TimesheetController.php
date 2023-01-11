<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TimesheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $timesheets = Timesheet::with('tasks')->where('user_id', $user->id)->get()->toArray();

        return view('list', compact('timesheets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTimesheetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimesheetRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $timesheet = Timesheet::findOrFail($id);
        if (!Auth::user()->can('view', $timesheet)) {
            abort(403);
        }

        return view('timesheet', compact('timesheet'));
    }

    public function showByDate(Request $request) {
        $params = $request->only('date');
        $timesheet = Timesheet::whereDate('date', $params['date'])->where('user_id', Auth::user()->id)->first();
        if (!$timesheet) {
            return $this->successResponse([], 'Not found');
        }
        return $this->successResponse($timesheet->toArray(), 'Success');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Timesheet $timesheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTimesheetRequest  $request
     * @param  \App\Models\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTimesheetRequest $request, Timesheet $timesheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Timesheet  $timesheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Timesheet $timesheet)
    {
        //
    }
}
