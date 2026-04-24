<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Laravel', 'color' => '#F05340', 'description' => 'Framework PHP populer'],
            ['name' => 'Vue.js', 'color' => '#42B883', 'description' => 'JavaScript framework'],
            ['name' => 'React', 'color' => '#61DAFB', 'description' => 'Library JavaScript'],
            ['name' => 'Tailwind CSS', 'color' => '#38B2AC', 'description' => 'Utility-first CSS'],
            ['name' => 'Alpine.js', 'color' => '#8BC0D0', 'description' => 'Lightweight JavaScript framework'],
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag['name'],
                'slug' => \Illuminate\Support\Str::slug($tag['name']),
                'color' => $tag['color'],
                'description' => $tag['description'],
                'is_active' => true,
            ]);
        }
    }
}