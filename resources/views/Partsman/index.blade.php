@extends('layout.main')

@section('index-content')
<div id="wrapper">

    <!-- Navigation -->
    @include('Partsman.sidebar')

    <div id="page-wrapper" class="gray-bg dashbard-1">
        @include('Partsman.header')

        <div class="row wrapper border-bottom white-bg page-heading">
            <div class="col-lg-10">
                <h2>Dashboard</h2>
                <ol class="breadcrumb">
                    <li>
                        <a href="/index">Home</a>
                    </li>
                    <li class="active">
                        <strong>Dashboard</strong>
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
                            <h5>Search Item</h5>
                            <!--<div class="ibox-tools">
                                <a href="" class="btn btn-primary btn-xs">Create new project</a>
                            </div>-->
                        </div>
                        <div class="ibox-content">
                            <form method="get" action="/home/item" id="myform">
                                {{--{{ csrf_field() }}--}}
                            <div class="row m-b-sm m-t-sm">
                                <div class="col-md-1">
                                    <button type="button" onClick="history.go(0)" id="loading-example-btn" class="btn btn-white btn-sm" ><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                                <div class="col-md-11">
                                    <div class="input-group"><input type="text" value="{{ @$key }}" name="item" onfocus="reprint(this)" onchange="reprint(this)" onblur="reprint(this)" onkeyup="remove(this)" id="typeahead_2" placeholder="Search" class="input-sm form-control"> <span class="input-group-btn">
                                        <button type="submit" id="btn-search" class="btn btn-sm btn-primary"> Go!</button> </span></div>
                                </div>
                            </div>
                            </form>
                            <div class="project-list">
                                <div class="spiner-example" id="main-spinner" style="position: fixed;
    display: none;
    top: -50px;
    left: 0;
    right: 0;
    z-index: 9999; /* Specify a stack order in case you're using a different order for other elements */ padding: 23%; ">
                                    <div class="sk-spinner sk-spinner-wave">
                                        <div class="sk-rect1"></div>
                                        <div class="sk-rect2"></div>
                                        <div class="sk-rect3"></div>
                                        <div class="sk-rect4"></div>
                                        <div class="sk-rect5"></div>
                                    </div>
                                </div>
                                @if(isset($inventories))
                                <table class="table table-hover">
                                    <tbody id="order-item">

                                    @forelse($inventories as $inventory)
                                    <tr>
                                        <td class=\"project-status\">
                                            {!! $inventory->quantity==0?'<span class="label label-danger">Not Available</span>':'<span class="label label-success">In-Stock</span>' !!}
                                        </td>
                                        <td class=\"project-title\">
                                            <a href=\"javascript:;\">{{ $inventory->inventory->name }}</a>
                                            <br/>
                                            <!--<small>Created 14.08.2014</small>-->
                                            <small>UNIT OF MEASURE - {{$inventory->inventory->uom}}</small>
                                        </td>
                                        <td class=\"project-completion\">
                                            <!-- <small>Completion with: 48%</small>
                                             <div class=\"progress progress-mini\">
                                                 <div style=\"width: 48%;\" class=\"progress-bar\"></div>
                                             </div>-->
                                            {{ $inventory->quantity }}
                                        </td>
                                        <td class=\"project-people\">
                                            <!--<a href=\"\"><img alt=\"image\" class=\"img-circle\" src=\"img/a3.jpg\"></a>
                                            <a href=\"\"><img alt=\"image\" class=\"img-circle\" src=\"img/a1.jpg\"></a>
                                            <a href=\"\"><img alt=\"image\" class=\"img-circle\" src=\"img/a2.jpg\"></a>
                                            <a href=\"\"><img alt=\"image\" class=\"img-circle\" src=\"img/a4.jpg\"></a>
                                            <a href=\"\"><img alt=\"image\" class=\"img-circle\" src=\"img/a5.jpg\"></a>-->
                                            {{ $inventory->branch->name }}
                                        </td>
                                        <td class=\"project-actions\">
                                            <!--<a href=\"#\" class=\"btn btn-white btn-sm\"><i class=\"fa fa-folder\"></i> View </a>
                                            <a href=\"#\" class=\"btn btn-white btn-sm\"><i class=\"fa fa-pencil\"></i> Edit </a>-->
                                            ₱{{ $inventory->price }}
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan='5' class='project - title '>
                                                <span class='text-success'>Your search - {{ @$key }} - did not match any items. </span>
                                                <br>
                                                <p style=\"margin-top:1em\">Suggestions:</p>
                                                <ul style=\"margin-left:1.3em;margin-bottom:2em\"><li>Make sure that all words are spelled correctly.</li><li>Try different keywords.</li><li>Try more general keywords.</li></ul>
                                            </td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                    {!! $inventories->appends(Request::except('page'))->render() !!}
                            @endif
                                <table class="table table-hover">
                                    <tbody id="order-items">
                                    </tbody>
                                </table>


                                <!--<nav class='pagination' aria-label="...">
                                    <ul class="pager">
                                        <li><a href="#fh5co-explore" class='previous'>&lt;&lt;Previous</a></li>
                                        <span class='current'></span>
                                        <li><a href="#fh5co-explore" class='next'>Next&gt;&gt;</a></li>
                                    </ul>
                                </nav>-->
                            </div>
                        </div>
                    </div>
                    <!--End of Search -->
                    <div class="row">
                        <div class="col-md-12">


                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Sales Report</h5>
                                    <!--<div class="ibox-tools">
                                        <a href="" class="btn btn-primary btn-xs">Create new project</a>
                                    </div>-->
                                </div>
                                <div class="ibox-content">
                                    <div class="btn-group">
                                        <button class="btn btn-white btn-sm previous" id="prev" onclick="prevMonth()"><i
                                                    class="fa fa-chevron-left"></i></button>
                                        <span class="current"></span>
                                        <button class="btn btn-white btn-sm next" id="next" onclick="nextMonth()"><i
                                                    class="fa fa-chevron-right"></i></button>
                                    </div>
                                    <button type="button" id="btn-month"
                                            class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled"
                                            disabled="disabled">{{ Carbon\Carbon::now()->format('F Y') }}</button>
                                    <button type="button" id="btn-today"
                                            class="fc-today-button fc-button fc-state-default fc-corner-left fc-corner-right fc-state-disabled">Today {{ Carbon\Carbon::now()->format('F d, Y') }}</button>
                                    <input type="hidden" id="dt-month" value="{{ Carbon\Carbon::now()->format('Y-n') }}">
                                    <div class="clearfix"></div>
                                    <div class="row">

                                        <div class="col-md-7 b-r">
                                            <table class="table table-hover no-margins">
                                                <thead>
                                                <tr>
                                                    <th>Branch</th>
                                                    <th width="25%">Goal</th>
                                                    <th>Sales</th>
                                                    <th>Perf</th>
                                                </tr>
                                                </thead>
                                                <tbody id="performance">

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="col-md-5">
                                            <div id="morris-donut-chart"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End of Sales Report-->
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
<link href="{{  asset('css/plugins/morris/morris-0.4.3.min.css') }}" rel="stylesheet">
@endpush
@push('scripts')
<script src="{{  asset('/js/plugins/typehead/bootstrap3-typehead.min.js') }}"></script>
<script src="{{  asset('js/plugins/morris/raphael-2.1.0.min.js') }}"></script>
<script src="{{  asset('js/plugins/morris/morris.js') }}"></script>
<script>
   /*$(document).on('click','#btn-search',function () {
       var item = $('#typeahead_2').val();
       $.ajax({
           url: "home/"+item,
           beforeSend: function() {
               $('#main-spinner').fadeIn();
           },
           success: function(output) {
               $('#main-spinner').fadeOut();
               $.each(output.data, function (index, value) {
                   $('tbody').append('<tr> ' +
                       '<td class=\"project-status\"> </td> ' +
                       '<td class=\"project-title\"> ' +
                       '<a href=\"javascript:;\"></a> ' +
                       '<br/> ' +
                       '<small>UNIT OF MEASURE - '+ value.price +'</small> ' +
                       '</td> ' +
                       '<td class=\"project-completion\"> ' +
                       '</td> ' +
                       '<td class=\"project-people\"> ' +
                       '</td> ' +
                       '<td class=\"project-actions\"> ' +
                       '</td> ' +
                       '</tr>');
               });

           },
           error: function (xhr, ajaxOptions, thrownError) {
               alert(xhr.status + " "+ thrownError);
           }
       });
   });*/
   $.get('/home/{id}/item', function(data){
       $("#typeahead_2").typeahead({
           source:data.ITEM,
           autoSelect: false,
           updater : function(item) {
               $('#typeahead_2').val(item);
               $('#myform').submit();
               return item;
           }
       });
   },'json');
   $('#typeahead_2').keydown(function (e) {
       var key = e.which;
       var item = $(this).val();
       if(key == 13 && $(this).val()!="")  // the enter key code
       {
           $('#typeahead_2').val(item);
           $('#myform').submit();
       }
   });
   $('form input').on('keypress', function(e) {
       return e.which !== 13;
   });
