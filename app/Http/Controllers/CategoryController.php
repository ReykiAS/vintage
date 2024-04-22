<?php

namespace App\Http\Controllers;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category = Category::all();
        return CategoryResource::collection($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos');
            $category->addImage($photoPath);
        }

        return response()->json(['message' => 'Category created successfully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::findOrFail($id);
        $category->loadMissing('image');
        return new CategoryResource($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryUpdateRequest $request,  Category $category)
    {
        $validated = $request->validated();
        $category->update($validated);
        $category->updateImage($request);
        return response()->json(['message' => 'Category successfully updated', 'category' => new CategoryResource($category)]);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
    /**
     * show data deleted.
     */
    public function showSoftDeleted()
    {
        $deletedCategories = Category::onlyTrashed()->get();
        return response()->json($deletedCategories);
    }
    /**
     * restore the data.
     */

     public function restore($id)
     {
        $category = Category::withTrashed()->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->restore();

        return response()->json(['message' => 'Category succesfully restored.']);
     }

}
