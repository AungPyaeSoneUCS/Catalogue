<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ReturnedListExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $requests;
    protected $search;

    public function __construct($requests, $search = null) 
    {
        $this->requests = $requests;
        $this->search = $search;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection() 
    {
        return $this->requests->map(function($req) {
            return [
                $req->user->name ?? 'N/A',
                $req->user->roll_number ?? 'N/A',
                $req->user->year->academic_year ?? 'N/A', 
                $req->book->title ?? 'N/A',
                $req->book->code ?? 'N/A',
                $req->returned_at ? Carbon::parse($req->returned_at)->tz('Asia/Yangon')->format('d-m-Y') : 'N/A',
            ];
        });
    }

    public function headings(): array 
    {
        return ['User Name', 'Roll Number', 'Academic Year', 'Book Title', 'Book Code', 'Returned Date'];
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

                // Main Title (A1 မှ F1 အထိ)
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'Returned Books List Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Filter Title (A2 မှ F2 အထိ)
                $filterText = !empty($this->search) ? 'Filtered by: ' . $this->search : 'All Records';
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', $filterText);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}