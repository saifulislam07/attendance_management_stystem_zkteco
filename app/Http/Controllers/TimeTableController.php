<?php

namespace App\Http\Controllers;

use App\Models\TimeTable;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $timetables = TimeTable::with('schoolClass')->orderBy('role')->orderBy('day')->get();
        return view('timetables.index', compact('timetables'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = SchoolClass::all();
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return view('timetables.create', compact('classes', 'days'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|in:teacher,student',
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'day' => 'required|string',
            'in_time' => 'required',
            'late_time' => 'required',
            'out_time' => 'required',
            'grace_time' => 'nullable|integer',
            'half_day_time' => 'nullable',
            'overtime_start' => 'nullable',
        ]);

        $data = $request->except('_token');
        
        if ($request->day === 'All Days') {
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            foreach ($days as $day) {
                $dayData = $data;
                $dayData['day'] = $day;
                
                // Update or Create to avoid duplicates for the same day
                TimeTable::updateOrCreate(
                    [
                        'role' => $data['role'],
                        'class_id' => $data['class_id'] ?? null,
                        'day' => $day
                    ],
                    $dayData
                );
            }
            return redirect()->route('timetables.index')->with('success', 'Timetable created for all days.');
        }

        TimeTable::create($data);

        return redirect()->route('timetables.index')->with('success', 'Timetable created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeTable $timetable)
    {
        $classes = SchoolClass::all();
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        return view('timetables.edit', compact('timetable', 'classes', 'days'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeTable $timetable)
    {
        $request->validate([
            'role' => 'required|in:teacher,student',
            'class_id' => 'nullable|exists:classes,id|required_if:role,student',
            'day' => 'required|string',
            'in_time' => 'required',
            'late_time' => 'required',
            'out_time' => 'required',
            'grace_time' => 'nullable|integer',
            'half_day_time' => 'nullable',
            'overtime_start' => 'nullable',
        ]);

        $timetable->update($request->all());

        return redirect()->route('timetables.index')->with('success', 'Timetable updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeTable $timetable)
    {
        $timetable->delete();
        return redirect()->route('timetables.index')->with('success', 'Timetable deleted successfully.');
    }
}
