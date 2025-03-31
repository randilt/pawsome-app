<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        // Get featured products for the home page
        $featuredProducts = Product::active()
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();
            
        return view('home', compact('featuredProducts'));
    }

    /**
     * Display the about page.
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Process the contact form submission.
     */
    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        // In a real application, you would send an email or store the contact message
        
        return back()->with('success', 'Your message has been sent successfully!');
    }
}

