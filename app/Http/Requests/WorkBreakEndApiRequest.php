<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkBreakEndApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'remarks' => ['string', 'nullable'],
            'break_end' => ['required', 'string'],
        ];
    }

    /**
     * 備考取得
     *
     * @return string|null
     */
    public function getRemarks(): ?string
    {
        return $this->input('remarks');
    }

    /**
     * 休憩終了日時取得
     *
     * @return string
     */
    public function getBreakEnd(): string
    {
        return $this->input('break_end');
    }
}
