<?php

namespace App\Http\Controllers;

use App\Models\CustomerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)
            ->orderBy('price')
            ->get();
        
        // Check if user has active subscription
        $activeSubscription = null;
        if (Auth::check()) {
            $activeSubscription = CustomerSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>', now())
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
            $activeSubscription = CustomerSubscription::where('user_id', Auth::id())
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->first();
                
            if ($activeSubscription) {
                throw new \Exception('You already have an active subscription.');
            }
            
            // Calculate dates
            $startDate = now();
            $endDate = now()->addDays($plan->duration_in_days);
            
            // Create subscription
            $subscription = CustomerSubscription::create([
                'user_id' => Auth::id(),
                'subscription_plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'payment_method' => 'credit_card', // Default payment method
                'payment_status' => 'completed', // Default payment status
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
            ->where('user_id', Auth::id())
            ->where('status', 'active')
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
        $subscriptions = CustomerSubscription::with('subscriptionPlan')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('profile.subscriptions', compact('subscriptions'));
    }

    /**
     * Display a listing of subscription plans for admin.
     */
    public function adminIndex()
    {
        $plans = SubscriptionPlan::orderBy('price')->get();
        
        return view('admin.subscriptions.plans.index', compact('plans'));
    }

    /**
     * Store a new subscription plan.
     */
    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        // Convert features from array to JSON if needed
        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = array_filter($validated['features']); // Remove empty values
        }
        
        $plan = SubscriptionPlan::create($validated);
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan created successfully.');
    }

    /**
     * Update an existing subscription plan.
     */
    public function updatePlan(Request $request, $id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_in_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);
        
        // Set is_active to false if not present in request
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }
        
        // Convert features from array to JSON if needed
        if (isset($validated['features']) && is_array($validated['features'])) {
            $validated['features'] = array_filter($validated['features']); // Remove empty values
        }
        
        $plan->update($validated);
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan updated successfully.');
    }

    /**
     * Remove a subscription plan.
     */
    public function destroyPlan($id)
    {
        $plan = SubscriptionPlan::findOrFail($id);
        
        // Check if plan has any subscriptions
        if ($plan->customerSubscriptions()->count() > 0) {
            return redirect()->route('admin.subscriptions.plans')
                ->with('error', 'Cannot delete a plan with active subscriptions.');
        }
        
        $plan->delete();
        
        return redirect()->route('admin.subscriptions.plans')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    /**
     * Display a listing of customer subscriptions for admin.
     */
    public function adminSubscriptions()
    {
        $subscriptions = CustomerSubscription::with(['user', 'subscriptionPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}

