<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'phone' => '+1 493 1234567',
            'password' => bcrypt('12345678'),
        ]);
        DB::table('users')->insert([
            'name' => 'Guest',
            'email' => 'guest@gmail.com',
            'role_id' => 2,
            'phone' => '+1 493 1234567',
            'password' => bcrypt('12345678'),
        ]);
        DB::table('roles')->insert([
            'name' => 'admin',
            'user_id' => 1,
            'permissions' => json_encode(array_keys(Config::get('auth.permissions')))
        ]);
        DB::table('roles')->insert([
            'name' => 'guest',
            'user_id' => 1,
            'permissions' => json_encode(['posts.view', 'categories.view'])
        ]);

        $postCategories = [
            "Technology Trends",
            "Travel Adventures",
            "Health and Wellness",
            "Science Discoveries",
            "Book Reviews",
            "Movie Recommendations",
            "DIY Projects",
            "Cooking and Recipes",
            "Fashion Inspiration",
            "Fitness Tips",
            "Pet Care",
            "Gardening Ideas",
            "Personal Development",
            "Business Success Stories",
            "Environmental Conservation",
            "Parenting Advice",
            "Home Decor",
            "Financial Planning",
            "Art and Creativity",
            "Music Reviews",
            "Career Insights",
            "Mindfulness and Meditation",
            "Historical Discoveries",
            "Gaming Strategies",
            "Product Reviews",
            "Mental Health Awareness",
            "Sustainable Living",
            "Inspirational Stories",
            "Outdoor Activities",
            "Photography Tips",
            "Social Media Trends",
            "Productivity Hacks",
            "Cultural Experiences",
            "Educational Resources",
            "Celebrity News",
            "Motivational Quotes",
            "Car Reviews",
            "Hiking Trails",
            "Poetry Corner",
            "Sports Updates",
            "Parenting Humor",
            "Tech Gadgets",
            "Virtual Reality Experiences",
            "Home Improvement",
            "Relationship Advice",
            "Nature Exploration",
            "Educational Apps",
            "Crafty Creations",
            "Time Management",
            "Cooking Challenges",
            "Astronomy Facts",
            "Inspirational People",
            "Mind-Blowing Facts",
            "Movie Spoilers Discussion",
            "Unique Travel Destinations",
            "Science Fiction Corner",
            "Historical Fiction Recommendations",
            "Travel Hacks",
            "Community Spotlights",
            "Wildlife Conservation",
            "Budget Travel Tips",
            "Home Office Ideas",
            "Success Strategies",
            "Space Exploration",
            "Gaming Reviews",
            "Artistic Expressions",
            "Sustainable Fashion",
            "Entrepreneurship Tips",
            "Green Living",
            "Parenting Stories",
            "DIY Home Decor",
            "Personal Finance",
            "Nutrition Advice",
            "Mental Wellness Strategies",
            "Cultural Cuisine",
            "Musical Journey",
            "Leadership Insights",
            "Cybersecurity Tips",
            "Movie Analysis",
            "Fitness Challenges",
            "Pet Adoption Stories",
            "Nature Photography",
            "DIY Beauty Products",
            "History Unveiled",
            "Travel Photography",
            "Social Impact Initiatives",
            "Career Change Stories",
            "Virtual Events Highlights",
            "Home Organization",
            "Motivational Podcasts",
            "Futuristic Technologies",
            "Mindful Eating",
            "Green Energy Solutions",
            "Parenting Tips for Teens",
            "Adventure Sports",
            "DIY Gifts",
            "Financial Literacy",
            "Painting Techniques",
            "Mystery Novels",
            "Life Lessons from Nature"
        ];
        $postCategoriesBatch = [];
        foreach ($postCategories as $value) {
            $postCategoriesBatch[] = ['name' => $value];
        }
        DB::table('categories')->insert($postCategoriesBatch);
    }
}
