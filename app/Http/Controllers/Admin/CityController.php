<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller
{
    // List cities
    public function index(Request $request)
    {
        $query = City::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->latest()->paginate(10)->withQueryString();

        return view('admin.cities.index', compact('cities'));
    }

    // Show create form
    public function create()
    {
        return view('admin.cities.create');
    }

    // Store city
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name',
            // 'status' => 'required|in:0,1',
        ]);

        City::create([
            'name' => $request->name,
            // 'status' => $request->status
        ]);

        return redirect()->route('dashboard.admin.cities.index')
                         ->with('success', 'City created successfully.');
    }

    // Show edit form
    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    // Update city
    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:cities,name,' . $city->id,
            // 'status' => 'required|in:0,1',
        ]);

        $city->update([
            'name' => $request->name,
            // 'status' => $request->status
        ]);

        return redirect()->route('dashboard.admin.cities.index')
                         ->with('success', 'City updated successfully.');
    }

    // Delete city
    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('dashboard.admin.cities.index')
                        ->with('success', 'City deleted successfully.');
    }
}