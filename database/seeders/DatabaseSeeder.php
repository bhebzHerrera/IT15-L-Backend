<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Technology', 'slug' => 'technology'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Sports', 'slug' => 'sports'],
            ['name' => 'Lifestyle', 'slug' => 'lifestyle'],
        ])->map(fn (array $data) => Category::create($data));

        $categoryMap = $categories->keyBy('slug');

        Post::create([
            'category_id' => $categoryMap['technology']->id,
            'title' => 'Simple Web App Ideas',
            'description' => 'A short list of beginner-friendly ideas to practice layout, routing, and database queries.',
        ]);

        Post::create([
            'category_id' => $categoryMap['technology']->id,
            'title' => 'Why MVC Matters',
            'description' => 'MVC keeps your code organized by separating data, logic, and views for easier maintenance.',
        ]);

        Post::create([
            'category_id' => $categoryMap['education']->id,
            'title' => 'Study Habits That Stick',
            'description' => 'Simple routines like time blocking and spaced repetition make learning feel manageable.',
        ]);

        Post::create([
            'category_id' => $categoryMap['sports']->id,
            'title' => 'Training for Consistency',
            'description' => 'Small, repeatable workouts build confidence and progress without burnout.',
        ]);

        Post::create([
            'category_id' => $categoryMap['lifestyle']->id,
            'title' => 'Minimalist Desk Setup',
            'description' => 'Keep only the essentials: laptop, notebook, and a small lamp for focus.',
        ]);
    }
}
