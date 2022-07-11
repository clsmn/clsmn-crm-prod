@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.leads.call_history'))

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>
        {{ trans('labels.backend.leads.call_history') }}
    </h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.leads.call_history') }}</h3>
            <div class="box-tools pull-right">
                <div class="pull-right mb-10">
                    <button class="btn btn-success btn-xs disabled" disabled id="massAssignment">Mass Assigned To</button>
                </div>
                <div class="clearfix"></div>
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <div class="table-responsive">
                    <select id="frequencyFilter" class="form-control" style="right:1160px;">
                        <option value="">Select Frequency</option>
                        @for($i=1; $i<=20; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                    <select id="sourceFilter" class="form-control" style="right:950px;">
                        <option value="">Select Source</option>
                        @foreach($sources as $source)
                        <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                    <select id="executiveFilter" class="form-control" style="right:740px;">
                        <option value="">Select Executive</option>
                        @foreach($executives as $key => $executive)
                        <option value="{{ $key }}">{{ $executive }}</option>
                        @endforeach
                    </select>
                    <select id="leadStatusFilter" class="form-control" style="right:590px;">
                        <option value="">All</option>
                        <option value="open" {!! ($leadStatus == 'open')? 'selected="selected"' : '' !!}>Open</option>
                        <option value="sale" {!! ($leadStatus == 'sale')? 'selected="selected"' : '' !!}>Sale</option>
                        <option value="hot" {!! ($leadStatus == 'hot')? 'selected="selected"' : '' !!}>Hot</option>
                        <option value="mild" {!! ($leadStatus == 'mild')? 'selected="selected"' : '' !!}>Mild</option>
                        <option value="cold" {!! ($leadStatus == 'cold')? 'selected="selected"' : '' !!}>Cold</option>
                        <option value="no_answer" {!! ($leadStatus == 'no_answer')? 'selected="selected"' : '' !!}>No Answer</option>
                        <option value="busy" {!! ($leadStatus == 'busy')? 'selected="selected"' : '' !!}>Busy</option>
                        <option value="not_interested" {!! ($leadStatus == 'not_interested')? 'selected="selected"' : '' !!}>Not Interested</option>
                        <option value="dead" {!! ($leadStatus == 'dead')? 'selected="selected"' : '' !!}>Dead</option>
                    </select>
                    <div id="datePicker1" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 310px;">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span>@php echo date('F d, Y', strtotime('-30 days')).' - '.date('F d, Y') @endphp</span> <i class="fa fa-caret-down"></i>
                    </div>
                    <table id="called-list-table" class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="select_all" value="1" id="selectAllUser"></th>
                                <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                <th>Frequency</th>
                                <th>{{ trans('labels.backend.leads.table.call_duration') }}</th>
                                <th>{{ trans('labels.backend.leads.table.called_by') }}</th>
                                <th>{{ trans('labels.backend.leads.table.called_at') }}</th>
                                <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                                <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                                <th>{{ trans('labels.backend.leads.table.assigned_to') }}</th>
                                <th>{{ trans('labels.backend.leads.table.status') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div><!--table-responsive-->
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->
    
    <div class="modal fade" id="showLeadModal" style="display: none;">
        <div class="modal-dialog" style="width:90%">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close text-red" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle"></i>
                    </button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body" style="background-color:#ecf0f5;" id="modalBody">

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="assignLeadModal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close text-red" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times-circle"></i>
                    </button>
                    <h4 class="modal-title">Assigned Lead To</h4>
                </div>
                <div class="modal-body" id="modalBody">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="executive" class="col-sm-2 control-label">Executive</label>
                            <div class="col-sm-10">
                                <select name="executive" id="executive" class="form-control">
                                    <option value="">Select Executive</option>
                                    @foreach($executives as $key => $executive)
                                        <option value="{{ $key }}">{{ $executive }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="callDate" class="col-sm-2 control-label">Call Date</label>
                            <div class="col-sm-10">
                            <input type="text" id="callDate" class="form-control datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask value="{{ date('d/m/Y') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="assignToMass">
                    <input type="hidden" id="dataUserId">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary assignLeadSubmit">Submit</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

@endsection

@section('after-scripts')
    {{ Html::script("js/backend/plugin/Bootstrap-Confirmation-2/bootstrap-confirmation.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script  src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key={{ env('GOOGLE_MAP_KEY') }}"></script>
    {{ Html::script("js/backend/plugin/geocomplete/jquery.geocomplete.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.date.extensions.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.extensions.js") }}
    {{ Html::script("js/backend/plugin/datepicker/bootstrap-datepicker.js") }}
    {{ Html::script("js/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.js") }}
    <script>
    $(function() {
        var startDate = '';
        var endDate = '';

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#datePicker1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        var leadStage = JSON.parse('{!! json_encode($leadStage) !!}');
        
        var calledTable = $('#called-list-table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth : false,
            "lengthMenu": [[5, 10, 20, 25, 30, 40, 50, 100], [5, 10, 20, 25, 30, 40, 50, 100]],
            "pageLength": 50,
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.lead_id);
                $(row).attr('data-du', data.data_user_id);
                $(row).attr('data-name', (data.name !='')? data.name : data.phone);
            },
            rowCallback:function(row){
                $(row).click(function(e){
                    if(!$(e.target).hasClass('recordPlay') && !$(e.target).hasClass('leadCheck') && !$(e.target).hasClass('dt-body-center'))
                    {
                        $("#showLeadModal").attr('data-val', $(this).attr('data-val'));
                        var html = $(this).attr('data-name') + ' <span></span>';
                        $("#showLeadModal").find('.modal-title').html(html);
                        $("#showLeadModal").modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });
                    }
                    if($(e.target).hasClass('dt-body-center'))
                    {
                        var du = $(this).attr('data-du')
                        if($('#du-'+du).prop('checked'))
                        {
                            $('#du-'+du).prop('checked', false);
                        }else{
                            $('#du-'+du).prop('checked', true);
                        }
                    }
                });
            },
            ajax: {
                url: '{{ route("admin.lead.called.manager.get") }}',
                type: 'post',
                data: function(d) {
                    d.start_date = startDate;
                    d.end_date = endDate;
                    d.lead_status = $('#leadStatusFilter').val();
                    d.executive = $('#executiveFilter').val();
                    d.medium = $('#sourceFilter').val();
                    d.frequency = $('#frequencyFilter').val();
                    d.city = $('#cityFilter').val();
                }
            },
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function (data, type, full, meta){
                    return '<input type="checkbox" class="leadCheck" id="du-'+$('<div/>').text(data).html()+'" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                }
            }],
            columns: [
                {data: 'data_user_id'},
                {data: 'lead_id', name:  'lead_id', searchable: false, 'className': 'dt-body-center'},
                {data: 'phone', name:  'phone'},
                {data: 'name', name:  'name', searchable: false},
                {data: 'frequency', name:  'frequency',  searchable: false},
                {data: 'call_duration', name:  'call_duration',  searchable: false},
                {data: 'called_by', name:  'called_by',  searchable: false},
                {data: 'created_at', name:  'created_at', searchable: false},
                {data: 'next_follow_up', name:  'next_follow_up', searchable: false},
                {data: 'data_medium', name:  'data_medium', searchable: false},
                {data: 'assigned_to', name:  'assigned_to',  searchable: false},
                {data: 'lead_status', name:  'lead_status', searchable: false, orderable: false},
            ],
            order: [[5, "desc"]],
            searchDelay: 500
        });

        // Handle click on "Select all" control
        $('#selectAllUser').on('click', function(){
            // Get all rows with search applied
            var rows = calledTable.rows({ 'search': 'applied' }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"]', rows).prop('checked', this.checked);

            enableDisableMassAssignment();
        });

        // Handle click on checkbox to set state of "Select all" control
        $('#called-list-table tbody').on('change', 'input[type="checkbox"]', function(){
            // If checkbox is not checked
            if(!this.checked){
                var el = $('#selectAllUser').get(0);
                // If "Select all" control is checked and has 'indeterminate' property
                if(el && el.checked && ('indeterminate' in el)){
                    // Set visual state of "Select all" control
                    // as 'indeterminate'
                    el.indeterminate = true;
                }
            }

            enableDisableMassAssignment();
        });

        function enableDisableMassAssignment()
        {
            var checkedUsers = $('.leadCheck:checked').length;
            if(checkedUsers > 0)
            {
                $('#massAssignment').removeAttr('disabled');
                $('#massAssignment').removeClass('disabled');
            }else{
                $('#massAssignment').addClass('disabled');
                $('#massAssignment').attr('disabled', 'disabled');
            }
        }

        $('#massAssignment').click(function(){
            $('#assignLeadModal').modal('show');
            $('#assignToMass').val('true');
        });

        $('body').on('click', '.assignLeadSubmit', function(){
            var dataUserId = $('#dataUserId').val();
            var executive = $('#executive').val();
            var callDate = $('#callDate').val();
            var ids = $('input[name="id[]"]').serialize();
            var assignToMass = $('#assignToMass').val();

            var err = false;
            if(dataUserId == '' && assignToMass == 'false')
            {
                err = true;
            }
            if(ids == '' && assignToMass == 'true')
            {
                err = true;
            }
            if(executive == '')
            {
                err = true;
            }
            if(callDate == '')
            {
                err = true;
            }

            if(!err)
            {
                $.ajax({
                    url : baseURL + '/admin/ajax/moveToLead',
                    type : 'post',
                    'data' : 'callDate='+callDate+'&executive='+executive+'&dataUserId='+dataUserId+'&assignToMass='+assignToMass+'&'+ids,
                    success: function(response)
                    {
                        if(response.Status == '200')
                        {
                            $('#assignLeadModal').modal('hide');
                            calledTable.draw(false);
                        }
                    }
                });
                
            }
            
        });

        $('body').on('change', '#leadStatusFilter, #executiveFilter, #sourceFilter, #cityFilter, #frequencyFilter', function(){
            calledTable.draw();
        });

        $('#showLeadModal').on('hidden.bs.modal', function (e) {
            $("#modalBody").html("");
            calledTable.draw();
        });

        $('#datePicker1').daterangepicker({
            autoUpdateInput: false,
            locale: {
                cancelLabel: 'Clear'
            },
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
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

        $('#datePicker1').on('apply.daterangepicker', function(ev, picker) {
            cb(picker.startDate, picker.endDate);
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
            calledTable.draw();
        });

        $('#datePicker1').on('cancel.daterangepicker', function(ev, picker) {
            $('#datePicker1 span').html('Select Date Range');
            startDate = '';
            endDate = '';
        });
        
    });
    </script>
    {{ Html::script("js/backend/lead.js?t=".time()) }}
@endsection
