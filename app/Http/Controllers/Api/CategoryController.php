<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::orderBy('title')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'status' => ['nullable', 'boolean'],
        ]);

        $category = Category::create([
            'title' => $data['title'],
            'slug' => $this->uniqueSlug($data['title']),
            'status' => $request->boolean('status', true),
            'body' => $data['title'].' used in construction inventory.',
        ]);

        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'status' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'title' => $data['title'],
            'slug' => $category->slug ?: $this->uniqueSlug($data['title']),
            'status' => $request->boolean('status', true),
        ]);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        if ($category->services()->exists()) {
            return response()->json([
                'message' => 'This category still has materials.',
            ], 422);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted.']);
    }

    protected function uniqueSlug(string $title): string
    {
        $base = str($title)->slug()->toString() ?: 'category';
        $slug = $base;
        $i = 2;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
