<?php

namespace App\Exports;

use App\Models\Book;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BooksExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithCustomStartCell, WithEvents
{
    protected $request;
    protected $titleHeader = "Books Inventory Report"; // Default Title

    // 🎯 Controller ကပို့လိုက်တဲ့ $request ကို လက်ခံပြီး Dynamic Title စစ်ခြင်း
    public function __construct($request)
    {
        $this->request = $request;

        if ($this->request->has('category_id') && $this->request->category_id != '') {
            $categoryData = Category::find($this->request->category_id);
            if ($categoryData) {
                $this->titleHeader = "Books Report - " . $categoryData->name;
            }
        }
    }

    /**
     * 🎯 Database Query စနစ် (Search နှင့် Dropdown Filter များ အလုပ်လုပ်မည့်အပိုင်း)
     */
    public function query()
    {
        $query = Book::query()
            ->select('books.*', 'categories.name as category_name')
            ->join('categories', 'books.category_id', '=', 'categories.id');

        // ၁။ Search Filter (စာအုပ်အမည်၊ ကုဒ်၊ စာရေးဆရာ၊ ပုံနှိပ်တိုက် တို့ဖြင့် ရှာခြင်း)
        if ($this->request->has('search') && $this->request->search != '') {
            $search = $this->request->search;
            $query->where(function($q) use ($search) {
                $q->where('books.title', 'like', "%{$search}%")
                  ->orWhere('books.code', 'like', "%{$search}%")
                  ->orWhere('books.author', 'like', "%{$search}%")
                  ->orWhere('books.press', 'like', "%{$search}%")
                  ->orWhereRaw("DATE_FORMAT(books.created_at, '%d-%b-%Y') LIKE ?", ['%' . $search . '%']);
            });
        }

        // ၂။ Category Dropdown Filter
        if ($this->request->has('category_id') && $this->request->category_id != '') {
            $query->where('books.category_id', $this->request->category_id);
        }

        return $query->latest('books.created_at');
    }

    /**
     * ဒေတာဇယားကို Row 3 ကနေ စတင်ထည့်သွင်းခိုင်းခြင်း
     */
    public function startCell(): string
    {
        return 'A3';
    }

    /**
     * Table Headings (Row 3 နေရာတွင် ပေါ်မည့် Column ခေါင်းစဉ် ၉ ခု)
     */
    public function headings(): array
    {
        return [
            'Book Code',
            'Book Title',
            'Subject',
            'Author',
            'Press',
            'Total Qty',
            'Available Qty',
            'Table of Contents',
            'Created At'
        ];
    }

    /**
     * Database ဒေတာများကို Excel Columns နှင့် ချိတ်ဆက်ခြင်း
     */
    public function map($book): array
    {
        return [
            $book->code ?? 'N/A',
            $book->title,
            $book->category_name ?? 'No Category',
            $book->author ?? 'Unknown',
            $book->press ?? 'N/A',
            $book->total_qty,
            $book->available_qty,
            $book->content ? strip_tags($book->content) : 'N/A', // HTML tags များ ရှင်းထုတ်ရန်
            $book->created_at ? $book->created_at->format('d-M-Y') : '',
        ];
    }

    /**
     * Layout Styling (ရောင်စုံ ဒီဇိုင်းဆင်ခြင်း)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Row 1 & 2: Main Title Header Styling
            1 => [
                'font' => [
                    'bold' => true, 
                    'size' => 16, 
                    'color' => ['rgb' => '444444']
                ]
            ],
            // Row 3: Table Headings (Grey Background, White Text)
            3 => [
                'font' => [
                    'bold' => true, 
                    'size' => 13,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '808080']
                ]
            ],
        ];
    }

    /**
     * AfterSheet Event (Merge လုပ်ခြင်း၊ Column Width Auto ချဲ့ခြင်း)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Column A1 မှ I2 အထိကို မာ့ချ် (Merge) လုပ်ပြီး ခေါင်းစဉ်ထည့်ခြင်း
                $sheet->mergeCells('A1:I2');
                $sheet->setCellValue('A1', $this->titleHeader);

                // စာသား တန်းညှိခြင်း
                $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                // Row အမြင့်များ သတ်မှတ်ခြင်း
                $sheet->getRowDimension('1')->setRowHeight(25);
                $sheet->getRowDimension('2')->setRowHeight(25);
                $sheet->getRowDimension('3')->setRowHeight(30); 

                // Column Width များကို Auto ချဲ့ပေးခြင်း (A မှ I အထိ)
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}