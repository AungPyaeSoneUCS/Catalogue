<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('library:update-status')->hourly(); // တစ်နာရီတစ်ကြိမ် နောက်ကွယ်မှ Auto စစ်ဆေးပေးမည်
// 🎯 တစ်မိနစ်တိုင်း အချိန်ကျော်တာရှိမရှိ စစ်ဆေးခိုင်းခြင်း
Schedule::command('app:cancel-expired-bookings')->everyMinute();