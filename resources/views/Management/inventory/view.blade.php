@extends('layout.main')

@section('index-content')
<div id="wrapper">

    <!-- Navigation -->
    @include('Management.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('Management.header')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Inventory</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="index">Home</a>
                    </li>
                    <li class="active">
                        <strong>Inventory</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>
        <div class="wrapper wrapper-content animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                @php $ctr = 1 @endphp
                                @foreach($branches as $branch)
                                    <li class="{{($branch->code==Auth::user()->branch)?'active':''}}"><a data-toggle="tab" href="#tab-{{ $ctr }}">{{ $branch->name }} </a>
                                    @php $ctr++ @endphp
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @php $ctr = 1 @endphp
                                @foreach($branches as $branch)
                                    <div id="tab-{{$ctr}}" class="tab-pane {{($branch->code==Auth::user()->branch)?'active':''}}">
                                        <div class="panel-body">
                                            <div>
                                                <a href="{{ url('/salesman/inventory/print/'.$branch->code.'') }}" class="btn btn-primary btn-sm"><i class="fa fa-print"></i> Print</a>
                                            </div>
                                            <br>
                                            <div class="table-responsive">
                                                <table width="100%" class="table table-striped table-bordered table-hover dataTables-example" id="dataTables-{{$branch->code}}">
                                                    <thead>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Cost</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>UOM</th>
                                                        {{--{!! $branch->code==Auth::user()->branch?'<th class="text-center">Action</th>':''  !!}--}}
                                                    </tr>
                                                    </thead>
                                                    <tbody class="tooltip-demo">

                                                    </tbody>
                                                    {{--<tr>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <td style="display: none;"></td>
                                                        <th class="text-right" colspan="3">TOTAL COST</th>
                                                        <th></th>
                                                        <th class="text-right" >TOTAL AMNT</th>
                                                        <th colspan="3"></th>
                                                    </tr>--}}
                                                    {{--<tfoot>
                                                    <tr>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Description</th>
                                                        <th>Cost</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>UOM</th>
                                                    </tr>
                                                    </tfoot>--}}
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @php $ctr++ @endphp
                                @endforeach
                            </div><!-- tab -->
                            <!--edit Item-->
                            <div class="modal inmodal" id="edit-prod-modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static">
                                <div class="modal-dialog">
                                    <div class="modal-content animated slideInDown">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                            <!-- <i class="fa fa-shopping-cart modal-icon"></i>-->

                                            <h4 class="modal-title" id="md-title-edit">Edit Product</h4>
                                            <small class="font-bold" id="md-info-edit">Taurus Merchandising inventory information</small>
                                        </div>
                                        <form method="post" id="form_edit_prod" action="/salesman/inventory/update">
                                            {{ csrf_field() }}
                                            <div class="modal-body">
                                                <div class="spiner-example" id="md-spinner-edit" hidden style="position: fixed; right: 50%">
                                                    <div class="sk-spinner sk-spinner-wave">
                                                        <div class="sk-rect1"></div>
                                                        <div class="sk-rect2"></div>
                                                        <div class="sk-rect3"></div>
                                                        <div class="sk-rect4"></div>
                                                        <div class="sk-rect5"></div>
                                                    </div>
                                                </div>
                                                <div class="alert alert-danger" id="md-alert-error-edit" hidden>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Price</label>
                                                            <input type="hidden" id="prod-code" name="prod_code">
                                                            <input type="text" placeholder="Price" autocomplete="off" name="price" id="price"
                                                                   class="form-control">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success"><i class="fa fa-edit"></i> Edit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- modal end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- /#page-wrapper -->
        <div class="footer">
            <div class="pull-right">
                <strong></strong>
            </div>
            <div>
                <strong>Copyright</strong> INFOZ-ITWORKS &copy; 2018
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link href="{{ asset('css/plugins/select2/select2.min.css' )}}" rel="stylesheet">
<link href="{{ asset('/js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet">
<link href="{{ asset('/css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{ asset('/js/plugins/dataTables/datatables.min.js') }}"></script>
<script src="{{ asset('js/plugins/select2/select2.full.min.js') }}"></script>
<script>
    @foreach($branches as $branch)
     $('#dataTables-{{$branch->code}}').DataTable({
        'processing': true,
        'serverSide': true,
        'ajax': 'inventory/{{$branch->code}}',
        'columns': [
            {data: 1, name: 'branch__inventories.prod_code'},
            {data: 9, name: 'inventories.name'},
            {data: 10, name: 'inventories.desc'},
            {data: 3, name: 'branch__inventories.cost'},
            {data: 4, name: 'branch__inventories.quantity'},
            {data: 2, name: 'branch__inventories.price'},
            {data: 11, name: 'inventories.uom'},
            //{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}
            {{--{!! $branch->code==Auth::user()->branch?"{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}":''  !!}--}}
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
    function validateNumber(event) {
        var key = window.event ? event.keyCode : event.which;
        var keychar = String.fromCharCode(key);
        if (event.keyCode == 8 || event.keyCode == 46
            || event.keyCode == 37 || event.keyCode == 39) {
            return true;
        }
        else if ( key < 48 || key > 57 || keychar==".") {
            return false;
        }
        else return true;
    }
    $(document).ready(function(){
        $('[id^=price]').keypress(validateNumber);
    });
    $(document).on('click','#btn-edit', function () {
        var prod = $(this).data('prod');
        $.ajax({url:"inventory/edit/"+prod,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function(data) {
                //var data = jQuery.parseJSON(output);
                $('#main-spinner').fadeOut();
                $('#price').val(data.price);
                $('#prod-code').val(data.prod_code);
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status + " "+ thrownError);
            }

        });
    });
    @if (session('status'))
        $(document).ready(function () {
            toastr.success("{{ session('status') }}");
        });
    @endif
</script>
@endpush