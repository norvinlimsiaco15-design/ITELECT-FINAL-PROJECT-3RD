@extends('adminlte::page')

@section('title', 'Transaction History')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Transaction History</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('stock-transaction.create') }}" class="btn btn-primary btn-sm">+ Record Movement</a>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="GET" class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label>Search</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Material, supplier, customer, notes">
                </div>
                <div class="form-group col-md-2">
                    <label>Type</label>
                    <select name="type" class="form-control">
                        <option value="">All</option>
                        <option value="in" @selected(request('type') === 'in')>Stock In</option>
                        <option value="out" @selected(request('type') === 'out')>Stock Out</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Material</label>
                    <select name="service_id" class="form-control">
                        <option value="">All materials</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" @selected((string) request('service_id') === (string) $material->id)>{{ $material->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2">
                    <label>From</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group col-md-2">
                    <label>To</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="form-group col-md-12">
                    <button class="btn btn-secondary">Filter</button>
                    <a href="{{ route('stock-transaction.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>

            <table id="myTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Material</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Balance after</th>
                        <th>Supplier / Customer</th>
                        <th>Recorded by</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                            <td>{{ $transaction->service->title ?? 'N/A' }}</td>
                            <td>{{ $transaction->typeLabel() }}</td>
                            <td>{{ number_format($transaction->quantity, 2) }} {{ $transaction->service->unit ?? '' }}</td>
                            <td>{{ number_format($transaction->balance_after, 2) }}</td>
                            <td>{{ $transaction->party_name ?: '—' }}</td>
                            <td>{{ $transaction->user->name ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({ responsive: true });
        });
    </script>
@stop
