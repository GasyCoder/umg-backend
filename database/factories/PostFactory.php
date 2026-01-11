<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        $title = $this->faker->sentence;
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => $this->faker->paragraph,
            'content_html' => '<p>' . $this->faker->paragraph(5) . '</p>',
            'status' => 'published',
            'published_at' => now(),
            'author_id' => User::factory(), // Will create a user if not provided
            'is_featured' => $this->faker->boolean(20),
            'is_slide' => $this->faker->boolean(20),
            'views_count' => $this->faker->numberBetween(0, 1000),
        ];
    }
}
