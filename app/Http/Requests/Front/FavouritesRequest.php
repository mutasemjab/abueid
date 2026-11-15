<?php

namespace App\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class FavouritesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['present', 'array', 'max:500'],
            'ids.*' => ['string', 'max:100'],
        ];
    }
}
