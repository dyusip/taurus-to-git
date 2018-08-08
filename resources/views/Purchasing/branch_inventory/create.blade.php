@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-9">
                    <h2>Inventory</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/">Home</a>
                        </li>
                        <li class="active">
                            <strong>Inventory</strong>
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

                                <form id="form-inventory"  method="POST" action="/branch_inventory">
                                    @if (session('status'))
                                        <div class="alert alert-success">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                    <input name="_method" type="hidden" disabled value="PUT" id="_method">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('branch_code') ? ' has-error' : '' }}">
                                                <label>Branch</label>
                                                <select class="form-control select2_demo_1" name="branch_code" id="branch_code" required>
                                                    <option value="">Select Branch</option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}" {{ (old("branch_code") == $branch->code ? "selected":"") }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('branch_code'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('branch_code') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('prod_code') ? ' has-error' : '' }}">
                                                <label>Product Name</label>
                                                <select class="form-control select2_demo_1" name="prod_code" id="prod_code" required>
                                                    <option value="">Select Product</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->code }}" {{ (old("prod_code") == $product->code ? "selected":"") }}>{{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('prod_code'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('prod_code') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('cost') ? ' has-error' : '' }}">
                                                <label>Cost</label>
                                                <input type="text" autocomplete="off" placeholder="Cost" value="{{ old('cost') }}" name="cost" id="cost" required class="form-control">
                                                @if ($errors->has('cost'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('cost') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('price') ? ' has-error' : '' }}">
                                                <label>Price</label>
                                                <input type="text" autocomplete="off" placeholder="Price" value="{{ old('price') }}" name="price" id="price" required class="form-control">
                                                @if ($errors->has('price'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('price') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                            <div>
                                                <!--<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Create Account</strong></button>-->
                                                <button class="btn btn-sm btn-primary btn-block" name="register" type="submit"><strong id="register">Create Inventory</strong></button>
                                                <a href="/branch_inventory/create" style="display: none" id="add-item" class="btn btn-sm btn-success btn-block" ><i class="fa fa-angle-double-left"></i><strong> Add Item</strong></a>
                                                <a href="#"  id="replicate-item" class="btn btn-sm btn-success btn-block" ><i class="fa fa-angle-double-left"></i><strong> Replicate Item</strong></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                {{--replication--}}
                                <form id="form-replicate" hidden method="POST" action="/branch_inventory/replicate">
                                    @if (session('status_'))
                                        <div class="alert alert-success">
                                            {{ session('status_') }}
                                        </div>
                                    @endif
                                    <input name="_method" type="hidden" disabled value="PUT" id="_method">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group {{ $errors->has('branch_code') ? ' has-error' : '' }}">
                                                <label>From</label>
                                                <select class="form-control" name="branch_code" id="branch_code" required>
                                                    <option></option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}" {{ (old("branch_code") == $branch->code ? "selected":"") }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('branch_code'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('branch_code') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div class="form-group {{ $errors->has('branch_to') ? ' has-error' : '' }}">
                                                <label>To</label>
                                                <select class="form-control" name="branch_to" id="branch_to" required>
                                                    <option></option>
                                                    @foreach($branches as $branch)
                                                        <option value="{{ $branch->code }}" {{ (old("branch_to") == $branch->code ? "selected":"") }}>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                                @if ($errors->has('branch_to'))
                                                    <span class="help-block">
                                                    <strong>{{ $errors->first('branch_to') }}</strong>
                                                </span>
                                                @endif
                                            </div>
                                            <div>
                                                <!--<button class="btn btn-sm btn-primary pull-right m-t-n-xs" type="submit"><strong>Create Account</strong></button>-->
                                                <button class="btn btn-sm btn-primary btn-block" name="register" type="submit"><strong id="register">Create Replication</strong></button>
                                                <a href="#"  id="add-item-main" class="btn btn-sm btn-success btn-block" ><i class="fa fa-angle-double-left"></i><strong> Add Item</strong></a>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="ibox">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs">
                                    @php $ctr = 1 @endphp
                                    @foreach($branches as $branch)
                                        <li class="{{($ctr==1)?'active':''}}"><a data-toggle="tab" href="#tab-{{$ctr}}"> {{$branch->name}}</a></li>
                                        @php $ctr++ @endphp
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @php $ctr = 1 @endphp
                                    @foreach($branches as $branch)
                                        <div id="tab-{{$ctr}}" class="tab-pane {{($ctr==1)?'active':''}}">
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-{{$branch->code}}">
                                                        <thead>
                                                        <tr>
                                                            <th>Code</th>
                                                            <th>Name</th>
                                                            <th>Cost</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                        </thead>
                                                        {{--<tbody>
                                                        @foreach($inventories as $inventory)
                                                            @if($branch->code===$inventory->branch->code)
                                                            <tr>
                                                                <td>{{ $inventory->prod_code }}</td>
                                                                <td>{{ $inventory->inventory->name }}</td>
                                                                <td>{{ $inventory->cost }}</td>
                                                                <td>{{ $inventory->quantity }}</td>
                                                                <td>{{ $inventory->price }}</td>
                                                                <td class="text-center">
                                                                    <a href="#" class="text-success" id="btn-edit" data-id="{{ $inventory->inventory->id }}"  data-branch="{{ $inventory->branch_code }}" data-prod="{{ $inventory->prod_code }}"><i class="fa fa-edit"></i></a>
                                                                    --}}{{--<a href="#modal-br-status" class="text-danger" id="btn-delete" data-id="{{ $inventory->branch_code }}" data-toggle="modal"><i class="fa fa-remove"></i></a>--}}{{--
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                        </tbody>--}}
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
                                        @php $ctr++ @endphp
                                    @endforeach
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
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
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

    /*Readonly*/
    select[readonly].select2-hidden-accessible + .select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
        display: none;
    }

