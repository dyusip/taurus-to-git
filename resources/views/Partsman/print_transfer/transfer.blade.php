@extends('layout.main')

@section('index-content')
    <div id="wrapper">

        <!-- Navigation -->
        @include('Partsman.sidebar')

        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Partsman.header')

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Print Transferred Item</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="/index">Home</a>
                        </li>
                        <li class="active">
                            <strong>Print Transferred Item</strong>
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
                                            <th width="9%">TF CODE</th>
                                            <th data-hide="phone" width="20%">FROM</th>
                                            <th data-hide="phone" width="13%">TO</th>
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
        'ajax': '/transferred/print/id',
        'columns': [
            {data: 1, name: 'transfer_headers.tf_code'},
            {data: 12+'.name', name: 'tf_fr_branch.name'},
            {data: 13+'.name', name: 'tf_to_branch.name'},
            {data: 4, name: 'transfer_headers.tf_date'},
            {data: 14+'.name', name: 'tf_prep_by.name'},
           /* {data: 14+'.name', name: 'tf_app_by.name'},*/
            {data: 15, name: 'action', orderable: false, searchable: false, 'class':'text-center'}
            {{--{!! $branch->code==Auth::user()->branch?"{data: 14, name: 'action', orderable: false, searchable: false, 'class':'text-center'}":''  !!}--}}
        ],
        "order": [[3, 'desc']]
    });
</script>
@endpush