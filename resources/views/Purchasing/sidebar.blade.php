@if(Auth::user()->position!='PURCHASING' && Auth::user()->position!='AUDIT-OFFICER')
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
                        <li><a href="/profile">Profile</a></li>
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
                    <li><a href="{{ url('/inventory/create') }}">Master List</a></li>
                    <li><a href="{{ url('/branch_inventory/create') }}">Branch Inventory </a></li>
                    <li><a href="{{ url('/inventory_analysis') }}">Inventory Analysis </a></li>
                    <li><a href="{{ url('/category_analysis') }}">Category Analysis </a></li>
                    <li><a href="{{ url('/miscellaneous') }}">Miscellaneous </a></li>
                    <li><a href="{{ url('/bi_records') }}">Inventory Record </a></li>
                </ul>
            </li>
            <li>
                <a href="{{ url('/supplier/create') }}"><i class="fa fa-user"></i> <span class="nav-label">Supplier</span></a>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-briefcase"></i> <span class="nav-label">Purchase Order</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/po/create') }}">Create</a></li>
                    <li><a href="{{ url('/printpo') }}">Print</a></li>
                    <li><a href="{{ url('/receiving/create') }}">Receiving</a></li>
                    <li><a href="{{ url('/receiving_list') }}">Receiving List</a></li>
                    <li class="{{ Auth::user()->position!='AUDIT-OFFICER'?'hide':'' }}"><a href="{{ url('/payable') }}">Payable</a></li>
                    <li><a href="{{ url('/payment') }}">Payment</a></li>
                    <li><a href="{{ url('/gen_pr') }}">Generate PR</a></li>

                </ul>
            </li>
            {{--<li>
                <a href="{{ url('/transfer/create') }}"><i class="fa fa-mail-forward"></i> <span class="nav-label">Transfer Item</span> </a>
            </li>--}}
            <li>
                <a href="javascript:;"><i class="fa fa-forward"></i> <span class="nav-label">Transfer</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/transfer/create') }}">Transfer Item</a></li>
                    <li><a href="{{ url('/pl_print') }}">Pick List</a></li>
                    <li><a href="{{ url('/transferred/print') }}">DR List</a></li>
                    <li><a href="{{ url('/picklist') }}">Picklist Report</a></li>
                    <li><a href="{{ url('/transfer_report') }}">CW Deliveries</a></li>
                    <li><a href="{{ url('/trans_in') }}">Transfer IN</a></li>
                    <li><a href="{{ url('/trans_out') }}">Transfer OUT</a></li>
                    <li><a href="{{ url('/stock_return') }}">Stock Returns</a></li>
                    <li><a href="{{ url('/transfer_history') }}">Transfer History</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-archive"></i> <span class="nav-label">Reports</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    {{--<li><a href="{{ url('/transfer_report') }}">Transfer</a></li>
                    <li><a href="{{ url('/transfer_history') }}">Transfer History</a></li>--}}
                    <li><a href="{{ url('/purchase_report') }}">Purchase</a></li>
                    <li><a href="{{ url('/receiving_report') }}">Receiving</a></li>
                    <li><a href="{{ url('/miscellaneous_report') }}">Miscellaneous IN</a></li>
                    <li><a href="{{ url('/misc_out') }}">Miscellaneous OUT</a></li>
                    <li class="{{ Auth::user()->position!='AUDIT-OFFICER'?'hide':'' }}"><a href="{{ url('/salesreport') }}">Sales</a></li>
                    <li class="{{ Auth::user()->position!='AUDIT-OFFICER'?'hide':'' }}"><a href="{{ url('/saleslogsreport') }}">Sales Logs</a></li>
                    <li class="{{ Auth::user()->position!='AUDIT-OFFICER'?'hide':'' }}"><a href="{{ url('/salesreturn_report') }}">Return</a></li>
                    <li class="{{ Auth::user()->position!='AUDIT-OFFICER'?'hide':'' }}"><a href="{{ url('/erp_report') }}">ERP</a></li>
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
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-cloud-upload"></i> <span class="nav-label">Import</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="{{ url('/import/create') }}">Item</a></li>
                    <li><a href="{{ url('/import/create/branch') }}">Branch Item</a></li>
                    <li><a href="{{ url('/sales_import/create') }}">Sales</a></li>
                    <li><a href="{{ url('/import_invupdate/create') }}">Update Inventory</a></li>
                    <li><a href="{{ url('/import/update/branch') }}">Branch Update</a></li>
                </ul>
            </li>
            {{--<li>
                <a href="javascript:;"><i class="fa fa-shopping-cart"></i> <span class="nav-label">Sales Order</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="Sales" id="btn-Sales-page">Create</a></li>
                    <li><a href="Return" id="btn-Return-page">Return</a></li>
                </ul>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-cube"></i> <span class="nav-label">Inventory</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="Inventory_create">Create</a></li>
                    <li><a href="Inventory">Update</a></li>
                </ul>
            </li>
            <li>
                <a href="Sales_Report"><i class="fa fa-calendar"></i> <span class="nav-label">Sales Report</span></a>
            </li>
            <li>
                <a href="Incentive"><i class="fa fa-money"></i> <span class="nav-label">Incentive</span></a>
            </li>
            <li>
                <a href="javascript:;"><i class="fa fa-user"></i> <span class="nav-label">Account </span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="create_mechanic">Create</a></li>
                    <li><a href="update_mechanic">Update</a></li>
                </ul>
            </li>--}}

        </ul>
    </div>
</nav>