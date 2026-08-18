<?php

namespace App\Http\Controllers\Admin;

use App\Exports\BooksExport;
use App\Http\Controllers\Controller;
use App\Imports\BooksImport;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
 use ZipArchive;

class BookController extends Controller
{
    public function details(Request $request)
    {
        // ၁။ ရှာဖွေထားသော အချက်အလက်များကို Session တွင် စစ်ဆေးခြင်း
    if ($request->hasAny(['search', 'category_id'])) {
        // အသစ်ရှာဖွေမှုရှိလျှင် Session ကို update လုပ်ပါ
        session(['last_query' => $request->only(['search', 'category_id'])]);
    } elseif (session()->has('last_query') && !$request->hasAny(['search', 'category_id'])) {
        // နောက်စာမျက်နှာသို့ သွားသည့်အခါ (search မပါလာလျှင်) Session ထဲမှ ပြန်ဖတ်ပါ
        $request->merge(session('last_query'));
    } else {
        // search အသစ်လုပ်ခြင်းမရှိလျှင် session ကို ရှင်းလင်းပါ
        session()->forget('last_query');
    }
    
        $categories = Category::all();
        $query = Book::query()->with('category');

        // Dropdown Category Filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search Box (Abstract ပါ ရှာမည်)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('abstract', 'like', "%{$search}%")
                  ->orWhere('press', 'like', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(books.created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%']);
            });
        }

        $totalBooksCount = $query->sum('total_qty');
        $books = $query->latest()->paginate(30);//get();////
        return view('admin.book.home', compact('books', 'categories','totalBooksCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'code'        => 'required|string|max:255|unique:books,code',
            'category_id' => 'required|exists:categories,id',
            'total_qty'   => 'required|integer|min:1',
            'cover_file'  => 'required|image',
            'abstract'    => 'required|string',
            'content'     => 'required|string',
            'press'       =>'required|string',
        ], [
            'title.required'       => __('validation.title_required'),
            'author.required'      => __('validation.author_required'),
            'code.required'        => __('validation.code_required'),
            'code.unique'          => __('validation.code_unique'),
            'category_id.required' => __('validation.category_required'),
            'total_qty.required'   => __('validation.qty_required'),
            'total_qty.min'        => __('validation.qty_min'),
            'cover_file.required'  => __('validation.cover_required'),
            'cover_file.image'     => __('validation.cover_image'),
            'abstract.required'    => __('validation.abstract_required'),
            'content.required'     => __('validation.content_required'),
            'press.required'       => __('validation.press_required')
        ]);

        $book = new Book($request->except(['cover_file']));
        
        // စာအုပ်အသစ်ဆောက်ချိန်တွင် စုစုပေါင်းအရေအတွက်အတိုင်း ငှားရန်ရှိသည်ကို သတ်မှတ်ပေးခြင်း
        $book->available_qty = $request->total_qty;

        // 🎯 ပြင်ဆင်ချက် - cover_image ထဲကို file name သီးသန့်ပဲ သိမ်းဆည်းခြင်း
        if ($request->hasFile('cover_file')) {
            $fileName = $request->file('cover_file')->hashName();
            $request->file('cover_file')->storeAs('books', $fileName, 'public');
            $book->cover_image = $fileName;
        }

        $book->save();
        return back()->with('success', __('book_created_success'));
    }

     public function import(Request $request)
{
    // Validator ကို သီးခြား object အနေဖြင့် ဆောက်ပါ
    $validator = Validator::make($request->all(), [
        'excel_file' => ['required', 'mimes:xlsx'],
        'cover_zip'  => ['required', 'file', 'mimes:zip'],
    ], [
        'excel_file.required' => __('excel_file.required'),
        'excel_file.mimes'    => __('excel_file.mimes'),
        'cover_zip.required'  => __('cover_zip.required'),
        'cover_zip.mimes'     => __('cover_zip.mimes'),
    ]);

    // Validator ကို စစ်ဆေးပါ
    if ($validator->fails()) {
        // 'excel_errors' ဆိုတဲ့ နာမည်နဲ့ Error Bag ကို ပို့ပါ
        return back()->withErrors($validator, 'excel_errors')->withInput();
    }

    try {
        Excel::import(new BooksImport, $request->file('excel_file'));
    } catch (ValidationException $e) {
        $failures = $e->failures();
        return back()->with('excel_validation_errors', $failures);
    }
    

    // Zip file handling
    $zip = new ZipArchive;
    $file = $request->file('cover_zip');
    if ($zip->open($file->getRealPath()) === TRUE) {
        $zip->extractTo(storage_path('app/public/books'));
        $zip->close();
    }

    return back()->with('success', __('book_imported_success'));
}

