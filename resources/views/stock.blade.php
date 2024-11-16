@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Stock Management System</h1>

    <!-- Stock In Form -->
    <div class="form-container">
        <h2>Stock In</h2>
        <form id="stockInForm">
            <input type="number" id="item_id_in" placeholder="Item ID" required>
            <input type="number" id="quantity_in" placeholder="Quantity" required>
            <input type="number" id="cost_in" placeholder="Cost" step="0.01">
            <button type="submit">Add Stock</button>
        </form>
    </div>

    <!-- Stock Out Form -->
    <div class="form-container">
        <h2>Stock Out</h2>
        <form id="stockOutForm">
            <input type="number" id="item_id_out" placeholder="Item ID" required>
            <input type="number" id="quantity_out" placeholder="Quantity" required>
            <input type="number" id="price_out" placeholder="Price" step="0.01">
            <button type="submit">Remove Stock</button>
        </form>
    </div>

    <!-- Transaction History -->
    <div class="transaction-history">
        <h2>Transaction History</h2>
        <button id="fetchTransactions">Fetch Transactions</button>
        <ul id="transactionsList"></ul>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/script.js') }}"></script>
@endsection
