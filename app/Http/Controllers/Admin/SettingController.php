<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Services\MediaService;
use App\Services\SiteSettings;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function __construct(private readonly SiteSettings $settings)
    {
        $this->middleware($this->perm('setting-edit'));
    }

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'groups' => config('settings'),
            'values' => $this->settings->all(),
        ]);
    }

    public function update(SettingRequest $request, MediaService $media): RedirectResponse
    {
        $values = $request->schema()->apply($this->settings->all(), $request, $media, 'settings');

        $this->settings->save(array_intersect_key($values, SettingRequest::fields()));

        return back()->with('success', __('admin.saved'));
    }
}
