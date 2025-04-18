<x-app-layout>
    <x-slot name="title">Subscription Plans</x-slot>
    
    <div class="container mx-auto px-4 py-12">
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-chewy text-accent mb-4 text-center">Pawsome Box Subscription</h1>
            <p class="text-xl text-gray-600 mb-12 text-center">Get premium pet supplies delivered to your door every month</p>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            
            <!-- Subscription Plans -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($plans as $plan)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden border {{ $plan->is_featured ? 'border-accent' : 'border-gray-200' }}">
                        @if($plan->is_featured)
                            <div class="bg-accent text-white text-center py-2 font-semibold">
                                Most Popular
                            </div>
                        @endif
                        
                        <div class="p-6">
                            <h2 class="text-2xl font-semibold mb-2">{{ $plan->name }}</h2>
                            <div class="text-3xl font-bold text-primary mb-4">
                                LKR {{ number_format($plan->price, 2) }}<span class="text-gray-500 text-base font-normal">/month</span>
                            </div>
                            
                            <p class="text-gray-600 mb-6">{{ $plan->description }}</p>
                            
                            <div class="space-y-3 mb-8">
                                @foreach($plan->features as $feature)
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span>{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if(Auth::check())
                                @if($activeSubscription && $activeSubscription->subscription_plan_id == $plan->id)
                                    <div class="text-center">
                                        <span class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-md">
                                            Currently Subscribed
                                        </span>
                                    </div>
                                @else
                                    <form action="{{ route('subscriptions.subscribe') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                        <button type="submit" class="w-full bg-accent text-white py-3 px-6 rounded-md hover:bg-accent-dark transition duration-300">
                                            Subscribe Now
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block text-center bg-blue-500 text-white py-3 px-6 rounded-md hover:bg-primary-dark transition duration-300">
                                    Login to Subscribe
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Subscription Benefits -->
            <div class="mt-16">
                <h2 class="text-3xl font-chewy text-accent mb-8 text-center">Why Subscribe to Pawsome Box?</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-accent bg-opacity-10 p-4 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-center">Save Money</h3>
                        <p class="text-gray-600 text-center">
                            Subscription boxes offer better value than buying products individually. Save up to 30% on premium pet supplies.
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-accent bg-opacity-10 p-4 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-center">Personalized Selection</h3>
                        <p class="text-gray-600 text-center">
                            Each box is tailored to your pet's needs, preferences, and size. Discover new products your pet will love.
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-accent bg-opacity-10 p-4 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-center">Convenient Delivery</h3>
                        <p class="text-gray-600 text-center">
                            Never run out of pet supplies again. Regular deliveries ensure you always have what your pet needs.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- How It Works -->
            <div class="mt-16">
                <h2 class="text-3xl font-chewy text-accent mb-8 text-center">How It Works</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="bg-accent text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">1</div>
                        <h3 class="text-xl font-semibold mb-2">Choose a Plan</h3>
                        <p class="text-gray-600">Select the subscription plan that best fits your pet's needs and your budget.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-accent text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">2</div>
                        <h3 class="text-xl font-semibold mb-2">Customize</h3>
                        <p class="text-gray-600">Tell us about your pet's preferences, allergies, and special needs.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-accent text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">3</div>
                        <h3 class="text-xl font-semibold mb-2">Receive</h3>
                        <p class="text-gray-600">Get your Pawsome Box delivered to your doorstep every month.</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="bg-accent text-white rounded-full w-12 h-12 flex items-center justify-center mx-auto mb-4 text-xl font-bold">4</div>
                        <h3 class="text-xl font-semibold mb-2">Enjoy</h3>
                        <p class="text-gray-600">Watch your pet enjoy their new toys, treats, and supplies!</p>
                    </div>
                </div>
            </div>
            
            <!-- Testimonials -->
            <div class="mt-16">
                <h2 class="text-3xl font-chewy text-accent mb-8 text-center">What Our Subscribers Say</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-semibold">Sarah Johnson</h4>
                                <div class="text-yellow-400">★★★★★</div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "My dog absolutely loves his monthly Pawsome Box! The quality of the toys and treats is exceptional, and it's so convenient to have them delivered right to our door."
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/men/54.jpg" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-semibold">Michael Lee</h4>
                                <div class="text-yellow-400">★★★★★</div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "The subscription has been a game-changer for my busy lifestyle. My cat gets premium products, and I save time and money. Win-win!"
                        </p>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                            <div>
                                <h4 class="font-semibold">Emily Rodriguez</h4>
                                <div class="text-yellow-400">★★★★★</div>
                            </div>
                        </div>
                        <p class="text-gray-600 italic">
                            "I've tried several pet subscription boxes, and Pawsome Box is by far the best. The products are always high-quality and perfectly suited to my pet's needs."
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- FAQ -->
            <div class="mt-16">
                <h2 class="text-3xl font-chewy text-accent mb-8 text-center">Frequently Asked Questions</h2>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" onclick="toggleFAQ(this)">
                            <span>What's included in a Pawsome Box?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-600">
                                Each Pawsome Box contains a carefully curated selection of 5-7 premium pet products, including toys, treats, grooming supplies, and accessories. The contents vary each month to provide variety and excitement for your pet.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" onclick="toggleFAQ(this)">
                            <span>Can I cancel my subscription anytime?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-600">
                                Yes, you can cancel your subscription at any time through your account dashboard. If you cancel before your next billing date, you won't be charged for the following month.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" onclick="toggleFAQ(this)">
                            <span>How is the box customized for my pet?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-600">
                                After subscribing, you'll complete a pet profile where you can specify your pet's size, age, breed, preferences, and any allergies or dietary restrictions. Our team uses this information to customize your box accordingly.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" onclick="toggleFAQ(this)">
                            <span>When will my box be delivered?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-600">
                                Boxes are shipped on the 5th of each month and typically arrive within 3-5 business days. You'll receive a tracking number via email when your box ships so you can monitor its progress.
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <button class="flex justify-between items-center w-full px-6 py-4 text-left font-semibold focus:outline-none" onclick="toggleFAQ(this)">
                            <span>What if my pet doesn't like something in the box?</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="px-6 py-4 border-t border-gray-200 hidden">
                            <p class="text-gray-600">
                                We have a satisfaction guarantee. If your pet doesn't like a particular item, please contact our customer service team, and we'll do our best to replace it with something more suitable in your next box.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA -->
            <div class="mt-16 text-center">
                <h2 class="text-3xl font-chewy text-accent mb-4">Ready to Make Your Pet Happy?</h2>
                <p class="text-xl text-gray-600 mb-8">Join thousands of pet owners who trust Pawsome Box for their pet's needs</p>
                
                <a href="#subscription-plans" class="inline-block bg-accent text-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-accent-dark transition duration-300">
                    Subscribe Now
                </a>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        function toggleFAQ(element) {
            const content = element.nextElementSibling;
            const arrow = element.querySelector('svg');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                arrow.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                arrow.classList.remove('rotate-180');
            }
        }
    </script>
    @endpush
</x-app-layout>

