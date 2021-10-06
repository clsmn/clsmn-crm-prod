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
        <h4>Welcome, {{ $logged_in_user->name }}</h4>
        @if($logged_in_user->note != null && $logged_in_user->note != '')
            <p>{{ $logged_in_user->note }}</p>
        @endif
    </div>

    <h5>Call Stats</h5>
    <div class="row dashboard">
        <div class="col-lg-6 col-xs-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ isset($data['totalCall'])? $data['totalCall'] : 0 }} | {{ $data['totalUniqueCall'] }} <small>Unique</small></span>
                            <span class="info-box-text">Total Calls</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ isset($data['totalTrainingCall'])? $data['totalTrainingCall']:0 }}</span>
                            <span class="info-box-text">Training Calls</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ isset($data['noAudioCalls'])? $data['noAudioCalls'] : 0 }}</span>
                            <span class="info-box-text">Audio Messing Calls</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-phone"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{ isset($data['totalSaleCall'])? $data['totalSaleCall']:0 }}</span>
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
                            <th style="width: 10px">#</th>
                            <th>Phone</th>
                            <th style="width: 100px">Lead Status</th>
                            <th style="width: 250px">Called At</th>
                        </tr>
                        @if(isset($data['recentCalls']))
                            @foreach($data['recentCalls'] as $key=>$row)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $row->phone }}</td>
                                    <td>{!! '<button class="btn btn-xs '.$row->lead->status_class.'">'.leadStatus($row->lead->lead_status).'</button>' !!}</td>
                                    <td>{{ $row->created_at->format(config('access.date_time_format')) }}</td>
                                </tr>
                            @endforeach
                        @endif
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
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalSaleLeads'] }}</span>
                    <span class="info-box-text">Sale</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon" style="background-color:#3c8dbc;color:#fff;"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalHotLeads'] }}</span>
                    <span class="info-box-text">Hot</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalMildLeads'] }}</span>
                    <span class="info-box-text">Mild</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalColdLeads'] }}</span>
                    <span class="info-box-text">Cold</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
    </div>
    <div class="row dashboard">
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalNoAnswerLeads'] }}</span>
                    <span class="info-box-text">No Answer</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon" style="background-color:#3c8dbc;color:#fff;"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalBusyLeads'] }}</span>
                    <span class="info-box-text">Busy</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon bg-blue"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalNotInterestedLeads'] }}</span>
                    <span class="info-box-text">Not Interested</span>
                </div>
                <!-- /.info-box-content -->
            </div>
        </div>
        <div class="col-lg-2 col-xs-2">
            <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">{{ $data['totalDeadLeads'] }}</span>
                    <span class="info-box-text">Dead</span>
                </div>
                <!-- /.info-box-content -->
            </div>
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