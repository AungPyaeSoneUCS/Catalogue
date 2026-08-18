<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $fillable=[
        'id',
        'user_id',
        'payment_id',
        'fee',
        'payslip',
        'created_at',
    ];
    public function payment()
    {
        // Links members.payment_id to payments.id
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    protected static function booted()
    {
        static::deleting(function ($member) {
            // public/payslipImage/ ထဲက ပုံကို ဖျက်ခြင်း
            if ($member->payslip) {
                $filePath = public_path('payslipImage/' . $member->payslip);
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        });
    }
}
