@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.leads.management'))

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
@php
$userLogin = Auth::user();
@endphp
    <h1>
        {{ trans('labels.backend.leads.management') }}
        
        <div class="pull-right">
            @if($checkOut)
                <a href="{{ route('admin.check_out') }}" class="btn btn-danger btn-xs">Check Out</a>
                @if($userLogin->office24by_username != '' || $userLogin->office24by_username != null)
                    <a onclick="fetchIncoming();" class="btn btn-success btn-xs">Fetch Incoming Details</a>
                @endif
            @endif
            {{-- <a href="{{ route('admin.fetch.leads') }}" class="btn btn-warning btn-xs hide" id="fetchLeads">Fetch Leads</a> --}}
        </div>
    </h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="nav-tabs-custom {{ ($checkIn)? 'overlay-wrapper':''}}">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true">Call List</a></li>
                    @if(Auth::id() != 140)
                    <li class=""><a href="#tab_7" data-toggle="tab" aria-expanded="false">Follow Ups</a></li>
                    @endif
                    <li class=""><a href="#tab_5" data-toggle="tab" aria-expanded="false">Call History</a></li>
                    <!-- <li class=""><a href="#tab_8" data-toggle="tab" aria-expanded="false">Incoming History</a></li> -->
                    <!-- <li class=""><a href="#tab_6" data-toggle="tab" aria-expanded="false">Transferred</a></li> -->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="table-responsive lead-call-list">
                            <select id="leadPhaseFilter" class="form-control">
                                <option value="">Select Phase</option>
                                <option value="buy_attempt">Buy Attempt</option>
                                <option value="cart">Cart Abandon</option>
                                <option value="trial">Trial Started</option>
                                <option value="kit_purchased">Kit Purchased</option>
                            </select>
                            <select id="sourceFilter" class="form-control">
                                <option value="">Select Source</option>
                                @foreach($sources as $source)
                                <option value="{{ $source }}">{{ $source }}</option>
                                @endforeach
                            </select>
                            <select id="typeFilter" class="form-control">
                                <option value="">Show Stage</option>
                                <option value="new">New Leads</option>
                                <option value="followUp">Follow Up Only</option>
                                <option value="open">Open</option>
                                <option value="sale">Sale</option>
                                <option value="hot">Hot</option>
                                <option value="mild">Mild</option>
                                <option value="cold">Cold</option>
                                <option value="no_answer">No Answer</option>
                                <option value="busy">Busy</option>
                                <option value="not_interested">Not Interested</option>
                                <option value="dead">Dead</option>
                            </select>
                            <table id="call-list-table" class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                        <th>CC</th>
                                        <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.city') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.last_call') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.subscription_type') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.phase') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.status') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!--table-responsive-->
                    </div>

                    <div class="tab-pane" id="tab_7">
                        <div class="table-responsive lead-call-list">
                            <select id="leadPhaseFilterFollowUp" class="form-control leadPhaseFilter">
                                <option value="">Select Phase</option>
                                <option value="buy_attempt">Buy Attempt</option>
                                <option value="cart">Cart Abandon</option>
                                <option value="trial">Trial Started</option>
                                <option value="kit_purchased">Kit Purchased</option>
                            </select>
                            <select id="sourceFilterFollowUp" class="form-control sourceFilter">
                                <option value="">Select Source</option>
                                @foreach($sources as $source)
                                <option value="{{ $source }}">{{ $source }}</option>
                                @endforeach
                            </select>
                            <select id="typeFilterFollowUp" class="form-control typeFilter">
                                <option value="">Show Stage</option>
                                <option value="new">New Leads</option>
                                <option value="followUp">Follow Up Only</option>
                                <option value="open">Open</option>
                                <option value="sale">Sale</option>
                                <option value="hot">Hot</option>
                                <option value="mild">Mild</option>
                                <option value="cold">Cold</option>
                                <option value="no_answer">No Answer</option>
                                <option value="busy">Busy</option>
                                <option value="not_interested">Not Interested</option>
                                <option value="dead">Dead</option>
                            </select>
                            <table id="follow-up-list-table" class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                        <th>CC</th>
                                        <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.city') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.last_call') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.subscription_type') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.phase') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.status') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!--table-responsive-->
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_5">
                        <div class="table-responsive">
                            <select id="sourceFilter2" class="form-control" style="height: 30px;width: 200px;position: absolute;right: 900px;top: 54px;z-index:9;">
                                <option value="">Select Source</option>
                                @foreach($sources as $source)
                                <option value="{{ $source }}">{{ $source }}</option>
                                @endforeach
                            </select>
                            <select id="leadStatusFilter" class="form-control" style="right:590px;">
                                <option value="">All</option>
                                <option value="open">Open</option>
                                <option value="sale">Sale</option>
                                <option value="hot">Hot</option>
                                <option value="mild">Mild</option>
                                <option value="cold">Cold</option>
                                <option value="no_answer">No Answer</option>
                                <option value="busy">Busy</option>
                                <option value="not_interested">Not Interested</option>
                                <option value="dead">Dead</option>
                            </select>
                            <div id="datePicker1" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 310px;">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                            </div>
                            <table id="called-list-table" class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                        <th>CC</th>
                                        <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.call_duration') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.called_at') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.status') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!--table-responsive-->
                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_6">
                        <div class="table-responsive">
                            <table id="transferred-list-table" class="table table-condensed table-hover" width="100%">
                                <thead>
                                    <tr>
                                        <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                        <th>CC</th>
                                        <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.city') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.last_call') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.follow_up') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.subscription_type') }}</th>
                                        <th>{{ trans('labels.backend.leads.table.status') }}</th>
                                    </tr>
                                </thead>
                            </table>
                        </div><!--table-responsive-->
                    </div>

                    <!-- /.tab-pane -->
             
                </div>
                <!-- /.tab-content -->
                @if($checkIn)
                <div class="overlay">
                    <a href="{{ route('admin.check_in') }}" class="btn btn-success btn-lg check-in">Check In</a>
                </div>
                @endif
            </div>
            <!-- nav-tabs-custom -->
        </div>
    </div>

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
<!-- Modal -->
<div id="incomingCallDetails" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Incoming Call Details</h4>
      </div>
      <div class="modal-body">
        <table class="table table-condensed table-hover dataTable no-footer">
            <thead>
                <tr>
                    <th>Lead ID</th>
                    <th>Caller Name</th>
                    <th>Caller Number</th>
                    <th>Call Time</th>
                    <th>Lead Assigned</th>
                </tr>
            </thead>
            <tbody id="incomingResponse">
                <tr></tr>
                <tr></tr>
                <tr></tr>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
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

    <script type="text/javascript">
    function fetchIncoming()
    {
        $.ajax({
                url : baseURL + '/admin/getIncomingDetail',
                type : 'get',
                success: function(response)
                {
                    console.log(response);
                    $('#incomingResponse').html(response);
                    $('#incomingCallDetails').modal('show');
                }
            });
        
    }
