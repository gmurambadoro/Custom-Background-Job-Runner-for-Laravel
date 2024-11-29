<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BackgroundJobRequest extends FormRequest
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
            'fqcn' => 'required|max:300',
            'method' => 'required|max:300',
            'is_static' => 'nullable|boolean',
            'arguments' => 'nullable|array',
            'priority' => 'integer|required',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'arguments' => $this->collect(explode(PHP_EOL, $this->input('arguments')))
                ->filter(fn(string $item) => $this->str($item)->trim()->length())
                ->toArray(),
        ]);
    }
}
