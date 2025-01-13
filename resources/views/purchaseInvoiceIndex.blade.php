<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<button type="button" class="btn btn-primary">Create</button>
<table class="table table-striped">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">User</th>
        <th scope="col">Invoice No</th>
        <th scope="col">Total</th>
        <th scope="col">Date</th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <th scope="row">{{ $invoice->id }}</th>
            <td>{{ $invoice->user_id }}</td>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->total_amount }}</td>
            <td>{{ $invoice->purchase_date }}</td>
            <td>-</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