</script>
    <script>
        function incoming()
        {
            $("#showLeadModal").attr('data-val', $('#lead-dataval').attr('data-val'));
            var html = $('#lead-dataval').attr('data-name') + ' <span></span>';
            $("#showLeadModal").find('.modal-title').html(html);
            $('#incomingCallDetails').modal('hide');
            $("#showLeadModal").modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });
            document.getElementById("showLeadModal").style.overflow = "scroll";
        }

    $(function() {
        var startDate = '';
        var endDate = '';

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#datePicker1 span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        var leadStage = JSON.parse('{!! json_encode($leadStage) !!}');

        $.fn.dataTable.ext.type.order['date-pre'] = function ( d ) {
            return Date.parse( d ) || Infinity;
        }
        var callTable = $('#call-list-table').DataTable({
            processing: true,
            serverSide: true,
            "lengthMenu": [[5, 10, 20, 25, 30, 40, 50, 100], [5, 10, 20, 25, 30, 40, 50, 100]],
            "pageLength": 10,
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.id);
                $(row).attr('data-name', (data.name !='')? data.name : data.phone);
            },
            rowCallback:function(row){
                $(row).click(function(){
                    $("#showLeadModal").attr('data-val', $(this).attr('data-val'));
                    var html = $(this).attr('data-name') + ' <span></span>';
                    console.log(html);debugger;
                    $("#showLeadModal").find('.modal-title').html(html);
                    $("#showLeadModal").modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                });
            },
            ajax: {
                url: '{{ route("admin.lead.call.get") }}',
                type: 'post',
                data: function(d) {
                    d.city = '';
                    d.type = $('#typeFilter').val();
                    d.medium = $('#sourceFilter').val();
                    d.phase = $('#leadPhaseFilter').val();
                }
            },
            columns: [
                {data: 'id', name:  '{{config('access.leads_table')}}.id', searchable: false},
                {data: 'country_code', name:  '{{config('access.leads_table')}}.country_code'},
                {data: 'phone', name:  '{{config('access.leads_table')}}.phone'},
                {data: 'name', name:  '{{config('access.leads_table')}}.name',searchable: false},
                {data: 'city', name:  '{{config('access.leads_table')}}.city',searchable: false},
                {data: 'last_call', name:  '{{config('access.leads_table')}}.last_call', searchable: false},
                {data: 'next_follow_up', name:  '{{config('access.leads_table')}}.next_follow_up', searchable: false},
                {data: 'data_medium', name:  '{{config('access.leads_table')}}.data_medium', searchable: false},
                {data: 'subscription_type', name:  '{{config('access.leads_table')}}.subscription_type', searchable: false},
                {data: 'phase', name:  '{{config('access.leads_table')}}.phase', searchable: false},
                {data: 'lead_status', name:  '{{config('access.leads_table')}}.lead_status', searchable: false, orderable:false},
            ],
            initComplete : function(settings, json){
                if(json.recordsTotal < 10)
                {
                    $('#fetchLeads').removeClass('hide');
                }else{
                    $('#fetchLeads').addClass('hide');
                }
            },
            order: [],
            searchDelay: 500
        });

        var followUpTable = $('#follow-up-list-table').DataTable({
            processing: true,
            serverSide: true,
            autoWidth : false,
            "lengthMenu": [[5, 10, 20, 25, 30, 40, 50, 100], [5, 10, 20, 25, 30, 40, 50, 100]],
            "pageLength": 10,
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.id);
                $(row).attr('data-name', (data.name !='')? data.name : data.phone);
            },
            rowCallback:function(row){
                $(row).click(function(){
                    $("#showLeadModal").attr('data-val', $(this).attr('data-val'));
                    var html = $(this).attr('data-name') + ' <span></span>';
                    $("#showLeadModal").find('.modal-title').html(html);
                    $("#showLeadModal").modal({
                        backdrop: 'static',
                        keyboard: false,
                        show: true
                    });
                });
            },
            ajax: {
                url: '{{ route("admin.lead.call.follow_up.get") }}',
                type: 'post',
                data: function(d) {
                    d.city = '';
                    d.type = $('#typeFilterFollowUp').val();
                    d.medium = $('#sourceFilterFollowUp').val();
                    d.phase = $('#leadPhaseFilterFollowUp').val();
                }
            },
            columns: [
                {data: 'id', name:  '{{config('access.leads_table')}}.id', searchable: false},
                {data: 'country_code', name:  '{{config('access.leads_table')}}.country_code'},
                {data: 'phone', name:  '{{config('access.leads_table')}}.phone'},
                {data: 'name', name:  '{{config('access.leads_table')}}.name',searchable: false},
                {data: 'city', name:  '{{config('access.leads_table')}}.city',searchable: false},
                {data: 'last_call', name:  '{{config('access.leads_table')}}.last_call', searchable: false},
                {data: 'next_follow_up', name:  '{{config('access.leads_table')}}.next_follow_up', searchable: false},
                {data: 'data_medium', name:  '{{config('access.leads_table')}}.data_medium', searchable: false},
                {data: 'subscription_type', name:  '{{config('access.leads_table')}}.subscription_type', searchable: false},
                {data: 'phase', name:  '{{config('access.leads_table')}}.phase', searchable: false},
                {data: 'lead_status', name:  '{{config('access.leads_table')}}.lead_status', searchable: false, orderable:false},
            ],
            initComplete : function(settings, json){
                if(json.recordsTotal < 10)
                {
                    $('#fetchLeads').removeClass('hide');
                }else{
                    $('#fetchLeads').addClass('hide');
                }
            },
            order: [],
            searchDelay: 500
        });

        // var transferredTable = $('#transferred-list-table').DataTable({
        //     processing: true,
        //     serverSide: true,
        //     createdRow: function( row, data, dataIndex ) {
        //         $(row).attr('data-val', data.id);
        //         $(row).attr('data-name', (data.name !='')? data.name : data.phone);
        //     },
        //     rowCallback:function(row){
        //         $(row).click(function(){
        //             $("#showLeadModal").attr('data-val', $(this).attr('data-val'));
        //             var html = $(this).attr('data-name') + ' <span></span>';
        //             $("#showLeadModal").find('.modal-title').html(html);
        //             $("#showLeadModal").modal({
        //                 backdrop: 'static',
        //                 keyboard: false,
        //                 show: true
        //             });
        //         });
        //     },
        //     ajax: {
        //         url: '{{ route("admin.lead.call.transferred") }}',
        //         type: 'post',
        //         data: { }
        //     },
        //     columns: [
        //         {data: 'id', name:  '{{config('access.leads_table')}}.id', searchable: false},
        //         {data: 'phone', name:  '{{config('access.leads_table')}}.phone'},
        //         {data: 'name', name:  '{{config('access.leads_table')}}.name',searchable: false},
        //         {data: 'city', name:  '{{config('access.leads_table')}}.city',searchable: false},
        //         {data: 'last_call', name:  '{{config('access.leads_table')}}.last_call', searchable: false},
        //         {data: 'next_follow_up', name:  '{{config('access.leads_table')}}.next_follow_up', searchable: false},
        //         {data: 'data_medium', name:  '{{config('access.leads_table')}}.data_medium', searchable: false},
        //         {data: 'subscription_type', name:  '{{config('access.leads_table')}}.subscription_type', searchable: false},
        //         {data: 'lead_status', name:  '{{config('access.leads_table')}}.lead_status', searchable: false, orderable:false},
        //     ],
        //     initComplete : function(settings, json){
        //         if(json.recordsTotal < 10)
        //         {
        //             $('#fetchLeads').removeClass('hide');
        //         }else{
        //             $('#fetchLeads').addClass('hide');
        //         }
        //     },
        //     order: [[4, "asc"]],
        //     searchDelay: 500
        // });


        // var calledTable = $('#called-list-incoming-table').DataTable({
        //     bStateSave: true,
        //     processing: true,
        //     serverSide: true,
        //     autoWidth : false,
        //     createdRow: function( row, data, dataIndex ) {
        //         $(row).attr('data-val', data.lead_id);
        //         $(row).attr('data-name', (data.name !='')? data.name : data.phone);
        //     },
        //     rowCallback:function(row){
        //         $(row).click(function(e){
        //             if(!$(e.target).hasClass('recordPlay'))
        //             {
        //                 $("#showLeadModal").attr('data-val', $(this).attr('data-val'));
        //                 var html = $(this).attr('data-name') + ' <span></span>';
        //                 $("#showLeadModal").find('.modal-title').html(html);
        //                 $("#showLeadModal").modal({
        //                     backdrop: 'static',
        //                     keyboard: false,
        //                     show: true
        //                 });
        //             }
        //         });
        //     },
        //     ajax: {
        //         url: '{{ route("admin.lead.called.get.incoming") }}',
        //         type: 'post',
        //         data: function(d) {
        //             d.start_date = startDate;
        //             d.end_date = endDate;
        //             d.city = '';
        //             d.medium = $('#sourceFilter2').val();
        //             d.lead_status = $('#leadStatusFilter').val();
        //         }
        //     },
        //     columns: [
        //         {data: 'lead_id', name:  'lead_id', searchable: false},
        //         {data: 'country_code', name:  'country_code',  searchable: false},
        //         {data: 'phone', name:  'phone'},
        //         {data: 'name', name:  'name', searchable: false},
        //         {data: 'call_duration', name:  'call_duration', searchable: false},
        //         {data: 'called_at', name:  'called_at', searchable: false},
        //         {data: 'next_follow_up', name:  'next_follow_up', searchable: false},
        //         {data: 'lead_status', name:  'lead_status', searchable: false, orderable:false},
        //     ],
        //     order: [[0, "asc"]],
        //     searchDelay: 500
        // });


        var calledTable = $('#called-list-table').DataTable({
            bStateSave: true,
            processing: true,
            serverSide: true,
            autoWidth : false,
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.lead_id);
                $(row).attr('data-name', (data.name !='')? data.name : data.phone);
            },
            rowCallback:function(row){
                $(row).click(function(e){
                    if(!$(e.target).hasClass('recordPlay'))
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
            ajax: {
                url: '{{ route("admin.lead.called.get") }}',
                type: 'post',
                data: function(d) {
                    d.start_date = startDate;
                    d.end_date = endDate;
                    d.city = '';
                    d.medium = $('#sourceFilter2').val();
                    d.lead_status = $('#leadStatusFilter').val();
                    d.type = 'called';
                }
            },
            columns: [
                {data: 'lead_id', name:  'lead_id', searchable: false},
                {data: 'country_code', name:  'country_code',  searchable: false},
                {data: 'phone', name:  'phone'},
                {data: 'name', name:  'name', searchable: false},
                {data: 'call_duration', name:  'call_duration', searchable: false},
                {data: 'called_at', name:  'called_at', searchable: false},
                {data: 'next_follow_up', name:  'next_follow_up', searchable: false},
                {data: 'lead_status', name:  'lead_status', searchable: false, orderable:false},
            ],
            order: [[0, "asc"]],
            searchDelay: 500
        });
        
        $('body').on('change', '#leadStatusFilter, #sourceFilter2', function(){
            calledTable.draw(false);
        });

        $('#showLeadModal').on('hidden.bs.modal', function (e) {
            $("#modalBody").html("");
            callTable.draw(false);
            calledTable.draw(false);
            followUpTable.draw(false);
            // transferredTable.draw(false);
        });

        $('#typeFilter, #sourceFilter, #leadPhaseFilter').change(function(){
            callTable.draw();
        });
        $('#typeFilterFollowUp, #sourceFilterFollowUp, #leadPhaseFilterFollowUp').change(function(){
            followUpTable.draw();
        });

        $('#datePicker1').daterangepicker({
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

        $('#datePicker1').on('apply.daterangepicker', function(ev, picker) {
            cb(picker.startDate, picker.endDate);
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
            calledTable.draw(false);
        });

        $('#datePicker1').on('cancel.daterangepicker', function(ev, picker) {
            $('#datePicker1 span').html('Select Date Range');
            startDate = '';
            endDate = '';
        });

    });
    </script>
    {{ Html::script("js/backend/lead.js?time=".time()) }}
@endsection
