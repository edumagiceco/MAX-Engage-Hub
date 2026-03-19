<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerNoteRequest;
use App\Http\Requests\StoreFollowUpTaskRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $status = $request->string('status')->toString();

        $query = Customer::query()->with('latestActivity')->latest('updated_at');

        if ($search !== '') {
            $query->where(function ($builder) use ($search): void {
                $builder
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        return view('admin.customers.index', [
            'customers' => $query->paginate(15)->withQueryString(),
            'filters' => compact('search', 'status'),
            'statusOptions' => Customer::STATUS_OPTIONS,
        ]);
    }

    public function show(Customer $customer): View
    {
        $customer->load([
            'activities' => fn ($query) => $query->latest(),
            'notes.user',
            'followUpTasks.user',
        ]);

        return view('admin.customers.show', [
            'customer' => $customer,
            'statusOptions' => Customer::STATUS_OPTIONS,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        $customer->activities()->create([
            'activity_type' => 'status_changed',
            'source' => 'admin_console',
            'title' => '리드 상태가 변경되었습니다.',
            'payload_json' => ['status' => $customer->status],
        ]);

        return back()->with('status', '고객 상태를 업데이트했습니다.');
    }

    public function storeNote(StoreCustomerNoteRequest $request, Customer $customer): RedirectResponse
    {
        $note = $customer->notes()->create([
            'user_id' => $request->user()->id,
            'note' => $request->validated('note'),
        ]);

        $customer->activities()->create([
            'activity_type' => 'note_added',
            'source' => 'admin_console',
            'title' => '운영 메모가 추가되었습니다.',
            'payload_json' => ['note_id' => $note->id],
        ]);

        return back()->with('status', '운영 메모를 저장했습니다.');
    }

    public function storeTask(StoreFollowUpTaskRequest $request, Customer $customer): RedirectResponse
    {
        $task = $customer->followUpTasks()->create([
            'user_id' => $request->user()->id,
            'title' => $request->validated('title'),
            'note' => $request->validated('note'),
            'due_date' => $request->validated('due_date'),
            'status' => 'pending',
        ]);

        $customer->activities()->create([
            'activity_type' => 'follow_up_scheduled',
            'source' => 'admin_console',
            'title' => '후속 액션이 등록되었습니다.',
            'payload_json' => [
                'task_id' => $task->id,
                'due_date' => optional($task->due_date)->toDateString(),
            ],
        ]);

        return back()->with('status', '후속 액션을 등록했습니다.');
    }
}
