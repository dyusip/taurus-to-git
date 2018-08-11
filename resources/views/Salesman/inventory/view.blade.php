@extends('layout.main')

@section('index-content')
<div id="wrapper">

    <!-- Navigation -->
    @include('Salesman.sidebar')
    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('Salesman.header')
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
                                    <li class="{{($ctr==1)?'active':''}}"><a data-toggle="tab" href="#tab-{{ $ctr }}">{{ $branch->name }} </a>
                                    @php $ctr++ @endphp
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @php $ctr = 1 @endphp
                                @foreach($branches as $branch)
                                    <div id="tab-{{$ctr}}" class="tab-pane {{($ctr==1)?'active':''}}">
                                        <div class="panel-body">
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
                                                        {!! $branch->code==Auth::user()->branch?'<th class="text-center">Action</th>':''  !!}
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
                                            <small class="font-bold" id="md-info-edit">Infotrade Resources payable to vendor information.</small>
                                        </div>
                                        <form method="post" id="form_edit_prod" action="INV_FOLDER/update_inventory.php">
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
                                                            <input type="hidden" name="prod_code_edit" id="prod_code_edit" readonly
                                                                   class="form-control">
                                                            <label>Cost</label>
                                                            <input type="text" autocomplete="off" class="form-control" name="cost"
                                                                   id="cost" >
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Price</label>
                                                            <input type="hidden" id="prod-code" name="prod_code">
                                                            <input type="text" placeholder="Price" autocomplete="off" name="prod_uprice_edit" id="prod_uprice_edit"
                                                                   class="form-control">
                                                        </div>
                                                        <div class="form-group"><label>Description</label>
                                                            <select name="desc" id="desc" required autocomplete="off" class="form-control">
                                                            </select>
                                                        </div>
                                                        <div class="form-group"><label>UOM</label>
                                                            <select name="uom" id="uom" required autocomplete="off" class="form-control">
                                                                <option value="" disabled="disabled" selected>UOM</option>
                                                                <option value="DRUM">DRUM</option>
                                                                <option value="GALLON">GALLON</option>
                                                                <option value="LITER">LITER</option>
                                                                <option value="ML">ML</option>
                                                                <option value="METER">METER</option>
                                                                <option value="PIECE">PIECE</option>
                                                                <option value="KIT">KIT</option>
                                                            </select>
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
            {!! $branch->code==Auth::user()->branch?"{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}":''  !!}
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
</script>
@endpush