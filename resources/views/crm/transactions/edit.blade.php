@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="p-6 max-w-xl">

    <h1 class="text-xl font-bold mb-4">Edit Transaction</h1>

    <form method="POST" action="{{ route('crm.transactions.update', $transaction) }}">
        @csrf
        @method('PUT')

        <input type="text" name="type"
               value="{{ $transaction->type }}"
               class="w-full border p-2 mb-3">

        <input type="number" name="amount"
               value="{{ $transaction->amount }}"
               class="w-full border p-2 mb-3">

        <input type="number" name="down_payment"
               value="{{ $transaction->down_payment }}"
               class="w-full border p-2 mb-3">

        <input type="number" name="tenor_months"
               value="{{ $transaction->tenor_months }}"
               class="w-full border p-2 mb-3">

        <textarea name="notes"
                  class="w-full border p-2 mb-3">{{ $transaction->notes }}</textarea>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Update
        </button>
    </form>

</div>
@endsection