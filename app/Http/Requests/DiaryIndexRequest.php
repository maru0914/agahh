<?php

namespace App\Http\Requests;

use App\ValueObjects\YearMonth;
use Illuminate\Foundation\Http\FormRequest;

class DiaryIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'yearMonth' => 'nullable|date_format:Ym',
        ];
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        $yearMonth = $this->input('yearMonth');
        $this->merge([
            'yearMonth' => $yearMonth ? YearMonth::fromString($yearMonth) : YearMonth::current(),
        ]);
    }
}
