<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRequest extends Model
{
    protected static function boot()
    {
        parent::boot();

        // BorrowRequest ကို ဖျက်ပြီးသွားတဲ့အခါ (deleted)
        static::deleted(function ($borrowRequest) {
            // အကယ်၍ ဖျက်လိုက်တဲ့ record ရဲ့ status က 'lost' ဖြစ်နေရင်
            if ($borrowRequest->status === 'lost') {
                // တကယ်လို့ တခြားနေရာကနေ စာအုပ်အရေအတွက် total_qty ကို 
                // အလိုအလျောက် ပြန်တိုးသွားစေခဲ့ရင်တောင် မတိုးသွားအောင် 
                // (သို့မဟုတ် ပြန်လျှော့ချချင်ရင်) ဒီနေရာမှာ ထည့်လို့ရပါတယ်။
                
                if ($borrowRequest->book) {
                    // ဖျက်လိုက်လို့ အရေအတွက် ပြန်တိုးသွားတာကို တားဆီးရန် 
                    // (decrement ထပ်လုပ်ပြီး မူလအတိုင်း ဆက်နည်းနေစေရန်)
                    $borrowRequest->book->decrement('total_qty');
                }
            }
        });
    }
    protected $fillable = ['user_id', 'book_id', 'status', 'booking_at', 'borrowed_at', 'due_at', 'returned_at', 'fine_amount','lost_fine','damage_fine'];

    // စာအုပ်ငှားသည့် User ကို ချိတ်ဆက်ခြင်း
    public function user() {
        return $this->belongsTo(User::class);
    }

    // ငှားရမ်းထားသည့် Book ကို ချိတ်ဆက်ခြင်း
    public function book() {
        return $this->belongsTo(Book::class);
    }
    // app/Models/BorrowRequest.php

// အကြံပြုချက်: Accessor ထဲတွင် Query မရေးပါနှင့်
public function getAutoFineAttribute() 
{
    static $rate;
    if (!$rate) {
        $rate = \App\Models\SystemSetting::where('key', 'daily_fine_rate')->value('value') ?? 100;
    }

    if ($this->status !== 'overdue' || !$this->due_at) return 0;
    
    // 🎯 ဒီမှာ abs() ကို ထည့်သုံးလိုက်ပါ
    $daysLate = abs(ceil(\Carbon\Carbon::parse($this->due_at)->diffInDays(now(), false)));
    
    // ရက်လွန်နေရင် အနည်းဆုံး 1 ရက်စာ ဒဏ်ကြေး (အနှုတ်ကိန်းမဖြစ်အောင်)
    return (int)(($daysLate == 0 ? 1 : $daysLate) * $rate);
}
protected $casts = [
    'returned_at' => 'datetime',
    'due_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
}