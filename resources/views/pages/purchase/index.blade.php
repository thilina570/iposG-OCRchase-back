<!-- resources/views/pages/home.blade.php -->
@extends('layouts.master')

@section('page-title')

    <div class="row align-items-start">
        <div class="col">
            <h2>Purchase Invoice - Create</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('purchaseInvoiceCreate') }}" ><button type="button" class="btn btn-primary">Create Invoice</button></a>
        </div>
    </div>
@endsection

@section('content')
    <table class="table table-striped">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">User</th>
            <th scope="col">Invoice No</th>
            <th scope="col">Total amout</th>
            <th scope="col">Purchase date</th>
            <th scope="col"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($invoices as $invoice)
            <tr>
                <th scope="row">{{ $invoice->id }}</th>
                <td>{{ $invoice->user->name }}</td>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->total_amount }}</td>
                <td>{{ $invoice->purchase_date }}</td>
                <td><a href="{{ url('purchaseInvoiceShow', $invoice->id) }}"><i class="fa-solid fa-eye"></i></a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