</style>
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $(".select2_demo_1").select2();

        $(document).ready(function () {
            resizeChosen();
            jQuery(window).on('resize', resizeChosen);
        });
        function resizeChosen() {
            $(".select2-container").each(function () {
                $(this).attr('style', 'width: 100%');
            });
        }
        $('#modalAdd').on('shown.bs.modal', function () {
            $('.chosen-select', this).chosen('destroy').chosen();
        });
        {{--@foreach($branches as $branch)
            $('#dataTables-{{$branch->code}}').DataTable({
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
        @endforeach--}}
        @foreach($branches as $branch)
           $('#dataTables-{{$branch->code}}').DataTable({
            'processing': true,
            'serverSide': true,
            'ajax': '{{$branch->code}}',
            'columns': [
                {data: 1, name: 'branch__inventories.prod_code'},
                {data: 9, name: 'inventories.name'},
                {data: 3, name: 'branch__inventories.cost'},
                {data: 4, name: 'branch__inventories.quantity'},
                {data: 2, name: 'branch__inventories.price'},
                {data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}
            ]
            /* "columns": [
             { "data": 1 },
             { "data": 9 },
             { "data": 3 },
             { "data": 4 },
             { "data": 2 },
             { "data": 14 },
             ]*/
        });
        @endforeach
        $(document).on('click','#btn-edit',function () {
            $('#form-replicate').hide();
            $('#form-inventory').show();
            var id = $(this).data('id');
            var prod_code = $(this).data('prod');
            var branch_code = $(this).data('branch');
            $(".overlay").show();
            $.get(id+"/edit",{ product: prod_code, branch: branch_code } ).done( function (data) {
                $('.overlay').fadeOut();
                //$('#branch_code').css({'pointer-events': 'none','background-color': '#eee','opacity':1});//background-color: #eee; opacity: 1
                $("#branch_code").select2().select2('val',data.branch_code);
                $("#branch_code, #prod_code").attr("readonly", "readonly");
                //$('#prod_code').css({'pointer-events': 'none','background-color': '#eee','opacity':1});
                //$('#prod_code option:not(:selected)').attr('disabled',true);
                $('#add-item').css('display','block');
                $("#prod_code").select2().select2('val',data.prod_code);

                $('#cost').val(data.cost);
                $('#price').val(data.price);
                $('#form-inventory').attr('action','/branch_inventory/'+id);
                $('#_method').removeAttr('disabled');
                $('#register').html('Update Inventory');
                $('#code').attr('disabled',true);
            });

        });
        $(document).on('click','#btn-delete',function () {
            var id = $(this).data('id');
            $('#form-status').attr('action','/inventory/'+id);
        });
        function validateCurrency(event) {
            var key = window.event ? event.keyCode : event.which;
            var keychar = String.fromCharCode(key);
            if (event.keyCode == 8 || event.keyCode == 46
                || event.keyCode == 37 || event.keyCode == 39 ||  keychar == ".") {
                return true;
            }
            else if ( key < 48 || key > 57 ) {
                return false;
            }
            else return true;
        }
        $(document).ready(function(){
            $('[id^=cost]').keypress(validateCurrency);
            $('[id^=price]').keypress(validateCurrency);
        });
        $(document).on('click','#replicate-item', function () {
            $('#form-replicate').show();
            $('#form-inventory').hide();
        });
        $(document).on('click','#add-item-main', function () {
            $('#form-replicate').hide();
            $('#form-inventory').show();
        });
        $(document).ready(function () {
            @if($errors->has('branch_to') || session('status_'))
                $('#form-replicate').show();
            $('#form-inventory').hide();
            @endif
        });
    });
</script>
@endpush