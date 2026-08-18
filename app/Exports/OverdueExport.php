<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OverdueExport implements FromCollection, WithHeadings
{
    protected $requests;
    protected $filterTitle;

    // constructor မှာ filterTitle ကို လက်ခံနိုင်ရန် ထည့်သွင်းပေးပါ
    public function __construct($requests, $filterTitle = null)
    {
        $this->requests = $requests;
        $this->filterTitle = $filterTitle;
    }

    public function collection()
    {
        $previousUserId = null;

        // အချက်အလက်များကို Mapping လုပ်ခြင်း (နာမည်နှင့် Roll Number ထပ်နေလျှင် ကွက်လပ်ထားရန်)
        $data = $this->requests->map(function ($req) use (&$previousUserId) {
            // ကျောင်းသားကို ခွဲခြားရန် ID သို့မဟုတ် Roll Number ကို သုံးပါ
            $currentUserId = $req->user->id ?? $req->user->roll_number;

            if ($currentUserId === $previousUserId) {
                $userName = '';
                $rollNumber = '';
            } else {
                $userName = $req->user->name;
                $rollNumber = $req->user->roll_number;
                $previousUserId = $currentUserId;
            }

            return [
                $userName,
                $rollNumber,
                $req->book->title,
                $req->book->code,
                $req->auto_fine,
            ];
        });

        // Total Amount ကို တွက်ချက်ခြင်း
        $totalFine = $this->requests->sum('auto_fine');

        // Total row ကို Array ထဲသို့ ပေါင်းထည့်ခြင်း
        $data->push([
            'TOTAL', 
            '', 
            '', 
            '', 
            $totalFine
        ]);

        return $data;
    }

    public function headings(): array
    {
        // Filter လုပ်ထားသော ခေါင်းစဉ်ပါလာပါက အပေါ်ဆုံးတွင် ပြသရန်
        $headingRows = [];

        if ($this->filterTitle) {
            $headingRows[] = [$this->filterTitle]; // Filter ခေါင်းစဉ်
            $headingRows[] = []; // ကြားထဲတွင် လပ်မည့် လိုင်း (သို့မဟုတ် လိုသလိုပြင်နိုင်သည်)
        }

        // ပုံမှန် Table ခေါင်းစဉ်များ
        $headingRows[] = ['User Name', 'Roll Number', 'Book Title', 'Book Code', 'Fine Amount'];

        return $headingRows;
    }
}