</script>
<script>
    $(document).ready(function () {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() ,1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        Donut_chart(first,last)
    });
    function Donut_chart(first, last) {
        $.ajax({
            url : '/home/salesreport/'+first+"/"+last,
            beforeSend: function() {
                $('#main-spinner').fadeIn();
            },
            success: function (data) {
                $('#main-spinner').fadeOut();
                var val = [];
                $('#performance').html("");
                $.each(data, function (index, value) {
                    val.push({'label' : value.so_branch.name, 'value': value.total.toFixed(1)});
                    var goal = (Number(value.total) / value.so_branch.goal) * 100;
                    goal = Number.isInteger(goal)?goal:goal.toFixed(1);
                    $('#performance').append(" <tr> " +
                        "<td><small>"+ value.so_branch.name +"</small></td> " +
                        "<td width='20%'> <input type='text' class='form-control' id='goal' value='"+value.so_branch.goal+"'></td> " +
                        "<td id='sales'>"+ formatDollar(value.total) +"</td> " +
                        "<td class='text-navy'> <i class='fa fa-level-up'></i> <span id='perf'>"+ Math.round(goal)+"%</span> </td> " +
                        "</tr>")
                });
                console.log(val.length);
                var color = [];
                var pick = ['#87d6c6', '#54cdb4','#1ab394', '#37afbd',
                    '#53b5ad', '#16acba','#47c5d1','#42f4d7','#37725c',
                    '#106344','#9bf2e6','#10e8ca','#5dc5e2','#94dbef',
                    '#94efdd','#509184','#26574d','#268c80','#05a18e',
                    '#11bdbd','#3eadad','#19e3bb','#104f4f'];

                for (var i = 0; i < val.length;i++) {
                    color.push(pick[i])
                }
                $('#morris-donut-chart').empty();
                Morris.Donut({
                    element: 'morris-donut-chart',
                    data: val,
                    resize: true,
                    colors: color
                });
            }
        });
    }
    function formatDollar(num) {
        var p = num.toFixed(2).split(".");
        return "₱" + p[0].split("").reverse().reduce(function(acc, num, i, orig) {
                return  num=="-" ? acc : num + (i && !(i % 3) ? "," : "") + acc;
            }, "") + "." + p[1];
    }

    var month = new Array();
    month = ["January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];
    $(document).on('click','#btn-today',function () {
        var today = new Date();
        var date = (today.getMonth()+1)+'-'+today.getDate()+'-'+today.getFullYear();
        Donut_chart(date,date)
    });

    function prevMonth() {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() - 1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        $('#dt-month').val(d.getFullYear() + "-"+ (d.getMonth() + 1));
        $('#btn-month').text(month[d.getMonth()]+" "+d.getFullYear());
        Donut_chart(first,last)
    }
    function nextMonth() {
        var d = new Date($('#dt-month').val());
        d.setDate(1);
        d.setMonth(d.getMonth() + 1);
        var first = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        d.setMonth(d.getMonth() +1,0);
        var last = d.getMonth()+1 + "-" + d.getDate() + "-" + d.getFullYear();
        $('#dt-month').val(d.getFullYear() + "-"+ (d.getMonth() + 1));
        $('#btn-month').text(month[d.getMonth()]+" "+d.getFullYear());
        Donut_chart(first,last)
    }
    $('tbody').delegate('#goal','keyup',function () {
        var tr = $(this).parent().parent();
        var goal = $(this).val();
        var sales = tr.find('#sales').text();
        sales = Number(sales.replace(/[^0-9\.]+/g,""));
        var perf = (Number(sales) / Number(goal)) * 100;
        perf = Number.isInteger(perf)?perf:perf.toFixed(1);
        tr.find('#perf').html(Math.round(perf)+"%");
    });

</script>
@endpush