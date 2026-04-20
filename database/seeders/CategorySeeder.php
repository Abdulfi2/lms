<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Programming', 'slug' => 'programming', 'icon' => '💻', 'color' => '#3B82F6', 'order' => 1],
            ['name' => 'Web Development', 'slug' => 'web-development', 'icon' => '🌐', 'color' => '#10B981', 'order' => 2, 'parent_id' => 1],
            ['name' => 'Mobile Development', 'slug' => 'mobile-development', 'icon' => '📱', 'color' => '#F59E0B', 'order' => 3, 'parent_id' => 1],
            ['name' => 'Data Science', 'slug' => 'data-science', 'icon' => '📊', 'color' => '#EF4444', 'order' => 4],
            ['name' => 'Design', 'slug' => 'design', 'icon' => '🎨', 'color' => '#8B5CF6', 'order' => 5],
            ['name' => 'Business', 'slug' => 'business', 'icon' => '💼', 'color' => '#EC4899', 'order' => 6],
            ['name' => 'Marketing', 'slug' => 'marketing', 'icon' => '📈', 'color' => '#14B8A6', 'order' => 7],
            ['name' => 'Photography', 'slug' => 'photography', 'icon' => '📷', 'color' => '#6366F1', 'order' => 8],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Categories seeded successfully.');
    }
}