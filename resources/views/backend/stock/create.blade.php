@extends('adminlte::page')

@section('title', 'Record Stock Movement')

@section('content_header')
    <h1>Record Stock-In / Stock-Out / Adjustment</h1>
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <form method="POST" action="{{ route('stock-transaction.store') }}">
            @csrf
            <div class="card-body">
                <div class="form-group">
                    <label>Material</label>
                    <select name="service_id" class="form-control" required>
                        <option value="">Select material</option>
                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}" @selected(old('service_id') == $material->id)>
                                {{ $material->title }} — available: {{ number_format($material->quantity, 2) }} {{ $material->unit }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Transaction type</label>
                    <select name="type" id="transaction_type" class="form-control" required>
                        <option value="in" @selected(old('type', 'in') === 'in')>Stock In</option>
                        <option value="out" @selected(old('type') === 'out')>Stock Out</option>
                        <option value="adjustment" @selected(old('type') === 'adjustment')>Adjustment</option>
                    </select>
                </div>
                <div class="form-group" id="adjustment_direction_group">
                    <label>Adjustment direction</label>
                    <select name="adjustment_direction" class="form-control">
                        <option value="increase" @selected(old('adjustment_direction', 'increase') === 'increase')>Increase stock</option>
                        <option value="decrease" @selected(old('adjustment_direction') === 'decrease')>Decrease stock</option>
                    </select>
                    <small class="text-muted">A decrease cannot take the recorded quantity below zero.</small>
                </div>
                <div class="form-group">
                    <label>Quantity</label>
                    <input type="number" step="0.001" min="0.001" name="quantity" class="form-control" value="{{ old('quantity') }}" required>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="transaction_date" class="form-control" value="{{ old('transaction_date', now()->toDateString()) }}" required>
                </div>
                <div class="form-group" id="party_group">
                    <label id="party_label">Supplier name</label>
                    <input type="text" name="party_name" class="form-control" value="{{ old('party_name') }}" placeholder="Required for stock-in and stock-out">
                    <small class="text-muted" id="party_help">Stock-in records the supplier. Stock-out records the customer.</small>
                </div>
                <div class="form-group" id="party_details_group">
                    <label id="party_details_label">Supplier details</label>
                    <textarea name="party_details" class="form-control" rows="3">{{ old('party_details') }}</textarea>
                </div>
                <div class="form-group">
                    <label>Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Save transaction</button>
                <a href="{{ route('stock-transaction.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@stop

@section('js')
    <script>
        function syncTransactionFields() {
            const type = document.getElementById('transaction_type').value;
            const adjustment = document.getElementById('adjustment_direction_group');
            const party = document.getElementById('party_group');
            const details = document.getElementById('party_details_group');
            const partyLabel = document.getElementById('party_label');
            const detailsLabel = document.getElementById('party_details_label');

            const isAdjustment = type === 'adjustment';
            adjustment.style.display = isAdjustment ? '' : 'none';
            party.style.display = isAdjustment ? 'none' : '';
            details.style.display = isAdjustment ? 'none' : '';
            adjustment.querySelector('select').disabled = !isAdjustment;
            party.querySelector('input').disabled = isAdjustment;
            details.querySelector('textarea').disabled = isAdjustment;

            if (type === 'out') {
                partyLabel.textContent = 'Customer name';
                detailsLabel.textContent = 'Customer details';
            } else {
                partyLabel.textContent = 'Supplier name';
                detailsLabel.textContent = 'Supplier details';
            }
        }

        document.getElementById('transaction_type').addEventListener('change', syncTransactionFields);
        syncTransactionFields();
    </script>
@stop
