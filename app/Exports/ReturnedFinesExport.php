<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReturnedFinesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $fines;
    protected $search;

    public function __construct($fines, $search = null)
    {
        $this->fines = $fines;
        $this->search = $search;
    }

    public function collection()
    {
        $data = $this->fines->map(function($f) {
            return [
                $f->user->name ?? 'N/A',
                $f->user->roll_number ?? 'N/A',
                $f->user->year->academic_year ?? 'N/A', 
                $f->book->title ?? 'N/A',
                $f->book->code ?? 'N/A',
                number_format($f->fine_amount) . ' MMK',
                $f->due_at ? Carbon::parse($f->due_at)->tz('Asia/Yangon')->format('d-m-Y') : 'N/A',
                $f->returned_at ? Carbon::parse($f->returned_at)->tz('Asia/Yangon')->format('d-m-Y') : 'N/A',
            ];
        });

        $totalFine = $this->fines->sum('fine_amount');
        
        // Column (၈) ခုအတွက် 'TOTAL FINE' နေရာကို အတိအကျညှိထားပါသည် (Column ၅ ခုမြောက် သို့မဟုတ် ၆ ခုမြောက်)
        $data->push([
            '', '', '', '', 'TOTAL FINE:', number_format($totalFine) . ' MMK', '', ''
        ]);

        return $data;
    }

    public function headings(): array
    {
        return ['User Name', 'Roll Number', 'Academic Year', 'Book Title', 'Book Code', 'Fine Amount', 'Overdue At', 'Returned At'];
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Main Title (A1 မှ H1 အထိ)
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Returned Fines Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Filter Title (A2 မှ H2 အထိ)
                $filterText = !empty($this->search) ? 'Filtered by: ' . $this->search : 'All Records';
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', $filterText);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}