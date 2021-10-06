@extends('backend.layouts.app')

@section('after-styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
    <div class="pull-right">
        <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%;margin-top:-25px;">
            <i class="fa fa-calendar"></i>&nbsp;
            <span>
                {{ date('F d, Y', strtotime($startDate)).' - '.date('F d, Y', strtotime($endDate)) }}
            </span> <i class="fa fa-caret-down"></i>
        </div>
    </div>
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Welcome, {{  $logged_in_user->name }}</h4>
        {{-- <p>Tip of the day to get more sales. Pitch on well.</p> --}}
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

        </div>
        <!-- ./col -->

        <div class="col-lg-6 col-xs-6">
            <div class="box">
                <div class="box-body no-padding">
                    <table class="table table-striped">
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

    <h5>Sales Stats</h5>
    <div class="row dashboard">
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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
    </div>
    <div class="row dashboard">
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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
        <div class="col-lg-2 col-xs-2">
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

@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
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
            window.location.href = baseURL + '/admin/dashboard?startDate='+startDate+'&endDate='+endDate;
        });
       
    });
    </script>
@endsection