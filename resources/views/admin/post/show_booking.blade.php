<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <title>Booking Details</title>
</head>
<body>
<a href="{{ route('admin.post.pending') }}" class="btn btn-danger waves-effect">BACK</a>
<br>
<br>
<br>
<h3 style="text-align: center">Booking Details</h3>
<table class="table table-dark">
    <thead>
    <tr>
        <th>name</th>
        <th>email</th>
        <th>phone</th>
        <th>Start time</th>
        <th>End Time</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row">{{$post->name}}</th>
        <th scope="row">{{$post->email}}</th>
        <th scope="row">{{$post->phone}}</th>
        <th scope="row">{{$post->start_time}}</th>
        <th scope="row">{{$post->end_time}}</th>
        <th scope="row">{{$post->description}}</th>

    </tr>

    </tbody>
</table>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>