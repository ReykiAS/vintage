<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Image;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a few users
        $users = User::factory(1)->create();

        // Create categories, brands, and products with relationships
        $categories = Category::factory(5)->create();
        $brands = Brand::factory(5)->create();

        Product::factory(10)
            ->has(Image::factory()) // One image per product
            ->create([
                'user_id' => $users->random()->id,
            ])
            ->each(function ($product) use ($categories, $brands) {
                $product->category()->associate($categories->random())->save();
                $product->brand()->associate($brands->random())->save();
            });

        // Assign images to categories, brands, and users
        foreach ($categories as $category) {
            $image = Image::factory()->make();
            $image->imageable_type = Category::class;
            $image->imageable_id = $category->id;
            $image->save();
        }

        foreach ($brands as $brand) {
            $image = Image::factory()->make();
            $image->imageable_type = Brand::class;
            $image->imageable_id = $brand->id;
            $image->save();
        }

        foreach ($users as $user) {
            $image = Image::factory()->make();
            $image->imageable_type = User::class;
            $image->imageable_id = $user->id;
            $image->save();
        }
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
