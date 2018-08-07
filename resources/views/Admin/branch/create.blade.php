@extends('layout.main')

@section('index-content')
    <div id="wrapper">
            @include('Admin.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Admin/header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-9">
                    <h2>Branch</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Branch</strong>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="ibox float-e-margins">
                            <div class="overlay">
                                <div class="loading">Loading&#8230;</div>
                            </div>
                            <div class="ibox-content">
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif


                                <form id="form-branch"  method="POST" action="/branch">
                                    <input name="_method" type="hidden" disabled value="PUT" id="_method">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <input type="hidden" name="code" id="code" value="{{ $num }}">
                                            <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                                                <label>Name</label>
                                                <input type="text" autocomplete="off" name="name" id="name" style="text-transform: uppercase" placeholder="Name" value="{{ old('name') }}" class="form-control" required>
                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('name') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
                                                <label>Address</label>
                                                <input type="text" autocomplete="off" placeholder="Address" value="{{ old('address') }}" name="address" id="address" required class="form-control">
                                                @if ($errors->has('address'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('address') }}</strong>
                                            </span>
                                                @endif
                                            </div>
                                            <div class="form-group">
                                                <label>Contact #</label>
                                                <input type="text" autocomplete="off" placeholder="Contact" value="{{ old('contact') }}" name="contact" id="contact" class="form-control">
                                            </div>
                                            <div>
                                                <!--<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Create Account</strong></button>-->
                                                <button class="btn btn-sm btn-primary btn-block" name="register" type="submit"><strong id="register">Create Account</strong></button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content">
                                <!--Employee Settings-->
                            <div class="modal inmodal" id="modal-br-status" tabindex="-1" role="dialog"  aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content animated fadeIn">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <i class="fa fa-user modal-icon"></i>
                                            <h4 class="modal-title" id="status_name">Branch Status</h4>
                                            <small >Activation and deactivation of branch</small>
                                        </div>
                                        <form class="" id="form-status" method="post"  autocomplete="off" enctype= multipart/form-data>
                                            {{ method_field('DELETE') }}
                                            {{ csrf_field() }}
                                            <div class="modal-body text-right">
                                                <button type="submit" name="status" value="AC" class="btn btn-primary btn-sm dim"><i class="fa fa-user"></i> Activate</button>
                                                <button type="submit" name="status" value="IN" class="btn btn-danger btn-sm dim"><i class="fa fa-user-times"></i> Deactivate</button>
                                            </div>
                                            <div class="modal-footer">

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!--Employee Settings-->
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                        <thead>
                                        <tr>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($branches as $branch)
                                            <tr>
                                                <td>{{ $branch->code }}</td>
                                                <td>{{ $branch->name }}</td>
                                                <td>{{ $branch->address }}</td>
                                                <td>{{ $branch->status }}</td>
                                                <td class="text-center">
                                                    <a href="#" class="text-success" id="btn-edit"  data-id="{{ $branch->id }}"><i class="fa fa-edit"></i></a>
                                                    <a href="#modal-br-status" class="text-danger" id="btn-delete" data-id="{{ $branch->id }}" data-toggle="modal"><i class="fa fa-remove"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <div class="pull-right">
                    <strong></strong>
                </div>
                <div>
                    <strong>Copyright</strong> INFOZ-ITWORKS © 2017
                </div>
            </div>

        </div>

    </div>
@endsection
<!-- datatable-->
<!-- Gritter -->
@push('styles')
<link href="{{ asset('/js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet">
<link href="{{ asset('/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
<style>
    /* Absolute Center Spinner */
    .loading {
        position: absolute;
        z-index: 999;
        height: 2em;
        width: 2em;
        overflow: show;
        margin: auto;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    /* Transparent Overlay */
    /*.loading:before {
        content: '';
        display: block;
        position: fixed;
        text-align: center;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.3);
    }*/
    .overlay {
        z-index: 999;
        display: none;
        position:absolute;
        top: 10px;
        left: 25px;
        width: 86%;
        height:90%;
        background-color: rgba(255,255,255,255.3);
    }

    /* :not(:required) hides these rules from IE9 and below */
    .loading:not(:required) {
        /* hide "loading..." text */
        font: 0/0 a;
        color: transparent;
        text-shadow: none;
        background-color: transparent;
        border: 0;
    }

    .loading:not(:required):after {
        content: '';
        display: block;
        font-size: 10px;
        width: 1em;
        height: 1em;
        margin-top: -0.5em;
        -webkit-animation: spinner 1500ms infinite linear;
        -moz-animation: spinner 1500ms infinite linear;
        -ms-animation: spinner 1500ms infinite linear;
        -o-animation: spinner 1500ms infinite linear;
        animation: spinner 1500ms infinite linear;
        border-radius: 0.5em;
        -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
        box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
    }

    /* Animation */

    @-webkit-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-moz-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @-o-keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
    @keyframes spinner {
        0% {
            -webkit-transform: rotate(0deg);
            -moz-transform: rotate(0deg);
            -ms-transform: rotate(0deg);
            -o-transform: rotate(0deg);
            transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
            -moz-transform: rotate(360deg);
            -ms-transform: rotate(360deg);
            -o-transform: rotate(360deg);
            transform: rotate(360deg);
        }
    }
</style>
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            dom: '<"html5buttons"B>lTfgitp',
            "bSort" : true,
            buttons: [
                /* {extend: 'copy'},
                 {extend: 'csv'},*/
                {extend: 'excel', title: 'Employee Account'},
                {extend: 'pdf', title: 'Employee Account'},

                {
                    extend: 'print',
                    customize: function (win) {
                        $(win.document.body).addClass('white-bg');
                        $(win.document.body).css('font-size', '10px');

                        $(win.document.body).find('table')
                            .addClass('compact')
                            .css('font-size', 'inherit');
                    }
                }
            ]
        });
        $(document).on('click','#btn-edit',function () {
            var id = $(this).data('id');
            $(".overlay").show();
            $.get(id+"/edit", function (data) {
                $('.overlay').fadeOut();
                $('#name').val(data.name);
                $('#address').val(data.address);
                $('#contact').val(data.contact);
                $('#form-branch').attr('action','/branch/'+data.id);
                $('#_method').removeAttr('disabled');
                $('#register').html('Update Account');
                $('#code').attr('disabled',true);
            });

        });
        $(document).on('click','#btn-delete',function () {
            var id = $(this).data('id');
            $('#form-status').attr('action','/branch/'+id);
        });
    });
</script>
@endpush