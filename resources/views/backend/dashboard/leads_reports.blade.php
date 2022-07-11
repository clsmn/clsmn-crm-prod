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

        .has-spinner.active {
           cursor: progress;
           }
       .has-spinner.active .spinner {
           opacity: 1;
           max-width: 50px;
           /* More than it will ever come, notice that this affects on animation duration */
        }
        
    </style>
       <script>

            window.onload = function () {

                var currentYear = new Date().getFullYear();
                var pastYear = new Date().getFullYear()  - 1;
                var pastYear1 = new Date().getFullYear()  - 2;

                //Daily Graph Leads Start 
                var chart = new CanvasJS.Chart("chartContainerLeadsDaily", {
                    title:{
                        text: ""   ,           
                    },
                    axisX:{
                           interval: 1
                        },
                    data: [              
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: <?php echo $leads_daily ?>
                    }
                    ]
                });
                chart.render();
                //Daily Graph Leads End


                //Monthly lead performance Starts

                var chartMonth = new CanvasJS.Chart("chartContainerMonthly", {
                    title:{
                        text: ""   ,           
                    },
                    axisY: {
                        title: "Conversion Rate (in %)",
                        suffix: "%"
                    },
                    axisX: {
                        title: "Source",
                        interval: 1
                    },
                    
                    data: [              
                    {
                        // Change type to "doughnut", "line", "splineArea", etc.
                        type: "column",
                        dataPoints: <?php echo $leads_monthlyPerformance ?>
                    }
                    ]
                });
                chartMonth.render();
                //Monthly lead performance Ends

            }

        </script>
@endsection

@section('page-header')
    <h1>Reports</h1>
    <div class="pull-right">
     
    </div>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3><span id="todaySales">{{$today_leads}}</span></h3>

        <p>Today's Leads <span style="font-size: 12px;"></span></p>
      </div>
      <div class="icon">
        <i class="ion ion-bag"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3>{{$yesterday_leads}}</h3>

        <p>Yesterday's Leads</p>
      </div>
      <div class="icon">
        <i class="ion ion-stats-bars"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3><span id="currentMonthSales">{{$currentMonth_leads}}</span></h3>

        <p>Current Month's Leads <span style="font-size: 12px;"></span></p>
      </div>
      <div class="icon">
        <i class="ion ion-person-add"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>{{$lastMonth_leads}}</h3>

        <p>Last Month's Leads</p>
      </div>
      <div class="icon">
        <i class="ion ion-pie-graph"></i>
      </div>
    </div>
  </div>
  <!-- ./col -->
