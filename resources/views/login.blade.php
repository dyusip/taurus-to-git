@if(isset(Auth::user()->username))
    <script>window.location="/successlogin"</script>
@endif
<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Taurus | Login</title>

    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('font-awesome/css/font-awesome.css')}}" rel="stylesheet">

    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

</head>

<body class="gray-bg">

<div class="loginColumns animated fadeInDown">
    <div class="row">

        <div class="col-md-6">
            {{--<img src="/img/TaurusLogopng.png" style="height: 68px">--}}

        </div>
        <div class="col-md-6">
            <div class="ibox-content">
                <div><img src='/img/TaurusLogopng.png' style="height: 58px;" class='cannot-select'/></div>
                <br><br>

            @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        {{$message}}
                    </div>
                @endif
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        @foreach($errors->all as $error)
                            {{$error}}
                        @endforeach
                    </div>
                @endif
                <form class="m-t" role="form" action="/user/login" method="post">
                    {{csrf_field()}}
                    <div class="form-group">
                        <input type="text" class="form-control" name="username" placeholder="Username" >
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                    <a href="#">
                        <small>Forgot password?</small>
                    </a>

                    <p class="text-muted text-center">
                        <small>Do not have an account?</small>
                    </p>
                    <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a>
                </form>
                <p class="m-t">
                    <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small>
                </p>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            Copyright Example Company
        </div>
        <div class="col-md-6 text-right">
            <small>© 2014-2015</small>
        </div>
    </div>
</div>

</body>

</html>
