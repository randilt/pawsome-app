<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        
        // Check if user has active subscription
        $activeSubscription = null;
        if (auth()->check()) {
            $activeSubscription = CustomerSubscription::where('user_id', auth()->id())
                ->active()
                ->first();
        }
        
        return view('subscriptions.index', compact('plans', 'activeSubscription'));
    }

    /**
     * Subscribe to a plan.
     */
    public function subscribe(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get plan details
            $plan = SubscriptionPlan::findOrFail($validated['plan_id']);
            
            // Check if user has active subscription
            $activeSubscription = CustomerSubscription::where('user_id', auth()->id())
                ->active()
                ->first();
                
            if ($activeSubscription) {
                throw new \Exception('You already have an active subscription.');
            }
            
            // Calculate dates
            $startDate = now();
            $endDate = now()->addMonths($plan->duration_months);
            
            // Create subscription
            $subscription = CustomerSubscription::create([
                'user_id' => auth()->id(),
                'plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
            ]);
            
            DB::commit();
            
            return redirect()->route('subscriptions.index')
                ->with('success', 'Successfully subscribed to ' . $plan->name);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel a subscription.
     */
    public function cancel($id)
    {
        $subscription = CustomerSubscription::where('id', $id)
            ->where('user_id', auth()->id())
            ->where('status', 'active')
            ->firstOrFail();
            
        $subscription->update([
            'status' => 'cancelled',
            'end_date' => now(),
        ]);
        
        return redirect()->route('profile.subscriptions')
            ->with('success', 'Subscription cancelled successfully.');
    }

    /**
     * Display user's subscriptions.
     */
    public function userSubscriptions()
    {
        $subscriptions = CustomerSubscription::with('plan')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('profile.subscriptions', compact('subscriptions'));
    }

    /**
     * Display a listing of all subscription plans (admin).
     */
    public function adminIndex()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        
        return view('admin.subscriptions.plans', compact('plans'));
    }

    /**
     * Store a newly created subscription plan in storage (admin).
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
        ]);
        
        SubscriptionPlan::create($validated);
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Update the specified subscription plan in storage (admin).
     */
    public function updatePlan(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_months' => 'required|integer|min:1',
        ]);
        
        // Check if plan has active subscriptions
        $activeSubscriptions = CustomerSubscription::where('plan_id', $id)
            ->active()
            ->count();
            
        if ($activeSubscriptions > 0) {
            return redirect()->route('admin.subscriptions.plans')
                ->with('error', 'Cannot modify plan with active subscriptions.');
        }
        
        $plan->update($validated);
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove the specified subscription plan from storage (admin).
     */
    public function destroyPlan($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Check if plan has active subscriptions
        $activeSubscriptions = CustomerSubscription::where('plan_id', $id)
            ->active()
            ->count();
            
        if ($activeSubscriptions > 0) {
            return redirect()->route('admin.subscriptions.plans')
                ->with('error', 'Cannot delete plan with active subscriptions.');
        }
        
        $plan->delete();
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Display a listing of all active subscriptions (admin).
     */
    public function adminSubscriptions()
    {
        $subscriptions = CustomerSubscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}

