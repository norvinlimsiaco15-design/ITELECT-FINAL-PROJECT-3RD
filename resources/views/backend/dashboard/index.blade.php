@extends('adminlte::page')

@section('title', 'OroTrack Dashboard')

@section('content_header')
    <h1>OroTrack Inventory Dashboard</h1>
    <p class="text-muted mb-0">Oro Construction Soil and Rock, Tagum City, Davao del Norte</p>
    @if (session('success'))
        <div class="alert alert-success alert-dismissable mt-2">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $materials->count() }}</h3>
                    <p>Materials</p>
                </div>
                <div class="icon"><i class="fas fa-cubes"></i></div>
                <a href="{{ route('service.index') }}" class="small-box-footer">Inventory monitor <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($totalValue, 2) }}</h3>
                    <p>Total inventory value</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
                <a href="{{ route('reports.index') }}" class="small-box-footer">Valuation report <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $lowStock->count() }}</h3>
                    <p>Low-stock items</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                <a href="{{ route('reports.index') }}" class="small-box-footer">Low-stock list <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $recentTransactions->count() }}</h3>
                    <p>Recent movements shown</p>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
                <a href="{{ route('stock-transaction.index') }}" class="small-box-footer">Transaction history <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Stock levels</h3></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Material</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Min</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($materials as $material)
                                <tr>
                                    <td>{{ $material->title }}</td>
                                    <td>{{ $material->category->title ?? 'N/A' }}</td>
                                    <td>{{ number_format($material->quantity, 2) }} {{ $material->unit }}</td>
                                    <td>{{ number_format($material->min_stock_level, 2) }}</td>
                                    <td>
                                        @if ($material->isLowStock())
                                            <span class="badge badge-warning">Low stock</span>
                                        @else
                                            <span class="badge badge-success">Available</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5">No materials recorded yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Recent transactions</h3></div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Material</th>
                                <th>Type</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td>{{ $transaction->service->title ?? 'N/A' }}</td>
                                    <td>{{ $transaction->typeLabel() }}</td>
                                    <td>{{ number_format($transaction->quantity, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">No stock movements yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
