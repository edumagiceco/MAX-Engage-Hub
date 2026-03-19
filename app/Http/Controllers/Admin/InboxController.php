<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Contracts\View\View;

class InboxController extends Controller
{
    public function index(): View
    {
        $statusPriority = implode(' ', [
            "CASE status",
            "WHEN 'new' THEN 0",
            "WHEN 'in_review' THEN 1",
            "WHEN 'contacted' THEN 2",
            "WHEN 'nurturing' THEN 3",
            'ELSE 99 END',
        ]);

        $customers = Customer::query()
            ->with('latestActivity')
            ->whereIn('status', ['new', 'in_review', 'contacted', 'nurturing'])
            ->orderByRaw($statusPriority)
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
