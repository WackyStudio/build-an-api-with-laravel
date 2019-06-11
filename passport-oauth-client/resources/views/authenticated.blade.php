<!DOCTYPE HTML>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>Passport Client</title>
    <link href="{{mix('/css/app.css')}}" media="screen, projection" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="{{mix('/js/app.js')}}" defer></script>

</head>

<body>
<main role="main" id="app">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-12">
                <h1>Authenticated</h1>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-md-6">
                <p>Access token: </p>
                <textarea class="form-control" cols="30" rows="10" readonly="">{{$access_token}}</textarea>
            </div>
            <div class="col-md-6">
                <p>Refresh token:</p>
                <textarea class="form-control" cols="30" rows="10" readonly="">{{$refresh_token}}</textarea>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>The user this access token belongs to:</h4>
                <p>Name: {{$name}}</p>
                <p>Email: {{$email}}</p>
                <p>The token expires in {{ (($expires_in / 60)/60)/24 }} days</p>
                <a href="/">Back</a>
            </div>
        </div>
    </div>
</main>


</body>

</html>
