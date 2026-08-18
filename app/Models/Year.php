<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    protected $fillable=[
        'id',
        'academic_year',
        'created_at',
    ];
    public function users()
    {
        return $this->hasMany(User::class, 'year_id', 'id');
    }

    protected static function booted()
    {
        static::deleting(function ($year) {
        foreach ($year->users as $user) {
            Message::where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id)
                ->delete();
                $user->delete();
        }
    });
    }
}
