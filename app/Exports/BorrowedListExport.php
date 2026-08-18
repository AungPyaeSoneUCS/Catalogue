<?php

namespace App\Exports;

use App\Models\BorrowRequest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BorrowedListExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $search;

    public function __construct($search)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = BorrowRequest::with(['user', 'book'])
            ->join('users', 'borrow_requests.user_id', '=', 'users.id')
            ->join('books', 'borrow_requests.book_id', '=', 'books.id')
            ->join('years', 'users.year_id', '=', 'years.id')
            ->where('borrow_requests.status', 'borrowed')
            ->select('borrow_requests.*');

        if (!empty($this->search)) {
            $key = $this->search;
            $query->where(function($q) use ($key) {
                $q->whereAny([
                    'users.name',
                    'users.roll_number',
                    'books.title',
                    'books.code',
                    'years.academic_year'
                ], 'like', '%' . $key . '%')
                ->orWhereRaw("DATE_FORMAT(borrow_requests.due_at, '%d-%b-%Y') LIKE ?", ['%' . $key . '%'])
            ->orWhereRaw("DATE_FORMAT(borrow_requests.due_at, '%Y-%m-%d') LIKE ?", ['%' . $key . '%']);

            });
        }

        return $query->latest('borrow_requests.created_at');
    }

    public function startCell(): string { return 'A3'; }

    public function headings(): array
    {
        return ['User Name', 'Roll Number', 'Academic Year', 'Email', 'Phone', 'Book Title', 'Book Code', 'Borrowed At', 'Due Date'];
    }

    public function map($req): array
    {
        return [
            $req->user->name,
            $req->user->roll_number,
            $req->user->year->academic_year ?? 'N/A', // Academic Year ထည့်သွင်းထားသည်
            $req->user->email,
            $req->user->phone,
            $req->book->title,
            $req->book->code,
            $req->created_at->format('M d, Y'),
            $req->due_at ? \Carbon\Carbon::parse($req->due_at)->format('M d, Y') : '',
        ];
    }

    // public function styles(Worksheet $sheet)
    // {
    //     return [
    //         3 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D6EFD']]],
    //     ];
    // }
public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();
            
            // 1. Main Title (A1)
            $sheet->mergeCells('A1:I1');
            $sheet->setCellValue('A1', 'Active Borrowed Books Report');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // 2. Filter Title / Search Keyword (A2)
            $filterText = !empty($this->search) ? 'Filtered by: ' . $this->search : 'All Records';
            $sheet->mergeCells('A2:I2');
            $sheet->setCellValue('A2', $filterText);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // 3. Column Auto-size
            foreach (range('A', 'I') as $col) { 
                $sheet->getColumnDimension($col)->setAutoSize(true); 
            }
        },
    ];
}
public function styles(Worksheet $sheet)
{
    return [
        // Table Headings သည် ယခု Row 3 တွင်ရှိမည်
        3 => [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 
                'startColor' => ['rgb' => '0D6EFD']
            ]
        ],
    ];
}
    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function(AfterSheet $event) {
    //             $sheet = $event->sheet->getDelegate();
    //             // Merge A1 မှ I2 အထိ ချဲ့လိုက်ပါ
    //             $sheet->mergeCells('A1:I2');
    //             $sheet->setCellValue('A1', 'Active Borrowed Books Report');
    //             // Column A မှ I အထိ အလိုအလျောက် အကျယ်ချိန်ညှိပေးခြင်း
    //             foreach (range('A', 'I') as $col) { $sheet->getColumnDimension($col)->setAutoSize(true); }
    //         },
    //     ];
    // }
}