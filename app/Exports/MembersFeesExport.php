<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MembersFeesExport implements FromCollection, WithHeadings, WithCustomStartCell, WithEvents
{
    protected $members, $totalFees, $search;

    public function __construct($members, $totalFees, $search = null)
    {
        $this->members = $members;
        $this->totalFees = $totalFees;
        $this->search = $search;
    }

    public function collection()
    {
        $data = $this->members->map(function ($member) {
            // Payment method + account_type ကို ပေါင်းစပ်ဖော်ပြခြင်း
            $paymentMethod = $member->payment 
                ? $member->payment->account_name . ' (' . $member->payment->account_number . ') ' . $member->payment->account_type
                : 'N/A';

            return [
                $member->created_at ? $member->created_at->format('d-m-Y') : 'N/A',
                $member->user->name ?? 'N/A',
                $member->user->roll_number ?? 'N/A',
                $member->user->year->academic_year ?? 'N/A',
                $paymentMethod,
                number_format($member->fee) . ' MMK', // Fee တွင် MMK ထည့်ခြင်း
            ];
        });

        // Excel ၏ အောက်ဆုံးတွင် Total ထည့်ခြင်း
        $data->push(['', '', '', '', 'TOTAL', number_format($this->totalFees) . ' MMK']);

        return $data;
    }

    public function headings(): array
    {
        return ['Date', 'Name', 'Roll Number', 'Year', 'Payment Method', 'Fee'];
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
                $dataCount = $this->members->count();

                // 1. Main Title (A1 မှ F1 အထိ ပေါင်း၍ တင်ခြင်း)
                $sheet->mergeCells('A1:F1');
                $sheet->setCellValue('A1', 'Members Fees Report');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 2. Filter Status / Subtitle (A2 မှ F2 အထိ)
                $filterText = !empty($this->search) ? 'Filtered by: ' . $this->search : 'All Records';
                $sheet->mergeCells('A2:F2');
                $sheet->setCellValue('A2', $filterText);
                $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // 3. Total Row ကို Bold လုပ်ရန် (Headings က A3 ဖြစ်၍ Data က A4 မှစမည်၊ Total က data နောက်ဆုံး row ဖြစ်ပါမည်)
                $totalRow = $dataCount + 4; 
                $sheet->getStyle('E' . $totalRow . ':F' . $totalRow)->getFont()->setBold(true);
            },
        ];
    }
}