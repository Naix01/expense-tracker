<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('user_id', Auth::id())->get();
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,NULL,id,user_id,' . Auth::id(),
        ]);

        $category = Category::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id . ',id,user_id,' . Auth::id(),
        ]);

        $category->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Category updated successfully', 'category' => $category]);
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
