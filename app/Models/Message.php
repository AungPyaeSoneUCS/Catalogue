<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['sender_id', 'receiver_id', 'message', 'is_read'];

    // ပေးပို့သူ (Sender) ကို သိရန် Relation
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // လက်ခံသူ (Receiver) ကို သိရန် Relation
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}