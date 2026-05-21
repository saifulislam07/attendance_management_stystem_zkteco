<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use App\Support\TablePerPage;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = Leave::with('user')->orderBy('start_date', 'desc');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('type', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('start_date', 'like', "%{$search}%")
                    ->orWhere('end_date', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $leaves = $query->paginate(TablePerPage::resolve($request));
        return view('leaves.index', compact('leaves'));
    }

    public function create()
    {
        $users = User::all();
        return view('leaves.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string',
        ]);

        Leave::create($request->all());

        return redirect()->route('leaves.index')->with('success', 'Leave application submitted.');
    }

    public function edit(Leave $leave)
    {
        $users = User::all();
        return view('leaves.edit', compact('leave', 'users'));
    }

    public function update(Request $request, Leave $leave)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $leave->update($request->only('status'));

        return redirect()->route('leaves.index')->with('success', 'Leave status updated.');
    }

    public function destroy(Leave $leave)
    {
        $leave->delete();
        return redirect()->route('leaves.index')->with('success', 'Leave application deleted.');
    }
}
