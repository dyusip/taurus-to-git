@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Admin.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Admin.header')
            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Acitvity Logs</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Acitvity Logs</strong>
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
                            <div class="ibox-title">
                                <h5>Logs Info <small>view logs info</small></h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                        <i class="fa fa-wrench"></i>
                                    </a>
                                    <ul class="dropdown-menu dropdown-user">
                                        <li><a href="#">Config option 1</a>
                                        </li>
                                        <li><a href="#">Config option 2</a>
                                        </li>
                                    </ul>
                                    <a class="close-link">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table width="100%"
                                                   class="table table-striped table-bordered table-hover dataTables-example"
                                                   id="dataTables-logs">
                                                <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>NAME</th>
                                                    <th>BRANCH</th>
                                                    <th>ACTIVITY</th>
                                                    <th>IP</th>
                                                    <th>TIME</th>
                                                </tr>
                                                </thead>
                                                <tbody class="tooltip-demo">

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
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
     $('#dataTables-logs').DataTable({
        'processing': true,
        'serverSide': true,
         //'bSort': false,
        'ajax': 'logs/data',
        'columns': [
            {data: 0, name: 'id'},
            {data: 6+".name", name: 'user.name', "orderable": "false"},
            {data: 6+".branch_user.name", name: 'user.branch_user.name', "orderable": "false"},
            {data: 2, name: 'text'},
            {data: 3, name: 'ip_address'},
            {data: 4, name: 'activity_log.created_at'},
            //{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}
            {{--{!! $branch->code==Auth::user()->branch?"{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}":''  !!}--}}
        ],
         "order": [[5, 'desc']]
        /* "columns": [
         { "data": 1 },
         { "data": 9 },
         { "data": 3 },
         { "data": 4 },
         { "data": 2 },
         { "data": 14 },
         ]*/
    });
</script>
@endpush