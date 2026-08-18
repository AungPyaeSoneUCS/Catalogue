<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category; // 💡 Category Model ကို Import လုပ်ရန်
use App\Models\Book;     // 💡 Book Model ကို Import လုပ်ရန်

class DashboardController extends Controller
{
    /**
     * User Home Page / Dashboard ကို ပြသပေးသည့် Function
     */
    public function dashboard(Request $request)
    {
        
       // ၁။ URL မှာ search သို့မဟုတ် category_id တကယ်ပါလာရင် Session ထဲမှာ သိမ်းမယ်
    if ($request->hasAny(['search', 'category_id'])) {
        session(['last_query' => $request->only(['search', 'category_id'])]);
    } 
    elseif (session()->has('last_query') && !$request->hasAny(['search', 'category_id'])) {
        $lastQuery = session('last_query');
        $request->merge($lastQuery); 
    }
    else {
        session()->forget('last_query');
    }
        $categories = Category::all();

        $booksQuery = Book::with('category'); 

        if ($request->filled('category_id')) {
            $booksQuery->where('category_id', $request->category_id);
        }

        $search = $request->input('search');
        if (!empty($search)) {
            $booksQuery->where(function($query) use ($search) {
                $query->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('author', 'LIKE', "%{$search}%")
                      ->orwhere('abstract',"LIKE","%{$search}%")
                      ->orwhere('content',"LIKE","%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%"); 
            });
        }

        $totalBooks = (clone $booksQuery)->sum('total_qty');
        $books = $booksQuery->inRandomOrder()->paginate(48);//latest()->
        return view('user.dashboard.home', compact('categories', 'books','totalBooks'));
    }
    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);

        return view('user.dashboard.show', compact('book'));
    }
}