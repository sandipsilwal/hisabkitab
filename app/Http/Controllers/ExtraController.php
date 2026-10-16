<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extra;

class ExtraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $extras = Extra::orderBy('display_order', 'asc')->orderBy('id', 'asc')->get();
        return view('skatepark.extras.index', compact('extras'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_default'] = $request->has('is_default');

        if (!isset($data['display_order']) || $data['display_order'] === null || $data['display_order'] === '') {
            $data['display_order'] = (Extra::max('display_order') ?? 0) + 1;
        }

        if ($data['is_default']) {
            Extra::where('id', '>', 0)->update(['is_default' => false]);
        }

        Extra::create($data);
        return redirect()->route('extras.index')->with('success', 'Extra created successfully.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Extra $extra)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'display_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['is_default'] = $request->has('is_default');

        if (!isset($data['display_order']) || $data['display_order'] === null || $data['display_order'] === '') {
            $data['display_order'] = 0;
        }

        if ($data['is_default']) {
            Extra::where('id', '!=', $extra->id)->update(['is_default' => false]);
        }

        $extra->update($data);
        return redirect()->route('extras.index')->with('success', 'Extra updated successfully.');
    }

    public function destroy(Extra $extra)
    {
        $extra->delete();
        return redirect()->route('extras.index')->with('success', 'Extra deleted successfully.');
    }
}
