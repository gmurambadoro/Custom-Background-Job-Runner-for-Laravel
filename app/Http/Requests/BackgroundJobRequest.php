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
     * Get the validation rules definitions. This may be reused in other classes outside of Request
     * @return string[]
     */
    public static function getValidationRules(): array
    {
        return [
            'fqcn' => 'required|max:300',
            'method' => 'required|max:300',
            'is_static' => 'nullable|boolean',
            'arguments' => 'nullable|array',
            'priority' => 'integer|required',
            'delay' => 'integer|gte:0',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return self::getValidationRules();
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            // Preprocess 'arguments' by:
            //  - Splitting it into an array using newline characters as separators
            //  - Filtering out any empty strings from the array
            //  - Trimming and removing whitespace from each string in the array
            //  - Removing empty arguments
            'arguments' => $this->collect(explode(PHP_EOL, $this->input('arguments')))
                ->filter(fn(string $item) => $this->str($item)->trim()->length())
                ->toArray(),
        ]);
    }
}
