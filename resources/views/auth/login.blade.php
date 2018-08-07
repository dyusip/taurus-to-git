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
                <div><img src='/img/tinuod.gif' style="height: 58px;" class='cannot-select'/></div>
                <br><br>

                <form class="m-t" role="form" action="{{ route('login') }}" method="post">
                    {{csrf_field()}}
                    <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                        <input type="text" class="form-control" name="username" placeholder="Username" >
                        @if ($errors->has('username'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('username') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" name="password" placeholder="Password">
                        @if ($errors->has('password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                        @endif
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">Login</button>

                    <a href="{{ route('password.request') }}">
                        <small>Forgot password?</small>
                    </a>

                    <p class="text-muted text-center">
                        <small>Do not have an account?</small>
                    </p>
                    <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a>
                </form>
                <p class="m-t">
                    <small>Taurus Merchandising &copy; 2018. Powered by <a href="http:\\infozitworks.com" target="_blank">Infoz Team</a></small>
                </p>
            </div>
        </div>
    </div>
    <hr/>
    <div class="row">
        <div class="col-md-6">
            Online Sales and Inventory Management Systems™
        </div>
        <div class="col-md-6 text-right">
            <small>© {{ date('Y') }}</small>
        </div>
    </div>
</div>
</body>

</html>

