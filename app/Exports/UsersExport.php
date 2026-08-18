<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Year;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $request;
    protected $titleHeader = "Users List Report"; 

    public function __construct($request)
    {
        $this->request = $request;

        if ($this->request->has('year_semester') && $this->request->year_semester != '') {
            $yearData = Year::find($this->request->year_semester);
            if ($yearData) {
                $this->titleHeader = $yearData->academic_year;
            }
        }
    }

    /**
     * Database Query Fetching
     */
    public function query()
    {
        $query = User::query()
            ->select('users.*', 'years.academic_year as year_name')
            ->join('years', 'users.year_id', '=', 'years.id')
            ->where('users.is_approved', true);

        // Search Filter
        if ($this->request->has('search') && $this->request->search != '') {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('users.name', 'like', "%{$search}%")
                  ->orWhere('users.roll_number', 'like', "%{$search}%")
                  ->orWhere('users.email', 'like', "%{$search}%")
                  ->orWhere('users.created_at', 'like', "%{$search}%");
            });
        }

        // Dropdown Filter
        if ($this->request->has('year_semester') && $this->request->year_semester != '') {
            $query->where('users.year_id', $this->request->year_semester);
        }

        return $query->latest('users.created_at');
    }

    /**
     * ပြင်ဆင်ချက် - ဒေတာဇယားကြီးတစ်ခုလုံးကို Row 3 ကနေ စတင်ထည့်ခိုင်းလိုက်ခြင်း
     */
    public function startCell(): string
    {
        return 'A3';
    }

    /**
     * Table Headings (Row 3 နေရာမှာ ကွက်တိ ဝင်သွားပါမယ်)
     */
    public function headings(): array
    {
        return [
            'User Name',
            'Roll Number',
            'Year / Semester',
            'Phone',
            'Email',
            'Created At'
        ];
    }

    /**
     * Mapping Database Data to Columns
     */
    public function map($user): array
    {
        return [
            $user->name,
            $user->roll_number,
            $user->year_name ?? 'Unknown Year',
            $user->phone,
            $user->email,
            $user->created_at ? $user->created_at->format('M d, Y h:i A') : '',
        ];
    }

    /**
     * Layout Styling Setup
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Row 1 & 2: Title Header Styling (စာသား ခဲရောင်အမှောင်)
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 16, 
                    'color' => ['rgb' => '444444']
                ]
            ],
            // ပြင်ဆင်ချက် - Row 3: Table Headings ကို ခဲရောင်နောက်ခံ၊ สာသားအဖြူရောင် ထည့်သွင်းခြင်း
            3 => [
                'font' => [
                    'bold' => true, 
                    'size' => 14,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '808080'] // Solid Grey Background
                ]
            ],
        ];
    }

    /**
     * AfterSheet Event
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ၁။ ပြင်ဆင်ချက် - Column A1 ကနေ F2 (နှစ်ကြောင်းစာ) အထိကို ကျယ်ကျယ်ဝန်းဝန်း Merge လုပ်လိုက်မယ်
                $sheet->mergeCells('A1:F2');

                // ၂။ Merge ထားတဲ့ နေရာမှာ ခေါင်းစဉ်စာသား ရိုက်ထည့်မယ်
                $sheet->setCellValue('A1', $this->titleHeader);

                // ၃။ စာသားကို ဒေါင်လိုက်အလယ်၊ ဘယ်ဘက်ကပ် (Vertical Center, Horizontal Left) ညှိမယ်
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // ၄။ Row တစ်ခုချင်းစီရဲ့ အမြင့် (Height) ကို သတ်မှတ်မယ်
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(25);
                $sheet->getRowDimension('3')->setRowHeight(28); // Heading Row

                // ၅။ ကော်လံ (Column) Width အားလုံးကို စာသားအရှည်အလိုက် Auto ချဲ့ပေးမယ်
                foreach (range('A', 'F') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}