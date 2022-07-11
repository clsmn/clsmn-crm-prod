@extends ('backend.layouts.app')

@section ('title', 'Reports')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .canvasjs-chart-credit
        {
            display: none;
        }
        #myInput {
          background-image: url('/css/searchicon.png');
          background-position: 10px 10px;
          background-repeat: no-repeat;
          width: 100%;
          font-size: 16px;
          padding: 12px 20px 12px 40px;
          border: 1px solid #ddd;
          margin-bottom: 12px;
        }
    </style>
       <script>

            window.onload = function () {

                var currentYear = new Date().getFullYear();
                var pastYear = new Date().getFullYear()  - 1;
                var pastYear1 = new Date().getFullYear()  - 2;

                //Daily Bar Graph Sales Start   
                var chart = new CanvasJS.Chart("chartContainer", {
                    title:{
                        text: ""   ,           
                    },
                    data: [              
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: <?php echo $sales_daily ?>
                    }
                    ]
                });
                chart.render();
                //Daily Bar Graph Sales Ends

                //Monthly Graph Sales Start
                var chart1 = new CanvasJS.Chart("chartContainer1", {
                    theme:"light2",
                    // animationEnabled: true,
                    title:{
                        text: ""              
                    },
                    toolTip: {
                        shared: "true"
                    },
                    legend:{
                        cursor:"pointer",
                        // itemclick : toggleDataSeries
                    },
                    data: [              
                   
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "spline",
                        showInLegend: true,
                        yValueFormatString: "##",
                        name: '"'+currentYear+'"',
                        dataPoints: <?php echo $sales_monthly ?>,
                    },
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "spline",
                        showInLegend: true,
                        yValueFormatString: "##",
                        name: '"'+pastYear+'"',
                        dataPoints: <?php echo $sales_monthly1 ?>,
                    },
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "spline",
                        showInLegend: true,
                        yValueFormatString: "##",
                        name: '"'+pastYear1+'"',
                        dataPoints: <?php echo $sales_monthly2 ?>,
                    }

                    ]
                });
                chart1.render();
                //Monthly Graph Sales Start

               var options = {
                    title:{
                        text: ""
                    },
                    legend:{
                        horizontalAlign: "right",
                        verticalAlign: "center"
                    },
                    data: [{
                        type: "pie",
                        startAngle: 45,
                        showInLegend: "true",
                        legendText: "{label}  (#percent%)",
                        indexLabel: "{label} ({y})",
                        yValueFormatString:"#,##0.#"%"",
                        dataPoints: <?php echo $yearly_sales ?>
                    }]
                };
                $("#chartContainerYearly").CanvasJSChart(options);
            }

        </script>
@endsection

@section('page-header')
    <h1>Reports  <span><button type="button" class="btn btn-success btn-xs" onclick="refreshSalesData()" >Refresh Sales Data</button></span></h1>
    <div class="pull-right">
        
     
    </div>
@endsection

@section('content')
<div class="row mt-5">
    <div>
    </div>
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
        <span onclick="refreshSalesData()" class="pull-right" style="padding: 5px 5px 0px 0px; cursor:pointer;display:none;"><i class="fa fa-refresh"></i></span>
      <div class="inner">
        <h3><span id="todaySales">{{$today_sales}}</span></h3>
        <p>Today's Sales <span style="font-size: 12px;"></span></p>
      </div>
      <div class="icon">
        <i class="ion ion-bag"></i>
      </div>
      <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{$yesterday_sales}}</h3>

        <p>Yesterday's Sales</p>
      </div>
      <div class="icon">
        <i class="ion ion-stats-bars"></i>
      </div>
      <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><span id="currentMonthSales">{{$currentMonth_sales}}</span></h3>

        <p>Current Month's Sales <span style="font-size: 12px;"></span></p>
      </div>
      <div class="icon">
        <i class="ion ion-person-add"></i>
      </div>
      <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{$lastMonth_sales}}</h3>

        <p>Last Month's Sales</p>
      </div>
      <div class="icon">
        <i class="ion ion-pie-graph"></i>
      </div>
      <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
    </div>
  </div>
  <!-- ./col -->
