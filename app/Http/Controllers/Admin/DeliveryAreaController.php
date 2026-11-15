<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DeliveryAreaRequest;
use App\Models\DeliveryArea;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DeliveryAreaController extends Controller
{
    public function __construct()
    {
        $this->resourcePermissions('area');
    }

    public function index(): View
    {
        $areas = DeliveryArea::query()->ordered()->paginate(30);

        return view('admin.delivery-areas.index', compact('areas'));
    }

    public function create(): View
    {
        return view('admin.delivery-areas.form', ['area' => new DeliveryArea()]);
    }

    public function store(DeliveryAreaRequest $request): RedirectResponse
    {
        DeliveryArea::query()->create($request->validated());

        return redirect()->route('admin.delivery-areas.index')->with('success', __('admin.saved'));
    }

    public function edit(DeliveryArea $deliveryArea): View
    {
        return view('admin.delivery-areas.form', ['area' => $deliveryArea]);
    }

    public function update(DeliveryAreaRequest $request, DeliveryArea $deliveryArea): RedirectResponse
    {
        $deliveryArea->update($request->validated());

        return redirect()->route('admin.delivery-areas.index')->with('success', __('admin.saved'));
    }

    public function destroy(DeliveryArea $deliveryArea): RedirectResponse
    {
        $deliveryArea->delete();

        return back()->with('success', __('admin.deleted'));
    }
}
