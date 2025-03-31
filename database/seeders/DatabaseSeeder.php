<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        Admin::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('Admin123!'),
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'address' => '123 Main St, Anytown, USA',
            'phone' => '1234567890',
        ]);

        // Create categories
        $categories = [
            ['name' => 'Dog Food', 'description' => 'High-quality nutrition for dogs'],
            ['name' => 'Cat Food', 'description' => 'Nutritious meals for cats'],
            ['name' => 'Pet Toys', 'description' => 'Fun and engaging toys for pets'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create products
        $products = [
            [
                'category_id' => 1,
                'name' => 'Deluxe Dog Treats',
                'description' => 'Tasty dog treats for training and rewarding.',
                'long_description' => 'Deluxe Dog Treats are crafted with care to ensure your furry friend enjoys a nutritious and delicious snack. These premium dog treats are made with high-quality, all-natural ingredients to promote health and happiness in dogs of all breeds and sizes.',
                'price' => 1500.00,
                'stock_quantity' => 150,
                'image_url' => 'https://www.acozykitchen.com/wp-content/uploads/2024/02/dog_treats_12-500x500.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 2,
                'name' => 'Premium Cat Food',
                'description' => 'High-quality cat food for your feline friends.',
                'long_description' => 'Premium Cat Food is specially formulated to provide complete and balanced nutrition for your cat. Made with real meat as the first ingredient, this food supports healthy muscles, energy, and overall well-being.',
                'price' => 800.00,
                'stock_quantity' => 100,
                'image_url' => 'https://m.media-amazon.com/images/I/71pjz7sTLrL._AC_SL1500_.jpg',
                'status' => 'active',
            ],
            [
                'category_id' => 3,
                'name' => 'Interactive Dog Toy',
                'description' => 'Durable interactive pet toy designed to keep your dog engaged.',
                'long_description' => 'This Interactive Dog Toy is designed to provide hours of entertainment and mental stimulation for your furry friend.',
                'price' => 1200.00,
                'stock_quantity' => 75,
                'image_url' => 'https://image.chewy.com/is/image/catalog/68074_MAIN._AC_SS1800_V1628101907_.jpg',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create subscription plans
        $subscriptionPlans = [
            [
                'name' => 'Basic Paw',
                'description' => "2-3 Toys\n1-2 Bags of Treats\n1 Chew Item",
                'price' => 3499.99,
                'duration_months' => 12,
            ],
            [
                'name' => 'Premium Paw',
                'description' => "4-5 Toys\r\n2-3 Bags of Premium Treats\r\n2 Chew Items\r\n1 Accessory (collar, bandana, etc.)",
                'price' => 7499.99,
                'duration_months' => 12,
            ],
            [
                'name' => 'Deluxe Paw',
                'description' => "6-7 Premium Toys\r\n3-4 Bags of Gourmet Treats\r\n3 Long-lasting Chews\r\n2 Accessories\r\n1 Surprise Luxury Item",
                'price' => 14999.99,
                'duration_months' => 12,
            ],
        ];

        foreach ($subscriptionPlans as $plan) {
            SubscriptionPlan::create($plan);
        }
    }
}

