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