<!-- resources/views/pages/home.blade.php -->
@extends('layouts.master')

@section('page-title')

    <div class="row align-items-start">
        <div class="col">
            <h2>Purchase Invoice - Create</h2>
        </div>
        <div class="col-auto ms-auto">
            <a href="{{ route('purchaseInvoice') }}" ><button type="button" class="btn btn-primary">List Invoice</button></a>
        </div>
    </div>
@endsection

@section('content')
    <div class="container mt-5">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }} <br/><br/>
                <a href="{{ url('purchaseInvoiceShow', session('invoiceId')) }}" ><button type="button" class="btn btn-warning">View invoice results</button></a>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchaseInvoice.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="file" class="form-label">Choose File</label>
                <input type="file" class="form-control" name="file" id="file" required>
                <div id="emailHelp" class="form-text"> jpg, jpeg, png, pdf | max:2048 MB </div>
            </div>
            <button type="submit" class="btn btn-success">Upload</button>
        </form>
    </div>
@endsection
