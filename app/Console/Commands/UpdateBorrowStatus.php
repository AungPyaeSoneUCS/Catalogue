<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BorrowRequest;
use Carbon\Carbon;

class UpdateBorrowStatus extends Command
{
    protected $signature = 'library:update-status';
    protected $description = 'Auto cancel expired booking and update fine amount';

    public function handle()
    {
        // ၁။ Booking Auto Cancel လုပ်ခြင်း
        $expiredBookings = BorrowRequest::where('status', 'pending')
            ->where('booking_at', '<=', Carbon::now()->subHours(24))
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update(['status' => 'canceled']);
        }

        // ၂။ Overdue စစ်ဆေးခြင်း
        $activeBorrows = BorrowRequest::whereIn('status', ['borrowed', 'overdue'])
            ->where('due_at', '<', Carbon::now())
            ->get();

        foreach ($activeBorrows as $borrow) {
            // 🎯 အရေးကြီးချက် - Model ထဲက getAutoFineAttribute ကို တိုက်ရိုက်ခေါ်သုံးပါ
            // ဒါဆိုရင် Model ထဲမှာ ရေးထားတဲ့ Logic အတိုင်းပဲ အမြဲတွက်ပေးသွားမှာပါ
            $calculatedFine = $borrow->auto_fine;

            $borrow->update([
                'status' => 'overdue',
                'fine_amount' => $calculatedFine
            ]);
        }
        
        $this->info('Statuses and fines updated successfully.');
    }
}