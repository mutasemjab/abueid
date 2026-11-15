<?php

namespace App\Http\Requests\Admin;

use App\Support\ContentSchema;

class SettingRequest extends AdminRequest
{
    public static function fields(): array
    {
        return collect(config('settings'))->flatMap(fn (array $group) => $group['fields'])->all();
    }

    public function schema(): ContentSchema
    {
        return new ContentSchema(self::fields(), 'settings');
    }

    public function rules(): array
    {
        return $this->schema()->rules();
    }

    public function attributes(): array
    {
        return $this->schema()->attributes();
    }
}
