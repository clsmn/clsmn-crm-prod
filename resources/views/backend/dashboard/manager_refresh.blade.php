@extends('backend.layouts.app')

@section('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
    
@endsection
<style type="text/css">
        
#overlay{   
      position: fixed;
      top: 0;
      left: 0;
      z-index: 100;
      width: 100%;
      height:100%;
      display: none;
      background: rgba(0,0,0,0.6);
    }
.cv-spinner {
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;  
    }
.spinner {
      width: 40px;
      height: 40px;
      border: 4px #ddd solid;
      border-top: 4px #2e93e6 solid;
      border-radius: 50%;
      animation: sp-anime 0.8s infinite linear;
    }
@keyframes sp-anime {
      100% { 
        transform: rotate(360deg); 
      }
    }
    .is-hide{
      display:none;
    }
.title-text
{
    text-align: center;
    margin-top: 100px;
}
tbody {
    display:block;
    height:500px;
    overflow:auto;
}
thead, tbody tr {
    display:table;
    width:100%;
    table-layout:fixed;
}
thead {
    
}
#myInput {
    background-position: 10px 10px;
    background-repeat: no-repeat;
    font-size: 10px;
    padding: 5px 5px 5px 5px;
    border: 1px solid #ddd;
    margin: 12px 0px 5px 10px;
    float: left;
}
</style>
@section('content')
    <div id="overlay">
      <div class="cv-spinner">
        <span class="spinner"></span>
      </div>
    </div>
    <div class="callout callout-info">
        <h4>Welcome, {{  $logged_in_user->name }}</h4>
        {{-- <p>Tip of the day to get more sales. Pitch on well.</p> --}}
    </div>
    <div class="row ">
        <div class="box-tools pull-right">
               <form action="{{ route('admin.getCalculation') }}" method="post">
                <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 5px; border: 1px solid #ccc; width: 310px;position: absolute;right: 140px;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span>
                        {{ date('F d, Y', strtotime($startDate)).' - '.date('F d, Y', strtotime($endDate)) }}
                    </span> <i class="fa fa-caret-down"></i>
                </div>
                <input type="hidden" name="endDate" id="endDate" value="">
                <input type="hidden" name="startDate" id="startDate" value="">
                <div class="pull-right mb-10">
                    
                    <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                    <a onclick="getCalculations();" class="btn btn-success" style="margin-right: 30px;"> Calculate</a>
                    <button type="submit" id="calculationButton" class="btn btn-success" style="margin-right: 30px; display: none;"></button>
                </div>
                </form>
                <div class="clearfix"></div>
            </div>
    </div>
    <div class="row">
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $data['totalWorkforce'] }}</h3>
                <p>Workforce</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-6 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $data['totalCallTime'] }}</h3>
                <p>Total Call Time</p>
            </div>
            <div class="icon">
                <i class="fa fa-tty"></i>
            </div>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <h5>Call Stats</h5>
    <div class="row dashboard">
        <div class="col-lg-6 col-xs-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalCall'] }} | {{ $data['totalUniqueCall'] }} <small>Unique</small></span>
                            <span class="info-box-text">Total Calls</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalTrainingCall'] }}</span>
                            <span class="info-box-text">Training Calls</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['noAudioCalls'] }}</span>
                            <span class="info-box-text">Audio Missing Calls</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalSaleCall'] }}</span>
                            <span class="info-box-text">Sales Calls</span>
                        </div>
                    </div>
                </div>
            </div>
            <h5>Sales Stats</h5>
            <div class="row ">
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'sale']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalSaleLeads'] }}</span>
                            <span class="info-box-text">Sale</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'hot']) }}">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color:#3c8dbc;color:#fff;"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalHotLeads'] }}</span>
                            <span class="info-box-text">Hot</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'mild']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalMildLeads'] }}</span>
                            <span class="info-box-text">Mild</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                
            </div>
            <div class="row">
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'no_answer']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalNoAnswerLeads'] }}</span>
                            <span class="info-box-text">No Answer</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'busy']) }}">
                    <div class="info-box">
                        <span class="info-box-icon" style="background-color:#3c8dbc;color:#fff;"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalBusyLeads'] }}</span>
                            <span class="info-box-text">Busy</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'not_interested']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalNotInterestedLeads'] }}</span>
                            <span class="info-box-text">Not Interested</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                
            </div>
            <div class="row">
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'cold']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalColdLeads'] }}</span>
                            <span class="info-box-text">Cold</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
                <div class="col-lg-4 col-xs-4">
                    <a href="{{ route('admin.lead.call_history', ['status' => 'dead']) }}">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ $data['totalDeadLeads'] }}</span>
                            <span class="info-box-text">Dead</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    </a>
                </div>
            </div>
        </div>
        <!-- ./col -->

        <div class="col-lg-6 col-xs-6">
            <div class="box">
                <div class="box-body no-padding">
                    <input type="text" id="myInput" onkeyup="sourceSearch()" placeholder="Search for executive.." title="Type in a executive name">
                    <div class="pull-right mt-10 mr-5">
                        <button id="btnExport" class="btn btn-success btn-xs" onclick="fnExcelReport();" > Excel Export</button>
                    </div>
                    <table class="table table-striped" id="executive-report">
                        <tbody><tr>
                            <th>Executive</th>
                            <th>Sale</th>
                            <th>Hot</th>
                            <th>Mild</th>
                            <th>Cold</th>
                            <th>No Answer</th>
                            <th>Busy</th>
                            <th>Not Interested</th>
                            <th>Dead</th>
                        </tr>
                        @foreach($tableData as $key=>$row)
                        <tr>
                            <td><a href="/admin/workforce/executive/{{ $row['id'] }}">{{ $row['name'] }}</a></td>
                            <td>{{ $row['sale'] }}</td>
                            <td>{{ $row['hot'] }}</td>
                            <td>{{ $row['mild'] }}</td>
                            <td>{{ $row['cold'] }}</td>
                            <td>{{ $row['no_answer'] }}</td>
                            <td>{{ $row['busy'] }}</td>
                            <td>{{ $row['not_interested'] }}</td>
                            <td>{{ $row['dead'] }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                 <!-- /.box-body -->
            </div>
        </div>
        <!-- ./col -->
    </div>

    

@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
      function getCalculations()
    {
        $("#overlay").fadeIn(300);　
        $('#calculationButton').click();
    }

    $(function() {
        var startDate = '{{ $startDate }}';
        var endDate = '{{ $endDate }}';

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: '{{ $pickerStartDate }}',
            endDate: '{{ $pickerEndDate }}',
            opens: 'left',
            ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
            $('#startDate').val(startDate);
            $('#endDate').val(endDate);
            // window.location.href = baseURL + '/admin/dashboard?startDate='+startDate+'&endDate='+endDate+'&cal=1';
        });
       
    });
    function sourceSearch() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInput");
          filter = input.value.toUpperCase();
          table = document.getElementById("executive-report");
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

    function fnExcelReport()
    {
        var tab_text="<table><tr bgcolor='#87AFC6'>";
        var textRange; var j=0;
        tab = document.getElementById('executive-report'); // id of table

        for(j = 0 ; j < tab.rows.length ; j++) 
        {     
            tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
            //tab_text=tab_text+"</tr>";
        }

        tab_text=tab_text+"</table>";
        tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
        tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); 

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html","replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus(); 
            sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Classmonitor.xls");
        }  
        else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

        return (sa);
    }

    </script>
    <script>
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
        }
    </script>
@endsection