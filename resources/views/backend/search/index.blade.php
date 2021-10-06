@extends('backend.layouts.app')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <style>
    #data-bank-user-table_filter{
        display: none;
    }
    </style>
@endsection

@section('page-header')
    <h1>
        {{ trans('strings.backend.search.title') }}
    </h1>
    <div class="pull-right">
        <form action="{{ route('admin.search.index') }}" method="get">
            <div class="input-group crm-search">
            <input type="text" name="q" id="searchTerm" class="form-control" placeholder="Search..." value="{{ $search_term}}">
            <span class="input-group-btn">
                    <button type="submit" id="search-btn" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
    </div>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Data Bank</h3>
            <div class="box-tools pull-right">
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="data-bank-user-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.data_bank.table.id') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.phone') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.name') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.city') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.messenger') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.learning') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.community') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.data_medium') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div><!-- /.box-body -->
    </div><!--box-->

    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Leads</h3>
            <div class="box-tools pull-right">
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="lead-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>{{ trans('labels.backend.leads.table.id') }}</th>
                            <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                            <th>{{ trans('labels.backend.leads.table.alternate_number') }}</th>
                            <th>{{ trans('labels.backend.leads.table.name') }}</th>
                            <th>{{ trans('labels.backend.leads.table.city') }}</th>
                            <th>{{ trans('labels.backend.leads.table.assigned_to') }}</th>
                            <th>{{ trans('labels.backend.leads.table.last_call') }}</th>
                            <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                            <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                            <th>{{ trans('labels.backend.leads.table.status') }}</th>
                            <th>{{ trans('labels.backend.leads.table.action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                            <input type="text" id="callDate" class="form-control datemask" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
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

    <div class="modal fade" id="assignToModal" style="display: none;">
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
                                <select name="executive" class="form-control executive">
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
                            <input type="text" class="form-control datemask callDate" data-inputmask="'alias': 'dd/mm/yyyy'" data-mask>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="leadId">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary assignToSubmit">Submit</button>
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
    <script  src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key={{ env('GOOGLE_MAP_KEY') }}"></script>
    {{ Html::script("js/backend/plugin/geocomplete/jquery.geocomplete.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.date.extensions.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.extensions.js") }}
    {{ Html::script("js/backend/plugin/datepicker/bootstrap-datepicker.js") }}
    {{ Html::script("js/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.js") }}
    <script>
    $(function() {
        var leadStage = JSON.parse('{!! json_encode($leadStage) !!}');

        $('#data-bank-user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.data.bank.search") }}',
                type: 'post',
                data: function(d) {
                    d.searchTerm = $('#searchTerm').val();
                }
            },
            columns: [
                {data: 'id', name:  'id', searchable: false},
                {data: 'phone', name:  'phone'},
                {data: 'name', name:  'name'},
                {data: 'city', name:  'city'},
                {data: 'messenger', name:  'messenger'},
                {data: 'learning', name:  'learning'},
                {data: 'community', name:  'community'},
                {data: 'data_medium', name:  'data_medium'},
                {data: 'action', name: 'action', searchable: false, sortable: false}
            ],
            order: [[0, "asc"]],
            searchDelay: 500
        });

        var leadTable = $('#lead-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("admin.lead.search") }}',
                type: 'post',
                data: function(d) {
                    d.searchTerm = $('#searchTerm').val();
                }
            },
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.id);
                $(row).attr('data-name', (data.name !='')? data.name : data.phone);
            },
            rowCallback:function(row){
                $(row).click(function(e){
                    if(!$(e.target).hasClass('assignedTo'))
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
                });
            },
            columns: [
                {data: 'id', name:  'id', searchable: false},
                {data: 'phone', name:  'phone'},
                {data: 'alternate_number', name:  'alternate_number'},
                {data: 'name', name:  'name', searchable: false},
                {data: 'city', name:  'city', searchable: false},
                {data: 'assigned_to', name:  'assigned_to',  searchable: false},
                {data: 'last_call', name:  'last_call', searchable: false},
                {data: 'next_follow_up', name:  'next_follow_up', searchable: false},
                {data: 'data_medium', name:  'data_medium'},
                {data: 'lead_status', name:  'lead_status', searchable: false, orderable: false},
                {data: 'action', name: 'action', searchable: false, sortable: false}
            ],
            order: [[0, "desc"]],
            searchDelay: 500
        });

        $('#showLeadModal').on('hidden.bs.modal', function (e) {
            $("#modalBody").html("");
            leadTable.draw();
        });

        $('body').on('click', '.assignLead', function(){
            $('#dataUserId').val($(this).attr('data-val'));
            $('#assignLeadModal').modal('show');
        });

        $('body').on('click', '.assignedTo', function(){
            $('#leadId').val($(this).attr('data-val'));
            $('#assignToModal').modal('show');
        });

        $('#assignLeadModal,#assignToModal').on('show.bs.modal', function (e) {
            $('.datemask').inputmask("dd/mm/yyyy");
        });

        $('body').on('click', '.assignLeadSubmit', function(){
            var dataUserId = $('#dataUserId').val();
            var executive = $('#executive').val();
            var callDate = $('#callDate').val();
            var ids = '';
            var assignToMass ='false';

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
                            table.draw();
                        }
                    }
                });
                
            }
            
        });
        
        $('body').on('click', '.assignToSubmit', function(){
            var leadId = $('#leadId').val();
            var executive = $('#assignToModal').find('.executive').val();
            var callDate = $('#assignToModal').find('.callDate').val();

            var err = false;
            if(leadId == '')
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
                    url : baseURL + '/admin/ajax/assignLeadToAnother',
                    type : 'post',
                    'data' : 'callDate='+callDate+'&executive='+executive+'&leadId='+leadId,
                    success: function(response)
                    {
                        if(response.Status == '200')
                        {
                            $('#assignToModal').modal('hide');
                            leadTable.draw();
                        }
                    }
                });
            }
            
        });
    });
    </script>
    {{ Html::script("js/backend/lead.js?t=".time()) }}
@endsection