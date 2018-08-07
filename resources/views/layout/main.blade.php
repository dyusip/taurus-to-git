<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('img/taurus.png')}}"/>

    <title>Taurus Merchandising</title>
    <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('font-awesome/css/font-awesome.css')}}" rel="stylesheet">
    <link href="{{ asset('css/plugins/toastr/toastr.min.css' )}}" rel="stylesheet">

    <link href="{{asset('css/animate.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">

    @stack('styles')
</head>
<body>


@yield('index-content')
<script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
<script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('js/plugins/jeditable/jquery.jeditable.js') }}"></script>

<script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>

<!-- Custom and plugin javascript -->
<script src="{{ asset('js/inspinia.js') }}"></script>
<script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>

<script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>
@stack('scripts')
<script>
    //Side bar
    $(function() {
        var url = "{{ URL::current() }}";
// for sidebar menu entirely but not cover 2nd level
        $('ul.metismenu a').filter(function() {
            return this.href == url;
        }).parent().addClass('active');

// for 2nd level
        $('ul.nav-second-level a').filter(function() {
            return this.href == url;
        }).addClass('active').parent().parent().addClass('in').parent().closest('li').addClass('active');
    });
    //Notification
    $('#class-message-menu').slimScroll({
        height: '250px',
        //start : 'bottom'
    });
    //Profile
    @if($errors->has('current_pass') || $errors->has('new_pass') || $errors->has('confirm_pass'))
        $(document).ready(function () {
            $('#modalSettings').modal('show');
        });
    @endif
    $(function() {
        // for bootstrap 3 use 'shown.bs.tab', for bootstrap 2 use 'shown' in the next line
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            // save the latest tab; use cookies if you like 'em better:
            localStorage.setItem('lastTab', $(this).attr('href'));
        });

        // go to the latest tab, if it exists:
        var lastTab = localStorage.getItem('lastTab');
        if (lastTab) {
            $('[href="' + lastTab + '"]').tab('show');
        }
    });
    @if (session('password_status'))
        $(document).ready(function () {
        toastr.success("{{ session('password_status') }}");
    });
    @endif
</script>

</body>
</html>