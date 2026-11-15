<?php

namespace App\Http\Requests\Front\Concerns;

use App\Support\Phone;

trait NormalizesPhone
{
    protected function prepareForValidation(): void
    {
        if ($this->has('phone')) {
            $this->merge(['phone' => Phone::normalize($this->input('phone'))]);
        }
    }

    protected function phoneRules(): array
    {
        return ['required', 'string', 'regex:'.Phone::PATTERN];
    }
}
