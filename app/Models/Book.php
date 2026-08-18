<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    protected $fillable = [
        'id',
        'category_id',
        'title',
        'author',
        'code',
        'press',
        'total_qty',
        'available_qty',
        'cover_image',
        'image_path',
        'content',
        'abstract',
        'created_at'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function books()
{
    return $this->hasMany(Book::class, 'category_id');
}
protected static function booted()
{
    static::deleting(function ($book) {
        // သင့်ရဲ့ Storage Code
        if ($book->cover_image && Storage::disk('public')->exists('books/' . $book->cover_image)) {
            Storage::disk('public')->delete('books/' . $book->cover_image);
        }
    });
}
}
