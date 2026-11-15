<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->resourcePermissions('customer');
    }

    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->when($request->query('search'), fn ($query, $search) => $query
                ->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'orders' => fn ($query) => $query->latest()->withCount('items'),
            'addresses.deliveryArea',
        ]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Reset the customer's password (there is no self-service reset without SMS verification).
     */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate(
            ['password' => ['required', 'string', 'min:6', 'max:100']],
            [],
            ['password' => __('admin.customers.new_password')],
        );

        $customer->forceFill([
            'password' => Hash::make($request->input('password')),
            'remember_token' => null,
        ])->save();

        return back()->with('success', __('admin.customers.password_reset'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', __('admin.deleted'));
    }
}
