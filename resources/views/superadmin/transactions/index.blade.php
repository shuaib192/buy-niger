@extends('layouts.app')

@section('title', 'Transaction History')
@section('page_title', 'Transaction History')

@section('sidebar')
    @include('superadmin.partials.sidebar')
@endsection

@section('content')
<div class="row g-4">
    <div class="col-12">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <h3>All Transactions</h3>
                <p class="text-muted mb-0">Clear flow of orders (Income) and vendor payouts (Expense).</p>
            </div>
            <div class="dashboard-card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Reference</th>
                                <th>User / Vendor</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paginatedTransactions as $transaction)
                                <tr>
                                    <td class="font-medium">{{ $transaction->reference }}</td>
                                    <td>{{ $transaction->user }}</td>
                                    <td class="text-muted small">{{ $transaction->description }}</td>
                                    <td>
                                        @if($transaction->type == 'income')
                                            <span class="badge badge-success">Income</span>
                                        @else
                                            <span class="badge badge-danger">Expense</span>
                                        @endif
                                    </td>
                                    <td class="font-bold {{ $transaction->type == 'income' ? 'text-success' : 'text-danger' }}">
                                        {{ $transaction->type == 'income' ? '+' : '-' }}₦{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td>{{ $transaction->date->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst($transaction->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No transactions found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $paginatedTransactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
