<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=[
        'id',
        'name',
        'created_at'
    ];

    public function books()
{
    return $this->hasMany(Book::class, 'category_id');
}
protected static function booted()
{
    static::deleting(function ($subject) {
        // Subject ထဲက စာအုပ်အားလုံးကို ရှာပါ
        foreach ($subject->books as $book) {
            // စာအုပ်တိုင်းကို delete() လုပ်ခြင်းဖြင့် 
            // Book Model ထဲက deleting event (ပုံဖျက်ခြင်း) ကို ခေါ်လိုက်ပါမယ်
            $book->delete();
        }
    });
}
}
