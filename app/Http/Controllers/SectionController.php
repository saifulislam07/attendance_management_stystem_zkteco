<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SchoolClass;
use App\Support\TablePerPage;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Section::with('schoolClass');

        if ($request->filled('q')) {
            $search = trim($request->q);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('schoolClass', fn ($classQuery) => $classQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $sections = $query->paginate(TablePerPage::resolve($request));
        return view('sections.index', compact('sections'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        return view('sections.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
        ]);

        Section::create($request->all());

        return redirect()->route('sections.index')->with('success', 'Section created successfully.');
    }

    public function edit(Section $section)
    {
        $classes = SchoolClass::all();
        return view('sections.edit', compact('section', 'classes'));
    }

    public function update(Request $request, Section $section)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
        ]);

        $section->update($request->all());

        return redirect()->route('sections.index')->with('success', 'Section updated successfully.');
    }

    /**
     * Batch delete sections.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['status' => 'error', 'message' => 'No sections selected.']);
        }
        Section::whereIn('id', $ids)->delete();
        return response()->json(['status' => 'success', 'message' => count($ids) . ' sections deleted.']);
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('sections.index')->with('success', 'Section deleted successfully.');
    }
}
