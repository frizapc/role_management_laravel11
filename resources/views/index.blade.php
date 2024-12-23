<!DOCTYPE html>
<html lang="en">

<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="{{
        !config('services.midtrans.isProduction') ? 'https://app.sandbox.midtrans.com/snap/snap.js' : 'https://app.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.clientKey')
    }}"></script>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>IDonation</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">

    <style>
        body {
            min-height: 75rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="/">IDonation</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="/donation">Donation <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="jumbotron">
        <div class="container">
            <h1 class="display-4">IDonation</h1>
            <p class="lead">Platform donasi untuk saudara kita yang membutuhkan.</p>
        </div>
    </div>

    <div class="container">
       <table class="table">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Nama</th>
            <th scope="col">Tipe</th>
            <th scope="col">Jumlah</th>
            <th scope="col">Status</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($donations as $donation)
          <tr>
            <th scope="row">{{$donation->id}}</th>
            <td>{{$donation->donor_name}}</td>
            <td>{{$donation->donation_type  }}</td>
            <td>{{$donation->amount}}</td>
            <td>
              @if ($donation->status == 'success')
              {{$donation->status}}
              @elseif ($donation->status == 'pending')
              <button class="btn btn-success btn-sm" onclick="snap.pay('{{$donation->snap_token}}')">Bayar</button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>



    <script src="https://code.jquery.com/jquery-3.4.1.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js">
    </script>
    
</body>

</html>