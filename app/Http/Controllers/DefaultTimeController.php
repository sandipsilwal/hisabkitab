<?php

namespace App\Http\Controllers;

use App\Models\DefaultTime;
use Illuminate\Http\Request;

class DefaultTimeController extends Controller
{
    public function index()
    {
        $defaultTimes = DefaultTime::orderBy('display_order')->get();
        return view('skatepark.default_times.index', compact('defaultTimes'));
    }

    public function create()
    {
        return view('skatepark.default_times.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'minutes' => 'required|integer|min:1',
            'display_order' => 'required|integer',
        ]);

        DefaultTime::create($request->only(['label', 'minutes', 'display_order']));

        return redirect()->route('default_times.index')->with('success', 'Default Time created successfully.');
    }

    public function edit(DefaultTime $defaultTime)
    {
        return view('skatepark.default_times.edit', compact('defaultTime'));
    }

    public function update(Request $request, DefaultTime $defaultTime)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'minutes' => 'required|integer|min:1',
            'display_order' => 'required|integer',
        ]);

        $defaultTime->update($request->only(['label', 'minutes', 'display_order']));

        return redirect()->route('default_times.index')->with('success', 'Default Time updated successfully.');
    }

    public function destroy(DefaultTime $defaultTime)
    {
        if ($defaultTime->rates()->exists()) {
            return back()->with('error', 'Cannot delete Duration because it has associated rates.');
        }
        $defaultTime->delete();
        return redirect()->route('default_times.index')->with('success', 'Default Time deleted successfully.');
    }
}
