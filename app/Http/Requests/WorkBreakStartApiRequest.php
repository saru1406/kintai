<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkBreakStartApiRequest extends FormRequest
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
            'break_start' => ['required', 'string'],
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
     * 休憩開始日時取得
     *
     * @return string
     */
    public function getBreakStart(): string
    {
        return $this->input('break_start');
    }
}
