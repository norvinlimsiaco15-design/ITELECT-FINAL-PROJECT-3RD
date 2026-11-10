<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\InventoryReportRequest;
use App\Http\Resources\InventoryReportResource;
use App\Models\InventoryReport;
use App\Models\Service;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function index(Request $request)
    {
        $scope = $request->string('scope')->toString() ?: 'mine';
        $query = InventoryReport::with(['user', 'items'])->latest('report_date')->latest('id');

        if ($scope === 'submissions') {
            abort_unless($request->user()->hasAnyRole(['admin', 'management']), 403);
            $query->where('status', 'submitted');
        } else {
            $query->where('user_id', $request->user()->id);
        }

        return InventoryReportResource::collection($query->get());
    }

    public function show(Request $request, InventoryReport $inventoryReport)
    {
        $this->authorizeView($request, $inventoryReport);

        return new InventoryReportResource($inventoryReport->load(['user', 'items']));
    }

    public function store(InventoryReportRequest $request)
    {
        abort_unless($request->user()->hasAnyRole(['admin', 'inventory_personnel']), 403);

        $submit = $request->boolean('submit');
        $report = InventoryReport::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'report_date' => $request->report_date,
            'notes' => $request->notes,
            'status' => $submit ? 'submitted' : 'draft',
            'submitted_at' => $submit ? now() : null,
        ]);

        $materials = Service::with('category')->orderBy('title')->get();
        foreach ($materials as $material) {
            $report->items()->create([
                'service_id' => $material->id,
                'material_name' => $material->title,
                'category_name' => $material->category?->title,
                'unit' => $material->unit,
                'quantity' => $material->quantity,
                'unit_price' => $material->price,
                'min_stock_level' => $material->min_stock_level,
                'line_value' => $material->inventoryValue(),
                'is_low_stock' => $material->isLowStock(),
            ]);
        }

        return (new InventoryReportResource($report->load(['user', 'items'])))
            ->response()
            ->setStatusCode(201);
    }

    public function submit(Request $request, InventoryReport $inventoryReport)
    {
        abort_unless($inventoryReport->user_id === $request->user()->id, 403);
        abort_unless($inventoryReport->status === 'draft', 422, 'This report is already submitted.');

        $inventoryReport->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return new InventoryReportResource($inventoryReport->load(['user', 'items']));
    }

    protected function authorizeView(Request $request, InventoryReport $report): void
    {
        if ($report->user_id === $request->user()->id) {
            return;
        }

        abort_unless(
            $request->user()->hasAnyRole(['admin', 'management']) && $report->status === 'submitted',
            403
        );
    }
}
