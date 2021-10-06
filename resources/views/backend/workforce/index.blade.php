@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.workforce.management'))

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>
        {{ trans('labels.backend.workforce.management') }}
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.workforce.management') }}</h3>
            <div class="box-tools pull-right">
                <div class="pull-right mb-10">
                    <select name="executive" id="executive" class="form-control inline" style="position:absolute;right:320px;height:33px;">
                        <option value="">Select Executive</option>
                        @if($executives)
                            @foreach($executives as $key=>$row)
                                <option value="{{ $key }}" {!! ($executiveId == $key)? 'selected="selected"':'' !!}>{{ $row }}</option>
                            @endforeach
                        @endif
                    </select>
                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>
                            {{ date('F d, Y', strtotime($startDate)).' - '.date('F d, Y', strtotime($endDate)) }}
                        </span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-lg-6 col-xs-6">
                    <!-- small box -->
                    <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3>{{ count($executives) }}</h3>
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
                        <h3>{{ $totalCallMinutes }}</h3>
                        <p>Total Call Time</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-tty"></i>
                    </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>

            <div class="table-responsive">
                <table id="workforce-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.workforce.table.executive') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.check_in') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.check_out') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.calls') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.call_time') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.sale') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.hot') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.mild') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.cold') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.no_answer') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.busy') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.not_interested') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.dead') }}</th>
                            <th>{{ trans('labels.backend.workforce.table.note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tableData as $row)
                        <tr>
                            <td>
                                <a href="{{ route('admin.workforce.executive', ['user' => $row['id']]) }}">
                                    {{ $row['name'] }}
                                </a>
                            </td>
                            <td>{{ ($row['check_in'] != 'NA')? date(config('access.date_time_format'), strtotime($row['check_in'])) : 'NA' }}</td>
                            <td>{{ ($row['check_out'] != 'NA')? date(config('access.date_time_format'), strtotime($row['check_out'])) : 'NA' }}</td>
                            <td>{{ $row['calls'] }}</td>
                            <td>{{ $row['call_time'] }}</td>
                            <td>{{ $row['sale'] }}</td>
                            <td>{{ $row['hot'] }}</td>
                            <td>{{ $row['mild'] }}</td>
                            <td>{{ $row['cold'] }}</td>
                            <td>{{ $row['no_answer'] }}</td>
                            <td>{{ $row['busy'] }}</td>
                            <td>{{ $row['not_interested'] }}</td>
                            <td>{{ $row['dead'] }}</td>
                            <td>
                                <button class="btn btn-xs btn-info setExecutiveNote" data-note="{{ $row['note'] }}" data-val="{{ $row['id'] }}" data-name="{{ $row['name'] }}" id="note-{{ $row['id'] }}">
                                    Set Note
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->

     <div class="modal fade" id="setExecutiveNoteModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close text-red" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle"></i>
                    </button>
                    <h4 class="modal-title" id="modalTitle"></h4>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-sm-12">
                            <textarea id="executiveNote" rows="2" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="dataUserId">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary setNoteSubmit">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
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
            var executive = $('#executive').val();
            window.location.href = baseURL + '/admin/workforce?executive='+executive+'&startDate='+startDate+'&endDate='+endDate;
        })
       
        $('#executive').change(function(){
            var executive = $('#executive').val();
            window.location.href = baseURL + '/admin/workforce?executive='+executive+'&startDate='+startDate+'&endDate='+endDate;
        });

        $('body').on('click', '.setExecutiveNote', function(){
            $('#dataUserId').val($(this).attr('data-val'));
            $('#executiveNote').val($(this).attr('data-note'));
            $('#modalTitle').html('Note for '+$(this).attr('data-name'));
            $('#setExecutiveNoteModal').modal('show');
        });

        $('body').on('click', '.setNoteSubmit', function(){
            var dataUserId = $('#dataUserId').val();
            var executiveNote = $('#executiveNote').val();
            
            $.ajax({
                url : baseURL + '/admin/ajax/setExecutiveNote',
                type: 'POST',
                data: 'dataUserId='+dataUserId+'&executiveNote='+executiveNote,
                success: function(response)
                {
                    if(response.Status == '200')
                    {
                        $('#note-'+dataUserId).attr('data-note', executiveNote);
                        $('#dataUserId').val('');
                        $('#executiveNote').val('');
                        $('#modalTitle').html('');
                        $('#setExecutiveNoteModal').modal('hide');
                    }
                }
            })
        });
    });
    </script>
@endsection
