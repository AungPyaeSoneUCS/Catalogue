<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-expired-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    // Admin ဆက်တင်မှ အချိန်ကို ဆွဲယူခြင်း (သို့မဟုတ် default 24)
    $expireHours = \App\Models\SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24;

    $expired = \App\Models\BorrowRequest::where('status', 'pending')
        ->where('booking_at', '<', \Carbon\Carbon::now()->subHours($expireHours))
        ->get();

    foreach ($expired as $req) {
        if ($req->book) {
            $req->book->increment('available_qty');
        }
        $req->update(['status' => 'canceled']);
    }
    
    $this->info('Expired bookings have been processed successfully.');
}
}