public function show($id)
    {
        $book = Book::findOrFail($id);
        return view('admin.book.show', compact('book'));
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('admin.book.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $bookId = is_object($id) ? $id->id : $id;
        $book = Book::findOrFail($bookId);

        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:255', Rule::unique('books', 'code')->ignore($book->id)],
            'press'       => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'total_qty'   => 'required|integer|min:0',
            'available_qty'   => 'required|integer|min:0|lte:total_qty',//|lte:total_qty
            'cover_file'  => 'nullable|image',
            'abstract'    => 'required|string',
            'content'     => 'required|string',
        ],[
            'title.required'       => __('validation.title_required'),
            'author.required'      => __('validation.author_required'),
            'code.required'        => __('validation.code_required'),
            'code.unique'          => __('validation.code_unique'),
            'category_id.required' => __('validation.category_required'),
            'total_qty.required'   => __('validation.qty_required'),
            'total_qty.integer'   => __('validation.numeric'),
            'available_qty.required'=> __('validation.available_required'),
            'available_qty.integer'=> __('validation.numeric'),
            'available_qty.lte' => __('validation.available_qty_cannot_be_greater_than_total'),
            'total_qty.min'        => __('validation.qty_min'),
            'cover_file.required'  => __('validation.cover_required'),
            'cover_file.image'     => __('validation.cover_image'),
            'abstract.required'    => __('validation.abstract_required'),
            'content.required'     => __('validation.content_required'),
            'press.required'       => __('validation.press_required')
        ]);

//         $newTotalQty = (int)$request->total_qty;
// $newAvailableQty = (int)$request->available_qty;
// $difference = $newTotalQty - $book->total_qty;

// // ၂။ အဓိက စစ်ဆေးချက် (Security Guard)
// // User ရိုက်ထည့်လိုက်တဲ့ available က new total ထက် ကြီးနေရင် ရပ်တန့်ပါ
// if ($newAvailableQty > $newTotalQty) {
//     return redirect()->back()->with('available_error', __('validation.available_qty_cannot_be_greater_than_total'));
// }
        $oldTotalQty = $book->total_qty;
        $newTotalQty = (int)$request->total_qty;
        $difference = $newTotalQty - $oldTotalQty;

        if ($difference < 0 && $book->available_qty < abs($difference)) {
            return redirect()->back()->with('error', __('insufficient_available_qty_error'));
        }

        $book->title = $request->title;
        $book->author = $request->author;
        $book->code = $request->code;
        $book->press = $request->press;
        $book->category_id = $request->category_id;
        
        $book->total_qty = $newTotalQty;
        //$book->available_qty = $book->available_qty + $difference; 
        //$book->available_qty=$request->available_qty;
        // ၃။ Total Qty ပြောင်းလဲပါက Available Qty ကိုပါ ညှိပေးခြင်း
if ($difference !== 0) {
    // Total Qty ပြောင်းသွားရင် Available ကို အဲ့ဒီ Difference အတိုင်း လိုက်ညှိပေးမယ်
    $book->total_qty = $newTotalQty;
    $book->available_qty = $book->available_qty + $difference;
} else {
    // Total Qty မပြောင်းဘူးဆိုရင် User ပေးတဲ့ Available Qty အတိုင်း သတ်မှတ်မယ်
    $book->available_qty = $request->available_qty;
}
        $book->abstract = $request->abstract;
        $book->content = $request->input('content');

        if ($request->hasFile('cover_file')) {
            if ($book->cover_image && Storage::disk('public')->exists('books/' . $book->cover_image)) {
                Storage::disk('public')->delete('books/' . $book->cover_image);
            }
            $fileName = $request->file('cover_file')->hashName();
            $request->file('cover_file')->storeAs('books', $fileName, 'public');
            $book->cover_image = $fileName;
        }

        $book->save();
        return redirect()->back()->with('bookUpdateSuccess', __('book_updated_success'));
    }
    public function downloadTemplate()
{
    $filePath = public_path('templates/books_import_template.xlsx');
    
    if (file_exists($filePath)) {
        return response()->download($filePath);
    }
    
    return back()->with('error', 'Template file not found!');
}
    public function export(Request $request)
{
    $fileName = 'books_report_' . now()->format('d-M-Y') . '.xlsx';
    
    return Excel::download(new BooksExport($request), $fileName);
}

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image && Storage::disk('public')->exists('books/' . $book->cover_image)) {
            Storage::disk('public')->delete('books/' . $book->cover_image);
        }

        $book->delete();
        return redirect()->route('list#bookDetails')->with('success', __('book_deleted_success'));
    }
}