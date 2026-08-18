<?php

namespace App\Exports;

use App\Models\BorrowRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LostBooksExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    protected $search;

    public function __construct($search = null) {
        $this->search = $search;
    }

    public function collection()
    {
        return BorrowRequest::with(['user.year', 'book'])
            ->where('status', 'lost')
            ->when($this->search, function ($query) {
                $search = '%' . $this->search . '%';
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', function ($sub) use ($search) {
                        $sub->where('name', 'like', $search)
                            ->orWhere('roll_number', 'like', $search)
                            ->orWhereHas('year', fn($y) => $y->where('academic_year', 'like', $search));
                    })
                    ->orWhereHas('book', function ($sub) use ($search) {
                        $sub->where('title', 'like', $search)
                            ->orWhere('code', 'like', $search);
                    })
                    ->orWhereRaw("DATE_FORMAT(borrow_requests.returned_at, '%d-%b-%Y') LIKE ?", [$search]);
                });
            })->get();
    }

    public function headings(): array {
        return ['User Name', 'Roll Number', 'Year', 'Book Title', 'Book Code', 'Lost Date', 'Fine Amount'];
    }

    public function map($row): array {
        return [
            $row->user->name ?? 'N/A',
            $row->user->roll_number ?? 'N/A',
            $row->user->year->academic_year ?? 'N/A',
            $row->book->title ?? 'N/A',
            $row->book->code ?? 'N/A',
            $row->returned_at ? \Carbon\Carbon::parse($row->returned_at)->format('d-M-Y') : 'N/A',
            number_format($row->lost_fine) . ' MMK',
        ];
    }

    // Headings များကို A3 တွင် စတင်စေရန်
    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->collection();

                // 1. Main Title (A1 မှ G1 အထိ ပေါင်း၍ တင်ခြင်း)
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', 'Lost Books Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Filter Status (A2 မှ G2 အထိ ပေါင်း၍ တင်ခြင်း)
                $filterText = !empty($this->search) ? 'Filtered by: ' . $this->search : 'All Records';
                $sheet->mergeCells('A2:G2');
                $sheet->setCellValue('A2', $filterText);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. Total Row (Headings က A3 ဖြစ်၍ Data က A4 မှစမည်ဖြစ်ရာ row တွက်ချက်မှုအတွက် + 4 သုံးပါ)
                $totalRow = $data->count() + 4; 

                // Column F မှာ 'Total Lost Fine:' စာသား၊ Column G မှာ တန်ဖိုးနှင့် MMK ကို ထည့်ခြင်း
                $sheet->setCellValue('F' . $totalRow, 'Total Lost Fine:');
                $sheet->setCellValue('G' . $totalRow, number_format($data->sum('lost_fine')) . ' MMK');
                
                // စာသားကို Bold လုပ်ခြင်း
                $sheet->getStyle('F' . $totalRow . ':G' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}