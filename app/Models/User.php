<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'roll_number',
        'year_id',
        'phone',
        'profile',
        'role',
        'is_approved',
        'created_at',
    ];

    public function member()
{
    // A user has one active verification member record
    return $this->hasOne(Member::class, 'user_id', 'id');
}
public function isAdmin()
    {
        return $this->role === 'admin';
    }
public function year()
    {
        return $this->belongsTo(Year::class, 'year_id', 'id');
    }

public function lastMessage()
{
    return $this->hasOne(Message::class, 'sender_id', 'id')
                ->latestOfMany(); 
}
public function unreadMessages()
{
    // ဒီနေရာမှာ $this->id သည် message ပို့တဲ့သူ (sender) ဖြစ်ရပါမယ်
    // ဆိုလိုတာက ဒီ user ကပို့တဲ့ မဖတ်ရသေးတဲ့ message တွေလို့ အဓိပ္ပာယ်ရပါတယ်
    return $this->hasMany(Message::class, 'sender_id', 'id')
                ->where('is_read', 0);
}

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            // ၁။ User ငှားထားတဲ့ စာအုပ်တွေကို ရှာပါ
            $borrowedRequests = BorrowRequest::where('user_id', $user->id)
                                             ->whereIn('status', ['borrowed', 'overdue','pending'])
                                             ->get();

            // ၂။ စာအုပ်တွေကို 1 ပြန်တိုးပေးပါ
            foreach ($borrowedRequests as $request) {
                $book = Book::find($request->book_id);
                if ($book) {
                    $book->increment('available_qty');
                }
            }

            // ၃။ ပုံဖျက်ခြင်း
            if ($user->profile && file_exists(public_path('userProfile/' . $user->profile))) {
                @unlink(public_path('userProfile/' . $user->profile));
            }
        // ၃။ Member record ကို ဖျက်ပါ (ဒါမှ Member ရဲ့ deleting event အလုပ်လုပ်မှာပါ)
        if ($user->member) {
            $user->member->delete();
        }
        });
    }
}
