<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FetchWorkShowApiRequest extends FormRequest
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
            'day' => ['required', 'string'],
        ];
    }

    /**
     * 年を取得
     *
     * @return string
     */
    public function getYear(): string
    {
        return $this->query('year');
    }

    /**
     * 月を取得
     *
     * @return string
     */
    public function getMonth(): string
    {
        return $this->query('month');
    }

    /**
     * 日付を取得
     *
     * @return string
     */
    public function getDay(): string
    {
        return $this->query('day');
    }
}
