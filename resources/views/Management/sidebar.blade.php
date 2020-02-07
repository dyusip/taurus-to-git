@if(Auth::user()->position!='CEO' && Auth::user()->position!='CFO')
    <script>window.location='{{ url('/home') }}'</script>
@endif
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                            <!--<img alt="image" class="img-circle" src="../src/img/profile_small.jpg" />-->
                             </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold"> {{ Auth::user()->name }} </strong>
                             </span> <span class="text-muted text-xs block"> {{ Auth::user()->position }}  <b class="caret"></b></span> </span> </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a href="profile">Profile</a></li>
                        <li><a href="contacts.html">Contacts</a></li>
                        <li><a href="mailbox.html">Mailbox</a></li>
                        <li class="divider"></li>
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Logout</a></li>

                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            <li>{{--{!! (Request::is('home') ? 'class="active"' : '') !!}--}}
                <a href="{{ url('/home') }}"><i class="fa fa-th-large"></i> <span class="nav-label">Home</span> </a>
            </li>
            <li>{{--{!! (Request::is('inventory/create') ? 'class="active"' : '') !!}--}}
                <a href="javascript:;"><i class="fa fa-cubes"></i> <span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/salesman/inventory') }}">Inventory Report</a></li>
                    <li><a href="{{ url('/inventory_analysis') }}">Movement Analysis</a></li>
                    <li><a href="{{ url('/category_analysis') }}">Category Analysis</a></li>
                    <li><a href="{{ url('/perf_report') }}">Perf Measure Report</a></li>
                    <li><a href="{{ url('/bi_records') }}">Inventory Record </a></li>
                   <!-- <li><a href="{{ url('/transfer_report') }}">Transfer Report</a></li>
                    <li><a href="{{ url('/transfer_history') }}">Transfer History</a></li>-->
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-cart-plus"></i> <span class="nav-label">Purchase</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/purchase_report') }}">Purchase Report</a></li>
                    <li><a href="{{ url('/receiving_report') }}">Receiving Report</a></li>
                    <li><a href="{{ url('/payable') }}">Payable</a></li>
                </ul>
            </li>
             <li>
                <a href="javascript:;"><i class="fa fa-history"></i> <span class="nav-label">Transfer</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/picklist') }}">Picklist Report</a></li>
                    <li><a href="{{ url('/pl_perf') }}">Picklist PERF</a></li>
                    <li><a href="{{ url('/transfer_report') }}">CW Deliveries</a></li>
                    <li><a href="{{ url('/trans_in') }}">Transfer IN</a></li>
                    <li><a href="{{ url('/trans_out') }}">Transfer OUT</a></li>
                    <li><a href="{{ url('/stock_return') }}">Stock Returns</a></li>
                    <li><a href="{{ url('/transfer_history') }}">Transfer History</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-empire"></i> <span class="nav-label">Miscellaneous</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/miscellaneous_report') }}">Miscellaneous IN</a></li>
                    <li><a href="{{ url('/misc_out') }}">Miscellaneous OUT</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-calendar"></i> <span class="nav-label">Sales</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/salesreport') }}">Sales Report</a></li>
                    <li><a href="{{ url('/saleslogsreport') }}">Sales Logs</a></li>
                    <li><a href="{{ url('/salesreturn_report') }}">Return Report</a></li>
                    <li><a href="{{ url('/erp_report') }}">ERP Report</a></li>
                    <li><a href="{{ url('/act_erp') }}">Actual Trans ERP</a></li>
                    <li><a href="{{ url('/profit') }}">Profit Margin Analysis</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-folder"></i> <span class="nav-label">New Reports</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/nr_purchase_report') }}">Purchase Report</a></li>
                    <li><a href="{{ url('/nr_transfer_report') }}">CW Deliveries</a></li>
                    <li><a href="{{ url('/nr_trans_in') }}">Transfer IN</a></li>
                    <li><a href="{{ url('/nr_trans_out') }}">Transfer OUT</a></li>
                    <li><a href="{{ url('/nr_stock_return') }}">Stock Returns</a></li>
                    <li><a href="{{ url('/nr_transfer_history') }}">Transfer History</a></li>
                    <li><a href="{{ url('/nr_salesreport') }}">Sales Report</a></li>
                    <li><a href="{{ url('/nr_salesreturn_report') }}">Sales Return</a></li>
                    <li><a href="{{ url('/nr_erp_report') }}">ERP Report</a></li>
                    <li><a href="{{ url('/nr_profit') }}">Profit Margin Analysis</a></li>
                    <li><a href="{{ url('/cw_erp') }}">CW ERP</a></li>
                </ul>

            </li>

        </ul>

    </div>
</nav>
@push('scripts')
<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "progressBar": true,
            "preventDuplicates": true,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "0",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut",
            "tapToDismiss": false
        },
            @if (@$tf_count > 0)
                toastr.warning("<a href='/notification/transfer'>You have a pending DR to Approve</a>");
            @endif
            @if (@$po_count > 0)
                toastr.error("<a href='/notification/po'>You have a pending PO to Approve</a>");
            @endif
            @if (@$pr > 0)
                    toastr.warning("<a href='/pr_approval'>You have a pending PR to Approve</a>");
            @endif
});
</script>
@endpush