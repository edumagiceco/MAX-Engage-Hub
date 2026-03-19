<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseLibraryRequest;
use App\Models\CaseLibrary;
use App\Models\RecommendationResult;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaseLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->string('q')),
            'industry' => trim((string) $request->string('industry')),
            'department' => trim((string) $request->string('department')),
            'solution_type' => trim((string) $request->string('solution_type')),
            'status' => trim((string) $request->string('status')),
        ];

        $query = CaseLibrary::query()->latest();

        if ($filters['q'] !== '') {
            $query->where(function ($builder) use ($filters): void {
                $builder
                    ->where('title', 'like', "%{$filters['q']}%")
                    ->orWhere('problem', 'like', "%{$filters['q']}%")
                    ->orWhere('summary', 'like', "%{$filters['q']}%");
            });
        }

        foreach (['industry', 'department', 'solution_type', 'status'] as $filterKey) {
            if ($filters[$filterKey] !== '') {
                $query->where($filterKey, $filters[$filterKey]);
            }
        }

        return view('admin.cases.index', [
            'cases' => $query->paginate(12)->withQueryString(),
            'filters' => $filters,
            'solutionTypes' => RecommendationResult::TYPE_OPTIONS,
            'statusOptions' => ['approved', 'draft'],
        ]);
    }

    public function store(StoreCaseLibraryRequest $request): RedirectResponse
    {
        CaseLibrary::query()->create([
            'title' => $request->validated('title'),
            'industry' => $request->validated('industry'),
            'department' => $request->validated('department'),
            'problem' => $request->validated('problem'),
            'solution_type' => $request->validated('solution_type'),
            'summary' => $request->validated('summary'),
            'outcome' => $request->validated('outcome'),
            'metrics_json' => $this->parseMetrics($request->validated('metrics_input')),
            'status' => $request->validated('status'),
        ]);

        return redirect()
            ->route('admin.cases.index')
            ->with('status', '사례 라이브러리 항목을 등록했습니다.');
    }

    private function parseMetrics(?string $input): ?array
    {
        $lines = collect(preg_split('/\r\n|\r|\n/', (string) $input))
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        if ($lines->isEmpty()) {
            return null;
        }

        return $lines->map(function (string $line) {
            [$key, $value] = array_pad(explode(':', $line, 2), 2, null);

            return [
                'label' => trim($key),
                'value' => trim((string) ($value ?? '')),
            ];
        })->all();
    }
}
