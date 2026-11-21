<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Storefront catalogue filters. List filters (cat, brand, ids) are comma-separated slugs.
 */
class ProductIndexRequest extends FormRequest
{
    public const PER_PAGE = 24;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cat' => ['nullable', 'string', 'max:2000'],
            'brand' => ['nullable', 'string', 'max:2000'],
            'ids' => ['nullable', 'string', 'max:5000'],
            'q' => ['nullable', 'string', 'max:100'],
            'max' => ['nullable', 'numeric', 'min:0'],
            'in_stock' => ['nullable', 'boolean'],
            'sort' => ['nullable', 'in:feat,low,high'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:48'],
        ];
    }

    /** @return list<string> */
    public function list(string $key, int $limit = 100): array
    {
        return array_slice(array_values(array_filter(array_map('trim', explode(',', (string) $this->validated($key))))), 0, $limit);
    }

    public function perPage(): int
    {
        return (int) ($this->validated('per_page') ?? self::PER_PAGE);
    }
}
