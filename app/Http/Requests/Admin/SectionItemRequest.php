<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use App\Support\ContentSchema;

class SectionItemRequest extends AdminRequest
{
    public function schema(): ContentSchema
    {
        return new ContentSchema($this->section()->itemFields());
    }

    public function rules(): array
    {
        return [
            ...$this->schema()->rules(),
            ...$this->publishingRules(),
        ];
    }

    public function attributes(): array
    {
        return $this->schema()->attributes();
    }

    private function section(): Section
    {
        return $this->route('section');
    }
}
