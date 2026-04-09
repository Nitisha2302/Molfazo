<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoPlan;
use Illuminate\Http\Request;

class VideoPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoPlan::query();

        if ($request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $plans = $query->latest()->paginate(10);

        return view('admin.video_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.video_plans.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                // 'video_count' => 'required|integer|min:1',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:1',
            ],
            [
                'title.required' => 'Title is required',
                'title.max' => 'Title cannot exceed 255 characters',

                // 'video_count.required' => 'Video count is required',
                // 'video_count.integer' => 'Video count must be a number',
                // 'video_count.min' => 'Minimum 1 video required',

                'duration.required' => 'Duration is required',
                'duration.integer' => 'Duration must be in days',
                'duration.min' => 'Minimum duration is 1 day',

                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'price.min' => 'Price must be greater than 0',
            ]
        );

        VideoPlan::create([
            'name' => $request->title,
            // 'video_count' => $request->video_count,
            'duration_days' => $request->duration,
            'price' => $request->price,
        ]);

        return redirect()->route('dashboard.admin.video-plans.index')
            ->with('success','Video Plan Added Successfully');
    }

    public function edit($id)
    {
        $plan = VideoPlan::findOrFail($id);
        return view('admin.video_plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'title' => 'required|string|max:255',
                // 'video_count' => 'required|integer|min:1',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:1',
            ],
            [
                'title.required' => 'Title is required',
                'title.max' => 'Title cannot exceed 255 characters',

                // 'video_count.required' => 'Video count is required',
                // 'video_count.integer' => 'Video count must be a number',
                // 'video_count.min' => 'Minimum 1 video required',

                'duration.required' => 'Duration is required',
                'duration.integer' => 'Duration must be in days',
                'duration.min' => 'Minimum duration is 1 day',

                'price.required' => 'Price is required',
                'price.numeric' => 'Price must be a valid number',
                'price.min' => 'Price must be greater than 0',
            ]
        );

        $plan = VideoPlan::findOrFail($id);

        $plan->update([
            'title' => $request->title,
            // 'video_count' => $request->video_count,
            'duration_days' => $request->duration,
            'price' => $request->price,
        ]);

        return redirect()->route('dashboard.admin.video-plans.index')
            ->with('success','Video Plan Updated Successfully');
    }

    public function destroy($id)
    {
        VideoPlan::findOrFail($id)->delete();

        return back()->with('success','Deleted Successfully');
    }
}