</div>
<div class="row">
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Daily Sales Chart - Last 30 Days (Basis on Learning Subscription Data)</h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">

                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body">
                   <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
    <div class=" col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display:none;">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">    
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Monthly Sales Chart (Basis on Learning Subscription Data)</h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">

                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body">
                   <div id="chartContainer1" style="height: 370px; width: 100%;"></div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
    <div class=" col-lg-4 col-md-4 col-sm-12 col-xs-12" style="display:none;">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Yearly Sales (Basis on Learning Subscription Data)</h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">

                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body">
                   <div id="chartContainerYearly" style="height: 370px; width: 100%;"></div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">    
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Sales Table By Data Source <span id="cmonth">- Current Month</span> (Basis on Learning Subscription Data)</h3>
                    <p class="mt-3 text-red" id="activeDate">Total sales between <span id="dateBetween">{{$startDate}} - {{$endDate}}</span></p>
                     <p class="mt-3" style="display:none;"><strong>Total Kits - <span id="total_count"></span></strong></p>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">
                            <div id="datePickerSale" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 310px;position: absolute;right: 0px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body">
                   <!-- <input type="text" id="myInput" onkeyup="sourceSearch()" placeholder="Search for source.." title="Type in a source"> -->
                   <table class="main-table table-hover table-striped mt-5" style="width:100%;margin-top: 20px;border-collapse: separate; border-spacing: 0 1em;" id="Sales-table">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Lead Source Name</th>
                        <th>Total Sale</th>
                      </tr>
                    </thead>
                    <tbody id="table_sales">
                        @if($data_source)
                            @php $i=1; $count = 0; @endphp
                            @foreach ($data_source as $source)
                              <tr>
                                <td>{{$i}}</td>
                                 @if($source->data_medium == "")
                                <td>No Source</td>
                                @else
                                <td>{{$source->data_medium}}</td>
                                @endif
                                <td>{{$source->total}}</td>
                              </tr>
                              @php $i++; $count = $source->total + $count; @endphp
                            @endforeach
                             <tr>
                                <td></td>
                                <td><strong>Total</strong></td>
                                <td><strong>{{$count}}</strong></td>
                              </tr>
                        @else
                            <tr>
                                <td colspan="3" >No data found!</td>
                            </tr>
                        @endif
                    </tbody>
                  </table>
                  
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
</div>
    
@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript">
    function sourceSearch() {
              var input, filter, table, tr, td, i, txtValue;
              input = document.getElementById("myInput");
              filter = input.value.toUpperCase();
              table = document.getElementById("Sales-table");
              tr = table.getElementsByTagName("tr");
              for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0];
                if (td) {
                  txtValue = td.textContent || td.innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                  } else {
                    tr[i].style.display = "none";
                  }
                }       
              }
            }
 
   function refreshSalesData()
   {
    $.ajax({
            url: '{{ route("admin.reports.saleData") }}',
            type : 'get',
            success: function(response)
            {
                // console.log(response);
              location.reload();
            }
        });
   }

  </script>
  <script type="text/javascript">
        $(function() {
      var startDate = '';
      var endDate = '';
   
      var start = moment();
      var end = moment();
   
      function cb(start, end) {
          $('#datePickerSale span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
    
     $('#datePickerSale').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        },
        opens: 'left',
        ranges: {
        'Today': [moment(), moment()],
        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });
   
    $('#datePickerSale').on('apply.daterangepicker', function(ev, picker) {
        cb(picker.startDate, picker.endDate);
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        $('#calendar-filter').show();   
        $.ajax({
            url : baseURL + '/admin/lead/getSalesByDate',
            type : 'get',
            dataType: "html",
            'data' : 'startDate='+startDate+'&endDate='+endDate,
            success: function(response)
            {
                $('#table_sales').html(response);
                $('#l30').hide();
                $('#activeDate').html('<p>Show results between '+startDate+' - '+endDate+' </p>')
                $('#calendar-filter').hide();   
                $('#cmonth').hide();   
                
            }
        });
   
    });
   
    $('#datePickerSale').on('cancel.daterangepicker', function(ev, picker) {
        $('#datePickerSale span').html('Select Date Range');
        startDate = '';
        endDate = '';
    });
   });
    </script>
@endsection
