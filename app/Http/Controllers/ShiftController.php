<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('id', 'desc')->get();
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'break_duration' => 'nullable|integer',
            'status'         => 'required|boolean',
            'description'    => 'nullable|string',
        ]);

        Shift::create($validated);
        return redirect()->route('shifts.index')->with('success', 'تم إضافة الشفت بنجاح');
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'start_time'     => 'required',
            'end_time'       => 'required',
            'break_duration' => 'nullable|integer',
            'status'         => 'required|boolean',
            'description'    => 'nullable|string',
        ]);

        $shift->update($validated);
        return redirect()->route('shifts.index')->with('success', 'تم تحديث الشفت بنجاح');
    }
     public function destroy(Shift $shift)
    {        $shift->delete();
        return redirect()->route('shifts.index')->with('success', 'تم حذف الشفت بنجاح');
    }
}