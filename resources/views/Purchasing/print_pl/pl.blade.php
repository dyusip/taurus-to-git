@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Purchasing.sidebar')

        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Print Pick List</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Print Pick List</strong>
                        </li>
                    </ol>
                </div>
                <div class="col-lg-2">

                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="wrapper wrapper-content animated fadeInUp">

                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Transferred Item</h5>
                                <!--<div class="ibox-tools">
                                    <a href="" class="btn btn-primary btn-xs">Create new project</a>
                                </div>-->
                            </div>
                            <div class="ibox-content">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="tbl-transfer"
                                           data-page-size="15">
                                        <thead>
                                        <tr>
                                            <th width="9%">PL CODE</th>
                                            <th data-hide="phone" width="20%">REQUESTER</th>
                                            <th data-hide="phone" width="15%">DATE</th>
                                            <th data-hide="phone" width="15%">PREPARED BY</th>
                                            {{--<th data-hide="phone" width="15%">APPROVED BY</th>--}}
                                            <th class="text-center" width="8%">ACTION</th>
                                        </tr>
                                        </thead>
                                        <tbody class="tooltip-demo" id="order-tbl" >
                                        </tbody>


                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="post_form" action="/pl_print/print" method="POST" target="_blank">
                {{csrf_field()}}
                <input id="form_input" type="hidden" value="" name="rqh_code">
            </form>

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
    <!-- /#wrapper -->

    <!-- jQuery -->

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
    $('#tbl-transfer').DataTable({
        'processing': true,
        'serverSide': true,
        'ajax': '/pl_print/id',
        'columns': [
            {data: 1, name: 'req_headers.rqh_code'},
            {data: 11+'.name', name: 'req_from_branch.name'},
            {data: 4, name: 'req_headers.req_date'},
            {data: 13+'.name', name: 'rqh_req_by.name'},
            /* {data: 14+'.name', name: 'tf_app_by.name'},*/
            {data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}

        ],
        "order": [[3, 'desc']]
    });
</script>
<script type="text/javascript" language="javascript">
    /*function post_link(data){

        $('#post_form').find('#form_input').val(data);
        $('#post_form').submit();
    }*/
    $(document).on('click','#btn-edit',function () {
        var data = $(this).data('id');
        $('#post_form').find('#form_input').val(data);
        $('#post_form').submit();
    });
</script>
@endpush