</div>
<div class="row">
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Daily Leads Chart (Basis on Data Bank Data)</h3>
                <div class="box-tools pull-right">
                    <div class="pull-right mb-10">

                    </div>
                    <div class="clearfix"></div>
                </div><!--box-tools pull-right-->
            </div><!-- /.box-header -->
            <div class="box-body" style="overflow-x: scroll;">
               <div id="chartContainerLeadsDaily" style="height: 370px; width: 100%; max-width: 3000px;"></div>
            </div><!-- /.box-body -->
        </div><!--box-->
    </div>
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Monthly Lead Conversion Rate (Basis on Data Bank Data)</h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">

                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body" style="overflow-x: scroll; width: 100%;">
                   <div id="chartContainerMonthly" style="height: 450px; width:7000px"></div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">    
            <div class="box box-success">
                <ul class="nav nav-tabs" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" href="#profile" role="tab" data-toggle="tab">Active Source</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="#buzz" role="tab" data-toggle="tab">In-active Source</a>
                  </li>
                </ul>
                <div class="tab-content">
                  <div role="tabpanel" class="tab-pane fade in active" id="profile">
                        <div class="box-header with-border">
                            <h3 class="box-title">Leads Conversion By Data Source<span id="l30">- 30 Days Active </span> (Basis on Learning Subscription and Data Bank Table Data)</h3>
                              <span class="has-spinner" id="calendar-filter" style="display:none;">
                              <span class="spinner"><i class="fa fa-refresh fa-spin"></i></span></span>
                            <span id="activeDate"></span>
                            <div class="box-tools pull-right">
                                <div class="pull-right mb-10">
                                    <div id="datePickerConversion" style="background: #fff; cursor: pointer; padding: 5px 5px; border: 1px solid #ccc; position: absolute;right: 170px;margin-right: 20px;width: max-content;">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                                     </div>
                                </div>
                                     
                                <div class="clearfix"></div>

                            </div><!--box-tools pull-right-->
                        </div><!-- /.box-header -->
                        <div class="box-body">
                           <table class="main-table table-hover table-striped mt-5" style="width:100%;margin-top: 20px;border-collapse: separate;    border-spacing: 0 1em;" id="activeLeads-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Lead Source Name</th>
                                <th>Total Subscription</th>
                                <th>Total Leads</th>
                                <th>Percentage</th>
                              </tr>
                            </thead>
                            <tbody id="table_activeLeads">
                               
                                @if($sourceConversionTable)
                                    @php $i=1; $j=0; @endphp
                                    @foreach ($sourceConversionTable as $ConversionTable)
                                    @if($ConversionTable['percentage'] <= 100)
                                      <tr>
                                        <td>{{$i}}</td>
                                        <td>{{$ConversionTable['source_data']}}</td>
                                        <td>{{$ConversionTable['totalSubs']}}</td>
                                        <td>{{$ConversionTable['Totalleads']}}</td>
                                        <td>{{$ConversionTable['percentage']}}%</td>
                                      </tr>
                                      @php $i++; $j++; @endphp
                                      @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" >No data found!</td>
                                    </tr>
                                @endif
                            </tbody>
                          </table>
                          
                        </div><!-- /.box-body -->
                  </div>
                  <div role="tabpanel" class="tab-pane fade" id="buzz">
                       <div class="box-header with-border">
                            <h3 class="box-title">Leads Conversion By Data Source<span id="l31">- 30 Days In-active </span>(Basis on Learning Subscription and Data Bank Table Data)</h3>
                            <span class="has-spinner" id="calendar-filter1" style="display:none;">
                              <span class="spinner"><i class="fa fa-refresh fa-spin"></i></span></span>
                            <span id="activeDate1"></span>
                            <div class="box-tools pull-right">
                                <div class="pull-right mb-10">
                                    <div class="pull-right mb-10">
                                    <div id="datePickerConversionInactive" style="background: #fff; cursor: pointer; padding: 5px 5px; border: 1px solid #ccc; position: absolute;right: 170px;margin-right: 20px; width: max-content;">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                                     </div>
                                </div>
                                </div>
                                <div class="clearfix"></div>
                            </div><!--box-tools pull-right-->
                        </div><!-- /.box-header -->
                        <div class="box-body">
                           <table class="main-table table-hover table-striped mt-5" style="width:100%;margin-top: 20px;border-collapse: separate;    border-spacing: 0 1em;" id="inactiveLeads-table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Lead Source Name</th>
                                <th>Total Subscription</th>
                                <th>Total Leads</th>
                              </tr>
                            </thead>
                            <tbody id="table_inactiveLeads">
                               
                                @if($sourceConversionTable)
                                    @php $ii=1; @endphp
                                    @foreach ($sourceConversionTable as $ConversionTable)
                                    @if($ConversionTable['percentage'] > 100)
                                      <tr>
                                        <td>{{$ii}}</td>
                                        <td>{{$ConversionTable['source_data']}}</td>
                                        <td>{{$ConversionTable['totalSubs']}}</td>
                                        <td>{{$ConversionTable['Totalleads']}}</td>
                                      </tr>
                                      @php $ii++; @endphp
                                      @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" >No data found!</td>
                                    </tr>
                                @endif
                            </tbody>
                          </table>
                          
                        </div><!-- /.box-body -->
                  </div>
                </div>
                
            </div><!--box-->
        </div>
    </div>
</div>
    
@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    
    <script type="text/javascript">
        $(function() {
      var startDate = '';
      var endDate = '';
   
      var start = moment();
      var end = moment();
   
      function cb(start, end) {
          $('#datePickerConversion span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
    
     $('#datePickerConversion').daterangepicker({
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
   
    $('#datePickerConversion').on('apply.daterangepicker', function(ev, picker) {
        cb(picker.startDate, picker.endDate);
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        $('#calendar-filter').show();   
        $.ajax({
            url : baseURL + '/admin/lead/getleadConversionByDate',
            type : 'get',
            dataType: "html",
            'data' : 'startDate='+startDate+'&endDate='+endDate+'&status=Active',
            success: function(response)
            {
                $('#table_activeLeads').html(response);
                $('#l30').hide();
                $('#activeDate').html('<p>Show results between '+startDate+' - '+endDate+' </p>')
                $('#calendar-filter').hide();   
                
            }
        });
   
    });
   
    $('#datePickerConversion').on('cancel.daterangepicker', function(ev, picker) {
        $('#datePickerConversion span').html('Select Date Range');
        startDate = '';
        endDate = '';
    });
   });
    </script>

    <script type="text/javascript">
        $(function() {
      var startDate = '';
      var endDate = '';
   
      var start = moment();
      var end = moment();
   
      function cb(start, end) {
          $('#datePickerConversionInactive span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
    
     $('#datePickerConversionInactive').daterangepicker({
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
   
    $('#datePickerConversionInactive').on('apply.daterangepicker', function(ev, picker) {
        cb(picker.startDate, picker.endDate);
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        $('#calendar-filter1').show();   
        $.ajax({
            url : baseURL + '/admin/lead/getleadConversionByDate',
            type : 'get',
            dataType: "html",
            'data' : 'startDate='+startDate+'&endDate='+endDate+'&status=InActive',
            success: function(response)
            {
                $('#table_inactiveLeads').html(response);
                $('#l31').hide();
                $('#activeDate1').html('<p>Show results between '+startDate+' - '+endDate+' </p>')
                $('#calendar-filter1').hide();   
                
            }
        });
   
    });
   
    $('#datePickerConversionInactive').on('cancel.daterangepicker', function(ev, picker) {
        $('#datePickerConversionInactive span').html('Select Date Range');
        startDate = '';
        endDate = '';
    });
   });
    </script>
@endsection
