<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\StoresImages;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use StoresImages;

    public function __construct()
    {
        $this->resourcePermissions('brand');
    }

    public function index(Request $request): View
    {
        $brands = Brand::query()
            ->withCount('products')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%"))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.form', ['brand' => new Brand()]);
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        $this->save(new Brand(), $request);

        return redirect()->route('admin.brands.index')->with('success', __('admin.saved'));
    }

    public function edit(Brand $brand): View
    {
        return view('admin.brands.form', compact('brand'));
    }

    public function update(BrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->save($brand, $request);

        return redirect()->route('admin.brands.index')->with('success', __('admin.saved'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->deleteImage($brand->logo);
        $brand->delete();

        return back()->with('success', __('admin.deleted'));
    }

    private function save(Brand $brand, BrandRequest $request): void
    {
        $brand->fill($request->safe()->except(['logo', 'remove_logo']));
        $brand->logo = $this->uploadedImage($request, 'logo', $brand->logo, 'brands');
        $brand->save();
    }
}
