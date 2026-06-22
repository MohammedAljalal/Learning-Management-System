<?php

declare(strict_types=1);

namespace App\Services\Courses;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryContract;
use App\Services\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Log\LoggerInterface;
use Throwable;

class CategoryService extends Service
{
    public function __construct(
        LoggerInterface $logger,
        private readonly CategoryRepositoryContract $categoryRepository
    ) {
        parent::__construct($logger);
    }

    /**
     * Create a new category.
     *
     * @throws Throwable
     */
    public function createCategory(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = Str::slug($data['name']);
            
            $category = $this->categoryRepository->create($data);
            
            $this->logger->info("Category created.", ['category_id' => $category->id]);
            Cache::forget('root_categories');
            
            return $category;
        });
    }

    /**
     * Update an existing category.
     *
     * @throws Throwable
     */
    public function updateCategory(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            if (isset($data['name']) && $data['name'] !== $category->name) {
                $data['slug'] = Str::slug($data['name']);
            }

            $updatedCategory = $this->categoryRepository->update($category->id, $data);
            
            $this->logger->info("Category updated.", ['category_id' => $updatedCategory->id]);
            Cache::forget('root_categories');
            
            return $updatedCategory;
        });
    }

    /**
     * Delete a category.
     *
     * @throws Throwable
     */
    public function deleteCategory(int $id): void
    {
        DB::transaction(function () use ($id) {
            $this->categoryRepository->delete($id);
            $this->logger->info("Category deleted.", ['category_id' => $id]);
            Cache::forget('root_categories');
        });
    }

    /**
     * Get root categories, cached.
     */
    public function getRootCategories()
    {
        return Cache::remember('root_categories', now()->addDay(), function () {
            return Category::whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']);
        });
    }
}
