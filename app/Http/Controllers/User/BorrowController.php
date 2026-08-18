<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function requestBooking($bookId)
    {
        $book = Book::findOrFail($bookId);
        $userId = Auth::id();

        if (BorrowRequest::where('user_id', $userId)->where('book_id', $bookId)->whereIn('status', ['pending', 'borrowed', 'overdue'])->exists()) {
            return redirect()->back()->with('error', __('You have already booked or borrowed this book, so you cannot book it again.'));
        }

        $maxLimit = (int) SystemSetting::where('key', 'max_borrow_limit')->value('value') ?? 3;
        $currentBorrowedCount = BorrowRequest::where('user_id', $userId)->whereIn('status', ['pending', 'borrowed', 'overdue'])->count();

        if ($currentBorrowedCount >= $maxLimit) {
            return redirect()->back()->with('error', __('You have reached the maximum limit of :maxLimit books.', ['maxLimit' => $maxLimit]));
        }

        if ($book->available_qty <= 0) {
            return redirect()->back()->with('error', __('This book is currently out of stock.'));
        }

        BorrowRequest::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'status'  => 'pending',
            'booking_at' => now()
        ]);

        $book->decrement('available_qty');

        return redirect()->back()->with('success', __('Booking created successfully.'));
    }

    public function extendBorrow($id)
    {
        $borrow = BorrowRequest::findOrFail($id);

        if ($borrow->status === 'overdue' || now()->gt($borrow->due_at)) {
            return redirect()->back()->with('error', __('The book is overdue, so it cannot be renewed. Please pay the fine first.'));
        }

        $extendDays = (int) SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7;

        $borrow->update([
            'due_at' => Carbon::parse($borrow->due_at)->addDays($extendDays)->endOfDay()
        ]);

        return redirect()->back()->with('success', __('The borrowing period has been extended for another :extendDays days.', ['extendDays' => $extendDays]));
    }

    public function bookingHistory()
    {
        $userId = Auth::id();
        $expireHours = (float) SystemSetting::where('key', 'booking_expire_hours')->value('value') ?? 24.0;

        // Auto Cancel Logic
        BorrowRequest::where('user_id', $userId)
            ->where('status', 'pending')
            ->where('booking_at', '<', Carbon::now()->subMinutes((int)($expireHours * 60)))
            ->each(function ($booking) {
                if ($booking->book) $booking->book->increment('available_qty');
                $booking->update(['status' => 'canceled']);
            });

        $requests = BorrowRequest::with('book')
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'canceled'])
            ->latest()
            ->get();

        return view('user.book.borrow-requests', compact('requests', 'expireHours'));
    }

    public function currentBorrows()
    {
        $borrows = BorrowRequest::with('book')
            ->where('user_id', Auth::id())
            ->whereIn('status', ['borrowed', 'overdue'])
            ->latest()
            ->get();

        $extendDays = (int) SystemSetting::where('key', 'borrow_duration_days')->value('value') ?? 7;
        return view('user.book.borrow', compact('borrows','extendDays'));
    }

    public function cancelBooking($id)
    {
        $booking = BorrowRequest::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if ($booking->status === 'pending' && $booking->book) {
            $booking->book->increment('available_qty');
        }

        $booking->delete();

        return redirect()->back()->with('success', __('Booking request deleted and book stock has been updated.'));
    }
}