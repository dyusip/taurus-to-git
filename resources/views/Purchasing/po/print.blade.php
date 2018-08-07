@extends('layout.main')

@section('index-content')
    <div id="wrapper">
        @include('Purchasing.sidebar')
        <div id="page-wrapper" class="gray-bg dashbard-1">
            @include('Purchasing.header')
        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Print Purchase Order</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="dashboard.php">Home</a>
                    </li>
                    <li class="active">
                        <strong>Print Purchase Order</strong>
                    </li>
                </ol>
            </div>
            <div class="col-lg-2">

            </div>
        </div>

        <form method="POST">
            {{ csrf_field() }}
            <div class="wrapper wrapper-content animated fadeInRight">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-content text-center p-md">

                                <h2><span class="text-navy">PRINT - Purchase Order No</span>
                                    is provided to print or reprint purchase order <br/>for reference only</h2>

                                <p>
                                    Data loaded from delivered order<span class="text-muted"> (type 'PO...')</span>
                                </p>
                                <input type="text" onfocus="reprint(this)" onchange="reprint(this)" onblur="reprint(this)" onkeyup="remove(this)" autocomplete="off" name="reprint_DR" placeholder="PO No..." class="typeahead_2 form-control" style="text-transform: uppercase" />
                                <input type="hidden" class="form-control" name="PO_No" id="PO_No" value=""/>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <span class="label label-success pull-right">Copy</span>
                            <h5>Purchase Order</h5>
                        </div>
                        <div class="ibox-content">
                            <button class="btn btn-primary btn-block m-t" formaction="/po_pdf" formtarget="_blank"><i class="fa fa-arrow-down"></i> Print</button>
                            <!--<div class="stat-percent font-bold text-success">98% <i class="fa fa-bolt"></i></div>-->
                            <small>Purchase Order</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="ibox float-e-margins">
                        <!--<div class="ibox-title">
                            <span class="label label-success pull-right">Copy</span>
                            <h5>Purchase Order</h5>
                        </div>-->
                        <div class="ibox-content">
                            <img src="/img/TaurusLogopng.png" style="height: 85px">
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- /#page-wrapper -->
        <div class="footer">
            <div class="pull-right">
                <strong></strong>
            </div>
            <div>
                <strong>Copyright</strong> INFOZ-ITWORKS &copy; 2017
            </div>
        </div>
        </div>
    </div>
@endsection
@push('scripts')
<script src="{{  asset('/js/plugins/typehead/bootstrap3-typehead.min.js') }}"></script>
<script>
    $.get('/searchPO', function(data){
        $(".typeahead_2").typeahead({
            source:data.DRNO
        });
    },'json');
    function reprint(fieldObj) {
        var FileName = fieldObj.value.toUpperCase();
        $('#PO_No').val(FileName.substr(0, 10));
        if(!FileName){
            $('.btn-block').prop('disabled', true);
        }else{
            $('.btn-block').prop('disabled', false);
        }

    }
    function remove(fieldObj) {
        var FileName = fieldObj.value.toUpperCase();
        if(!FileName){
            $('.btn-block').prop('disabled', true);
        }
    }
    $('form input').on('keypress', function(e) {
        return e.which !== 13;
    });
    $(function () {
        $('.btn-block').prop('disabled', true);
    });
</script>
@endpush
