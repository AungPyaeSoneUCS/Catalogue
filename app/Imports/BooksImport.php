<?php
namespace App\Imports;

use App\Models\Book;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class BooksImport implements ToModel, WithHeadingRow, WithValidation
{
    
    /**
     * Excel ထဲက ဒေတာအားလုံး (၁၀၀%) မမှားမယွင်း မှန်ကန်မှသာ 
     * ဤနေရာသို့ ရောက်လာပြီး Database ထဲသို့ အကုန်လုံး တစ်ပြိုင်နက် သွင်းပါမည်။
     */
    public function model(array $row)
    {
        // Excel ခေါင်းစဉ်များကို စံသတ်မှတ်ချက်ညီအောင် ပြောင်းလဲခြင်း
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $normalizedRow[Str::snake(trim(strtolower($key)))] = $value;
        }

        // အကယ်၍ အကြောင်းအလွတ် (Empty Row) ဖြစ်နေပါက Database ထဲမသွင်းဘဲ ကျော်ရန်
        if (!isset($normalizedRow['title']) || empty(trim($normalizedRow['title']))) {
            return null;
        }

        $coverImage = trim($normalizedRow['cover_image']);

        return new Book([
            'category_id'   => $normalizedRow['category_id'] ?? 1,
            'title'         => trim($normalizedRow['title']),
            'author'        => trim($normalizedRow['author']),
            'code'          => $normalizedRow['code'] ?? null,
            'press'         => $normalizedRow['press'] ?? null, // ဒီကောင်တစ်ခုပဲ Nullable ဖြစ်ခွင့်ရှိမည်
            'total_qty'     => (int)$normalizedRow['total_qty'],
            'available_qty' => (int)$normalizedRow['total_qty'],
            'content'       => $normalizedRow['content'] ?? null,
            'abstract'      => $normalizedRow['abstract'] ?? null,
            'cover_image'   => $coverImage,
        ]);
    }

    /**
     * တင်းကျပ်သော စည်းကမ်းချက်များ သတ်မှတ်ခြင်း (Validation Rules)
     * တစ်ခုခုမှားပါက Import လုပ်ခြင်းတစ်ခုလုံးကို ရပ်တန့်ပစ်မည်။
     */
    public function rules(): array
    {
        return [
            'title'         => ['required', 'string'],
            'author'        => ['required', 'string'],
            'total_qty'     => ['required', 'numeric', 'min:1'],
            'cover_image'   => ['required'],
            'category_id'   => ['required'],
            'content'       => ['required'],
            'abstract'      => ['required'],
            'code'          => ['required', 'unique:books,code'],
            'press'         => ['nullable'], // 🌟 press တစ်ခုတည်းကိုသာ ကွက်လပ်ထားခွင့်ပြုထားသည်
        ];
    }

    /**
     * Error တက်လာပါက ပြသမည့် စာသားများ
     */
    public function customValidationMessages()
    {
        return [
            'title.required' => __('validation.title_required'),
            '*.required'     => __('validation.required'),
            '*.numeric'      => __('validation.numeric'),
            'code.unique'    => __('validation.code_unique'),
        ];
    }
}