<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function GetAll()
    {
        return Category::where('is_deleted', false)
            ->get();
    }

    public function GetActive()
    {
        return Category::where('is_active', true)
            ->where('is_deleted', false)
            ->get();
    }

    public function GetById($id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json($category);
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function Store(CategoryRequest $request)
    {
        $user = Auth::user();
        $category = new Category;

        $category->category = $request->input('category');
        $category->is_active = $request->input('is_active') ?? true;
        $category->created_by = $user->id;

        if ($category->save()) {
            return response()->json(['message' => 'Category created successfully']);
        } else {
            return response()->json(['message' => 'Failed to create Category'], 500);
        }
    }

    public function Update(CategoryRequest $request, $id)
    {
        $user = Auth::user();
        $category = Category::find($id);

        if ($category) {
            $category->category = $request->input('category');
            $category->is_active = $request->input('is_active') ?? true;
            $category->updated_by = $user->id;

            if ($category->save()) {
                return response()->json(['message' => 'Category updated successfully']);
            } else {
                return response()->json(['message' => 'Failed to update Category'], 500);
            }
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }

    public function SoftDelete($id)
    {
        $user = Auth::user();
        $category = Category::find($id);

        if ($category) {
            $category->is_deleted = true;
            $category->deleted_by = $user->id;
            $category->deleted_at = now();

            if ($category->save()) {
                return response()->json(['message' => 'Category deleted successfully']);
            } else {
                return response()->json(['message' => 'Failed to delete Category'], 500);
            }
        } else {
            return response()->json(['message' => 'Category not found'], 404);
        }
    }
}
