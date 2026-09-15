<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiPhotoPlan;
use Illuminate\Http\Request;

class AiPhotoPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = AiPhotoPlan::query();

        if ($request->search) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $plans = $query->orderBy('credits')->paginate(10);

        return view('admin.ai_photo_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.ai_photo_plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name'    => 'required|string|max:255',
                'credits' => 'required|integer|min:1|max:10000',
                'price'   => 'required|numeric|min:1|max:1000000',
            ],
            [
                'name.required'    => 'Plan name is required',
                'credits.required' => 'Credits are required',
                'credits.integer'  => 'Credits must be a whole number',
                'credits.min'      => 'Plan must give at least 1 credit',
                'price.required'   => 'Price is required',
                'price.numeric'    => 'Price must be a valid number',
                'price.min'        => 'Price must be greater than 0',
            ]
        );

        AiPhotoPlan::create([
            'name'    => $data['name'],
            'credits' => $data['credits'],
            'price'   => $data['price'],

            // filled automatically — admin never sees these
            'currency'   => config('services.stripe.currency', 'usd'),
            'is_active'  => true,
            'sort_order' => (int) (AiPhotoPlan::max('sort_order') ?? 0) + 1,
        ]);

        return redirect()->route('dashboard.admin.ai-photo-plans.index')
            ->with('success', 'AI Photo Plan Added Successfully');
    }

    public function edit($id)
    {
        $plan = AiPhotoPlan::findOrFail($id);

        return view('admin.ai_photo_plans.edit', compact('plan'));
    }

    public function update(Request $request, $id)
    {
        $plan = AiPhotoPlan::findOrFail($id);

        $data = $request->validate(
            [
                'name'    => 'required|string|max:255',
                'credits' => 'required|integer|min:1|max:10000',
                'price'   => 'required|numeric|min:1|max:1000000',
            ],
            [
                'name.required'    => 'Plan name is required',
                'credits.required' => 'Credits are required',
                'credits.min'      => 'Plan must give at least 1 credit',
                'price.required'   => 'Price is required',
                'price.min'        => 'Price must be greater than 0',
            ]
        );

        // only these three change — currency / is_active / sort_order untouched
        $plan->update([
            'name'    => $data['name'],
            'credits' => $data['credits'],
            'price'   => $data['price'],
        ]);

        return redirect()->route('dashboard.admin.ai-photo-plans.index')
            ->with('success', 'AI Photo Plan Updated Successfully');
    }

    /**
     * Show / hide a plan in the seller app without deleting it.
     * Replaces the "Status" checkbox that used to be on the form.
     */
    public function toggle($id)
    {
        $plan = AiPhotoPlan::findOrFail($id);

        $plan->update(['is_active' => ! $plan->is_active]);

        return back()->with(
            'success',
            $plan->is_active
                ? 'Plan is now visible to sellers'
                : 'Plan is now hidden from sellers'
        );
    }

    public function destroy($id)
    {
        $plan = AiPhotoPlan::findOrFail($id);

        if ($plan->orders()->where('status', 'paid')->exists()) {
            $plan->update(['is_active' => false]);

            return back()->with('success', 'This plan has purchases, so it was hidden instead of deleted.');
        }

        $plan->delete();

        return back()->with('success', 'Deleted Successfully');
    }
}