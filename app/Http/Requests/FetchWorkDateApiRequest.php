<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchWorkDateApiRequest extends FormRequest
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
            'year' => ['required', 'string'],
            'month' => ['required', 'string'],
        ];
    }

    /**
     * 年取得
     *
     * @return string
     */
    public function getYear(): string
    {
        return $this->query('year');
    }

    /**
     * 取得月
     *
     * @return string
     */
    public function getMonth(): string
    {
        return $this->query('month');
    }
}
