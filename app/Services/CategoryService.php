<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Throwable;

class CategoryService
{
    public function create(string $name, ?string $slug, ?string $description): Category
    {
        try {
            return DB::transaction(function () use ($name, $slug, $description) {
                $slugBase = $slug ?? Str::slug($name);
                $finalSlug = $this->uniqueSlug($slugBase);

                return Category::create([
                    'name' => $name,
                    'slug' => $finalSlug,
                    'description' => $description,
                ]);
            });
        } catch (Throwable $e) {
            Log::error('Failed to create category', [
                'name' => $name,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function update(Category $category, string $name, ?string $slug, ?string $description): Category
    {
        try {
            return DB::transaction(function () use ($category, $name, $slug, $description) {
                $slugBase = $slug ?? Str::slug($name);
                $finalSlug = $this->uniqueSlug($slugBase, $category->id);

                $category->update([
                    'name' => $name,
                    'slug' => $finalSlug,
                    'description' => $description,
                ]);

                return $category->fresh();
            });
        } catch (Throwable $e) {
            Log::error('Failed to update category', [
                'category_id' => $category->id,
                'name' => $name,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function delete(Category $category): bool
    {
        try {
            return DB::transaction(function () use ($category) {
                // Check if category has topics
                if ($category->topics()->count() > 0) {
                    throw new \Exception('Tidak dapat menghapus kategori yang memiliki topik terkait.');
                }

                return $category->delete();
            });
        } catch (Throwable $e) {
            Log::error('Failed to delete category', [
                'category_id' => $category->id,
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function uniqueSlug(string $baseSlug, ?int $excludeId = null): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while (Category::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }
}

