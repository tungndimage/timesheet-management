<?php

namespace App\Http\Controllers;

use App\Models\Timesheet;
use App\Http\Requests\StoreTimesheetRequest;
use App\Http\Requests\UpdateTimesheetRequest;
use App\Models\Task;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    public function create($id)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTimesheetRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTimesheetRequest $request)
    {
        $params = $request->only('tasks', 'difficulties', 'todo', 'date');
        $user = Auth::user();
        if (!Auth::user()->can('create')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $timesheet = Timesheet::create(['difficulties' => $params['difficulties'], 'todo' => $params['todo'], 'user_id' => $user->id, 'date' => $params['date']]);

            $tasks = [];
            foreach ($params['tasks'] as $item) {
                if (isset($item['title']) || isset($item['content']) || isset($item['hours_used'])) {
                    $item['user_id'] = $user->id;
                    $item['timesheet_id'] = $timesheet->id;
                    $tasks[] = $item;
                }
            }
            count($tasks) && Task::insert($tasks);
            DB::commit();
            return $this->successResponse('Timesheets created', 'Success');
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
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
    public function update(UpdateTimesheetRequest $request, int $id)
    {
        $user = Auth::user();
        $timesheet = Timesheet::findOrFail($id);
        if (!$user->can('update', $timesheet)) {
            abort(403);
        }
        $params = $request->only('tasks', 'difficulties', 'todo', 'date');

        DB::beginTransaction();
        try {
            $timesheet->difficulties = $params['difficulties'];
            $timesheet->todo = $params['todo'];
            $timesheet->save();

            Task::where('timesheet_id', $timesheet->id)->delete();

            $tasks = [];
            foreach ($params['tasks'] as $item) {
                if (isset($item['title']) || isset($item['content']) || isset($item['hours_used'])) {
                    $item['user_id'] = $user->id;
                    $item['timesheet_id'] = $timesheet->id;
                    $tasks[] = $item;
                }
            }
            count($tasks) && Task::insert($tasks);
            DB::commit();
            return $this->successResponse('Timesheets updated', 'Success');
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception($e->getMessage());
        }
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
