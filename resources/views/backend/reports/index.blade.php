@extends('adminlte::page')

@section('title', 'OroTrack Inventory Reports')

@section('content_header')
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1>OroTrack Inventory Reports</h1>
            <p class="text-muted mb-0">Oro Construction Soil and Rock, Tagum City, Davao del Norte</p>
            <p class="text-muted mb-0">Inventory value is recorded quantity × unit price. Figures come from entered stock records.</p>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm d-print-none" onclick="window.print()">Print</button>
    </div>
@stop

@section('content')
    <div class="d-none d-print-block mb-3">
        <img src="{{ asset('images/orotrack-logo.svg') }}" alt="OroTrack" width="48" height="48">
        <strong>OroTrack Inventory Reports</strong>
        <div>© 2026 OroTrack: A Construction Soil and Rock Inventory and Material Management System. All rights reserved.</div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total inventory value</span>
                    <span class="info-box-number">{{ number_format($totalValue, 2) }}</span>
                    <small>Quantity × unit price</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-cubes"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Materials tracked</span>
                    <span class="info-box-number">{{ $materials->count() }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Low-stock items</span>
                    <span class="info-box-number">{{ $lowStock->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Inventory valuation summary</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Quantity</th>
                        <th>Unit price</th>
                        <th>Inventory value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materials as $material)
                        <tr>
                            <td>{{ $material->title }}</td>
                            <td>{{ $material->category->title ?? 'N/A' }}</td>
                            <td>{{ $material->unit }}</td>
                            <td>{{ number_format($material->quantity, 2) }}</td>
                            <td>{{ number_format($material->price, 2) }}</td>
                            <td>{{ number_format($material->inventoryValue(), 2) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <th colspan="5" class="text-right">Total</th>
                        <th>{{ number_format($totalValue, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Stock levels</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Available qty</th>
                        <th>Minimum</th>
                        <th>Availability</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($materials as $material)
                        <tr>
                            <td>{{ $material->title }}</td>
                            <td>{{ number_format($material->quantity, 2) }} {{ $material->unit }}</td>
                            <td>{{ number_format($material->min_stock_level, 2) }}</td>
                            <td>
                                @if ($material->isLowStock())
                                    <span class="badge badge-warning">Low stock</span>
                                @else
                                    <span class="badge badge-success">In stock</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Low-stock list</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Material</th>
                        <th>Quantity</th>
                        <th>Minimum stock level</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lowStock as $material)
                        <tr>
                            <td>{{ $material->title }}</td>
                            <td>{{ number_format($material->quantity, 2) }} {{ $material->unit }}</td>
                            <td>{{ number_format($material->min_stock_level, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">No materials are currently at or below minimum stock.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Transaction records</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="form-row">
                <div class="form-group col-md-3">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="">All</option>
                        <option value="in" @selected(request('type') === 'in')>Stock In</option>
                        <option value="out" @selected(request('type') === 'out')>Stock Out</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group col-md-3">
                    <label>To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="form-group col-md-3">
                    <label>&nbsp;</label>
                    <button class="btn btn-secondary btn-block">Filter</button>
                </div>
            </form>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Supplier / Customer</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->service->title ?? 'N/A' }}</td>
                            <td>{{ $transaction->typeLabel() }}</td>
                            <td>{{ number_format($transaction->quantity, 2) }}</td>
                            <td>{{ $transaction->party_name ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5">No transactions in this range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
