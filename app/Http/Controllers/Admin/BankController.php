<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank;

class BankController extends Controller
{
    // List
    public function index(Request $request)
    {
        $query = Bank::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $banks = $query->latest()->paginate(10)->withQueryString();

        return view('admin.banks.index', compact('banks'));
    }

    // Create
    public function create()
    {
        return view('admin.banks.create');
    }

    // Store
    public function store(Request $request)
    {
        // $request->validate([
        //     'name'   => 'required|string|max:255',
        //     'logo'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        //     'status' => 'required|in:0,1',
        // ]);

        $request->validate([
            'name'   => 'required|string|max:255',
            'logo'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'status' => 'required|in:0,1',
        ], [
            // 🔥 Name
            'name.required' => 'Bank name is required',
            'name.string' => 'Bank name must be valid text',
            'name.max' => 'Bank name must not exceed 255 characters',

            // 🔥 Logo
            'logo.image' => 'Logo must be an image',
            'logo.mimes' => 'Logo must be jpeg, png, jpg or webp',
            'logo.max' => 'Logo size must be less than 1MB',

            // 🔥 Status
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        $fileName = null;

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/bank_images'), $fileName);
        }

        Bank::create([
            'name'   => $request->name,
            'logo'   => $fileName,
            'status' => $request->status,
        ]);

        return redirect()->route('dashboard.admin.banks.index')
                         ->with('success', 'Bank created successfully.');
    }

    // Edit
    public function edit(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    // Update
    public function update(Request $request, Bank $bank)
    {
        // $request->validate([
        //     'name'   => 'required|string|max:255',
        //     'logo'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        //     'status' => 'required|in:0,1',
        // ]);

        $request->validate([
            'name'   => 'required|string|max:255',
            'logo'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'status' => 'required|in:0,1',
        ], [
            // 🔥 Name
            'name.required' => 'Bank name is required',
            'name.string' => 'Bank name must be valid text',
            'name.max' => 'Bank name must not exceed 255 characters',

            // 🔥 Logo (optional in update)
            'logo.image' => 'Logo must be an image',
            'logo.mimes' => 'Logo must be jpeg, png, jpg or webp',
            'logo.max' => 'Logo size must be less than 1MB',

            // 🔥 Status
            'status.required' => 'Status is required',
            'status.in' => 'Invalid status selected',
        ]);

        if ($request->hasFile('logo')) {

            if ($bank->logo && file_exists(public_path('assets/bank_images/'.$bank->logo))) {
                unlink(public_path('assets/bank_images/'.$bank->logo));
            }

            $file = $request->file('logo');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/bank_images'), $fileName);
            $bank->logo = $fileName;
        }

        $bank->name   = $request->name;
        $bank->status = $request->status;
        $bank->save();

        return redirect()->route('dashboard.admin.banks.index')
                         ->with('success', 'Bank updated successfully.');
    }

    // Delete
    public function destroy(Bank $bank)
    {
        if ($bank->logo && file_exists(public_path('assets/bank_images/'.$bank->logo))) {
            unlink(public_path('assets/bank_images/'.$bank->logo));
        }

        $bank->delete();

        return redirect()->route('dashboard.admin.banks.index')
                         ->with('success', 'Bank deleted successfully.');
    }
}