<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Contracts\View\View;

class InboxController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->with('latestActivity')
            ->whereIn('status', ['new', 'in_review', 'contacted', 'nurturing'])
            ->orderByRaw("FIELD(status, 'new', 'in_review', 'contacted', 'nurturing')")
            ->latest('updated_at')
            ->paginate(12);

        return view('admin.inbox.index', [
            'customers' => $customers,
            'summary' => [
                'new' => Customer::query()->where('status', 'new')->count(),
                'in_review' => Customer::query()->where('status', 'in_review')->count(),
                'contacted' => Customer::query()->where('status', 'contacted')->count(),
                'total' => Customer::query()->count(),
            ],
        ]);
    }
}
