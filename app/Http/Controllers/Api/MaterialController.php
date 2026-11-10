<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Models\Service;
use App\Services\StockMovementService;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Service::with('category')->orderBy('title')->get();

        return MaterialResource::collection($materials);
    }

    public function show(Service $material)
    {
        return new MaterialResource($material->load('category'));
    }

    public function store(MaterialRequest $request, StockMovementService $stock)
    {
        $data = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $data['title']);
        $data['status'] = $request->boolean('status', true);
        $data['excerpt'] = $data['excerpt'] ?? '';

        $material = DB::transaction(function () use ($data, $stock, $request) {
            $material = Service::create($data);
            $stock->recordOpeningBalance($material, $request->user()->id);

            return $material;
        });

        return (new MaterialResource($material->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    public function update(MaterialRequest $request, Service $material)
    {
        $data = $request->validated();
        unset($data['quantity']);
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? $material->slug, $material->id);
        $data['status'] = $request->boolean('status', false);
        $data['excerpt'] = $data['excerpt'] ?? '';

        $material->update($data);

        return new MaterialResource($material->fresh()->load('category'));
    }

    public function destroy(Service $material)
    {
        $material->delete();

        return response()->json(['message' => 'Material moved to trash.']);
    }

    protected function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = str($title)->slug()->toString() ?: 'material';
        $slug = $base;
        $i = 2;

        while (Service::withTrashed()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }
}
