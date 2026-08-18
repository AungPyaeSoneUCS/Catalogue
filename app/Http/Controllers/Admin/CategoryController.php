<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories filtered by search parameters.
     */
    public function details(Request $request): View
    {
        if ($request->has('search') && !$request->filled('search')) {
        // User က search ဘောက်စ်ကို ဖျက်ပြီး ရှာလိုက်ရင် Session ရှင်းမယ်
        session()->forget('category_last_query');
    } elseif ($request->filled('search')) {
        // User က စာရိုက်ပြီး ရှာရင် Session မှာ မှတ်မယ်
        session(['category_last_query' => $request->search]);
    } elseif (session()->has('category_last_query')) {
        // Page ပြန်ဝင်လာရင် Session ထဲကဟာကို ပြန်ယူသုံးမယ်
        $request->merge(['search' => session('category_last_query')]);
    }
        $search = $request->get('search');

        $categories = Category::query()
            ->when($search, fn($query) => $query->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhereRaw("DATE_FORMAT(created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%'])
            ))
            
            ->get();

        return view('admin.category.home', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'unique:categories,name'],
    ], [
        'name.required' => __('The category name is required.'),
        'name.unique'   => __('This category name has already been taken.'),
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_type', 'create'); // Create Modal ကို ပြန်ဖွင့်ရန်
    }

    Category::create($validator->validated());

    return to_route('list#categoryDetails')
        ->with('createSuccess', __('Category created successfully.'));
}

public function update(Request $request, Category $category): RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'name' => ['required', 'string', 'max:255', "unique:categories,name,{$category->id}"],
    ], [
        'name.required' => __('The category name is required.'),
        'name.unique'   => __('This category name has already been taken.'),
    ]);

    if ($validator->fails()) {
        return back()
            ->withErrors($validator)
            ->withInput()
            ->with('modal_type', 'edit')
            ->with('category_id', $category->id); // Edit Modal ကို ပြန်ဖွင့်ရန်
    }

    $category->update($validator->validated());

    return to_route('list#categoryDetails')
        ->with('updateSuccess', __('Category updated successfully.'));
}

    /**
     * Remove the specified category using Route Model Binding.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return to_route('list#categoryDetails')
            ->with('deleteSuccess', __('Category deleted successfully.'));
    }
}