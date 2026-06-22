<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Services\Courses\CategoryService;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public string $name = '';
    public string $icon = '';
    public ?int $parentId = null;
    public ?int $editingId = null;
    public bool $showModal = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'icon' => 'nullable|string|max:100',
            'parentId' => 'nullable|integer|exists:categories,id',
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'icon', 'parentId', 'editingId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $cat->id;
        $this->name = $cat->name;
        $this->icon = $cat->icon ?? '';
        $this->parentId = $cat->parent_id;
        $this->showModal = true;
    }

    public function save(CategoryService $categoryService): void
    {
        abort_unless(auth()->user()->hasRole('Super Admin'), 403, 'Unauthorized action.');
        $this->validate();

        $data = [
            'name' => $this->name,
            'icon' => $this->icon ?: null,
            'parent_id' => $this->parentId,
        ];

        if ($this->editingId) {
            $cat = Category::findOrFail($this->editingId);
            $categoryService->updateCategory($cat, $data);
            session()->flash('success', 'تم تحديث التصنيف بنجاح.');
        } else {
            $categoryService->createCategory($data);
            session()->flash('success', 'تم إنشاء التصنيف بنجاح.');
        }

        $this->reset(['name', 'icon', 'parentId', 'editingId', 'showModal']);
        $this->resetPage();
    }

    public function delete(int $id, CategoryService $categoryService): void
    {
        abort_unless(auth()->user()->hasRole('Super Admin'), 403, 'Unauthorized action.');
        $categoryService->deleteCategory($id);
        session()->flash('success', 'تم حذف التصنيف.');
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['name', 'icon', 'parentId', 'editingId']);
    }

    public function render(CategoryService $categoryService)
    {
        $categories = Category::with('parent')
            ->withCount('courses')
            ->orderBy('name')
            ->paginate(20);

        $parentOptions = $categoryService->getRootCategories();

        return view('livewire.admin.category-manager', compact('categories', 'parentOptions'))
            ->layout('layouts.app');
    }
}
