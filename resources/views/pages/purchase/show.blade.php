<!-- resources/views/pages/home.blade.php -->
@extends('layouts.master')

@section('page-title')

    <div class="row align-items-start">
        <div class="col">
            <h2>Purchase Invoice - Show</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('purchaseInvoice') }}" ><button type="button" class="btn btn-primary">List Invoice</button></a>
        </div>
    </div>
@endsection

@section('content')
        <div class="row align-items-start">
            <div class="col-lg-3 col-sm-12">
                <div class="table-responsive">
                    <h4> Invoice data</h4>
                    <table class="table">
                        <tr>
                            <th scope="col">Id</th>
                            <td scope="col">{{ $invoice->id }}</td>
                        </tr>
                        <tr>
                            <th scope="col">User</th>
                            <td scope="col">{{ $invoice->user->name }}</td>
                        </tr>
                        <tr>
                            <th scope="col">Invoice No</th>
                            <td scope="col">{{ $invoice->invoice_number }}</td>
                        </tr>
                        <tr>
                            <th scope="col">Total amount</th>
                            <td scope="col">{{ $invoice->total_amount }}</td>
                        </tr>
                        <tr>
                            <th scope="col">Purchase date</th>
                            <td scope="col">{{ $invoice->purchase_date }}</td>
                        </tr>
                        <tr>
                            <th scope="col">Created at</th>
                            <td scope="col">{{ $invoice->created_at }}</td>
                        </tr>
                    </table>
                </div>
                <iframe
                    src="/bill-asoka.jpeg"
                    width="100%"
                    height="600"
                    style="border: none;"
                >
                    Your browser does not support iframes.
                </iframe>

            </div>
            <div class="col-lg-9 col-sm-12">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Item name</th>
                        <th scope="col">Quantity</th>
                        <th scope="col">Price per unit</th>
                        <th scope="col">Total price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($invoice->items as $item)
                        <tr>
                            <th scope="row">{{ $item->id }}</th>
                            <th scope="row">{{ $item->item_name }}</th>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->price_per_unit }}</td>
                            <td>{{ $item->total_price }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endsection
