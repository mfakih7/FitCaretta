<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sales\Customer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $customers = Customer::query()
            ->when($q !== '', fn ($query) => $query->where('full_name', 'like', '%' . $q . '%'))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers', 'q'));
    }
}

