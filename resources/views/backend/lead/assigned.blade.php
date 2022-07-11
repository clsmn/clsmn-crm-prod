@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.leads.management'))

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <style type="text/css">
         #stageFilter{
            position: absolute;
            width: 200px;
            right: 1140px;
            height: 30px;
            z-index: 9;
        }
        .select2
        {
            right: 0px !important;
            width: 100% !important;
            position: unset !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow
        {
            right: 20px !important;
        }
    </style>
@endsection

@section('page-header')
    <h1>
        {{ trans('labels.backend.leads.management') }}
        <div class="pull-right"></div>
    </h1>
    
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.leads.assigned') }}</h3>
            <button id="btnExport">Export</button>
            <div class="box-tools pull-right">
               <!--  <div id="datePickerLead" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 310px;position: absolute;right: 141px;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                </div> -->
                <div class="pull-right mb-10">
                    <button class="btn btn-success btn-xs disabled" disabled id="massAssignment">Mass Assigned To</button>
                </div>              
                <div class="clearfix"></div>
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
             <div class="row mx-auto">
                <h3 class="text-center"><b>Filters</b></h3>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                         <select id="assignedTo" class="form-control">
                        <option value="">Select Executive</option>
                        @foreach($executives as $key => $name)
                        <option value="{{ $key }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="leadPhaseFilter" class="form-control">
                        <option value="">Select Phase</option>
                        <option value="buy_attempt">Buy Attempt</option>
                        <option value="cart">Cart Abandon</option>
                        <option value="trial">Trial Started</option>
                        <option value="kit_purchased">Kit Purchased</option>
                    </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                       <select id="sourceFilter" class="form-control">
                        <option value="">Select Source</option>
                        @foreach($sources as $source)
                        <option value="{{ $source }}">{{ $source }}</option>
                        @endforeach
                    </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="cityFilter" class="form-control">
                            <option value="">Select City</option>
                            @foreach($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="typeFilter" class="form-control">
                        <option value="">Show Stage</option>
                        <option value="new">New Leads</option>
                        <option value="followUp">Pending Follow Up</option>
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
                    </div>
                     <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <div id="datePickerLead" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; width: 100%;position: unset;right: 0px; border-radius: 5px;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </div>
                <hr>
            <div class="table-responsive">
                
                <div class="table-responsive lead-call-list">
                 
                    <table id="lead-list-table" class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" name="select_all" value="1" id="selectAllUser"></th>
                                <th>{{ trans('labels.backend.leads.table.id') }}</th>
                                <th>CC</th>
                                <th>{{ trans('labels.backend.leads.table.phone') }}</th>
                                <th>{{ trans('labels.backend.leads.table.name') }}</th>
                                <th>{{ trans('labels.backend.leads.table.assigned_to') }}</th>
                                <th>{{ trans('labels.backend.leads.table.city') }}</th>
                                <th>{{ trans('labels.backend.leads.table.last_call') }}</th>
                                <th>Last Action</th>
                                <th>Follow Up</th>
                                <th>{{ trans('labels.backend.leads.table.data_medium') }}</th>
                                <th>{{ trans('labels.backend.leads.table.phase') }}</th>
                                <th>{{ trans('labels.backend.leads.table.status') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div><!--table-responsive-->
            </div><!--table-responsive-->

            <!-- <div class="table-responsive">
                <div class="table-responsive lead-call-list" id="table_sales">

                </div>
            </div> -->
        </div><!-- /.box-body -->
    </div><!--box-->
    <style type="text/css">
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
    <div class="box box-success" id="sales-box" style="display:none">
        <div class="box-header with-border">
            <div class="table-responsive">
                <div class="table-responsive lead-call-list">
                    <h3 class="text-center">Total Sales</h3>
                    <p class="text-center">Total sales between <span id="dateBetween"></span></p>
                    <p class="text-center"><strong>Total Kits - <span id="total_count"></span></strong></p>
                    <hr>
                    <input type="text" id="myInput" onkeyup="sourceSearch()" placeholder="Search for source.." title="Type in a source">
                    <table id="Sales-table" class="main-table table-hover table-striped mt-5" style="width:100%;margin-top: 20px;border-collapse: separate;    border-spacing: 0 1em;">
                        <thead>
                            <tr>
                                <th  >#</th>
                                <th >Lead Source</th>
                                <th >Total Sale</th>
                            </tr>
                        </thead>
                        <tbody  id="table_sales">
                            <tr class="text-center">
                                <td colspan="3"><h4>No data found!</h4></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
    </script>
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
   {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.date.extensions.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.extensions.js") }}
    {{ Html::script("js/backend/plugin/datepicker/bootstrap-datepicker.js") }}
    {{ Html::script("js/backend/plugin/select2/select2.full.min.js") }}
    <script src="https://cdn.datatables.net/plug-ins/1.10.12/pagination/input.js"></script>
    <script>
         $(document).ready(function () {
    //change selectboxes to selectize mode to be searchable
       $("select").select2();
    });

    $(function() {
        var startDate = '';
        var endDate = '';

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#datePickerLead span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
        
        $.fn.dataTable.ext.type.order['date-pre'] = function ( d ) {
            return Date.parse( d ) || Infinity;
        }
        var leadTable = $('#lead-list-table').DataTable({
            processing: true,
            serverSide: true,
           "lengthMenu": [[10, 25, 30,50, 100, 200, 500, 1000, 2000], [10,25,30,50, 100, 200, 500, 1000, 2000]],
            "pageLength": 50,            
            createdRow: function( row, data, dataIndex ) {
                $(row).attr('data-val', data.id);
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
                url: '{{ route("admin.lead.assigned.get") }}',
                type: 'post',
                data: function(d) {
                    d.city = $('#cityFilter').val();
                    d.type = $('#typeFilter').val();
                    d.medium = $('#sourceFilter').val();
                    d.phase = $('#leadPhaseFilter').val();
                    d.assignedTo = $('#assignedTo').val();
                    d.start_date = startDate;
                    d.end_date = endDate;
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
                {data: 'id', name:  '{{config('access.leads_table')}}.id', searchable: false, orderable:false},
                {data: 'country_code', name:  '{{config('access.leads_table')}}.country_code', orderable:false},
                {data: 'phone', name:  '{{config('access.leads_table')}}.phone', orderable:false},
                {data: 'name', name:  '{{config('access.leads_table')}}.name',searchable: false, orderable:false},
                {data: 'assigned_to', name:  '{{config('access.leads_table')}}.assigned_to', searchable: false, orderable:false},
                {data: 'city', name:  '{{config('access.leads_table')}}.city',searchable: false, orderable:false},
                {data: 'last_call', name:  '{{config('access.leads_table')}}.last_call', searchable: false, orderable:false},
                {data: 'last_updated', name:  '{{config('access.leads_table')}}.last_updated', searchable: false, orderable:false},
                {data: 'next_follow_up', name:  '{{config('access.leads_table')}}.next_follow_up', searchable: false, orderable:false},
                {data: 'data_medium', name:  '{{config('access.leads_table')}}.data_medium', searchable: false, orderable:false},
                {data: 'phase', name:  '{{config('access.leads_table')}}.phase', searchable: false, orderable:false},
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
        $("#btnExport").click(function(e){
            window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#lead-list-table').parent().html()));
        });
        $('#cityFilter, #typeFilter, #assignedTo, #sourceFilter, #leadPhaseFilter').change(function(){
            city = $('#cityFilter').val();
            type = $('#typeFilter').val();
            medium = $('#sourceFilter').val();
            phase = $('#leadPhaseFilter').val();
            assignedTo = $('#assignedTo').val();
                  
             $.ajax({
                url: '{{ route("admin.lead.assigned.getAssignedlead") }}',
                type : 'post',
                'data' : 'city='+city+'&type='+type+'&medium='+medium+'&phase='+phase+'&assignedTo='+assignedTo+'&startDate='+startDate+'&endDate='+endDate,
                success: function(response)
                {
                    $('#sales-box').show();
                   var html = startDate+' - '+endDate
                       
                   $('#dateBetween').html(html);
                   $('#table_sales').html(response.html);
                   $('#total_count').html(response.total);
                }
            });
            leadTable.draw();
        });

        // Handle click on "Select all" control
        $('#selectAllUser').on('click', function(){
            // Get all rows with search applied
            var rows = leadTable.rows({ 'search': 'applied' }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"]', rows).prop('checked', this.checked);

            enableDisableMassAssignment();
        });

        // Handle click on checkbox to set state of "Select all" control
        $('#lead-list-table tbody').on('change', 'input[type="checkbox"]', function(){
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
                            leadTable.draw(false);
                        }
                    }
                });
                
            }
        });

        $('#datePickerLead').daterangepicker({
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

        $('#datePickerLead').on('apply.daterangepicker', function(ev, picker) {
            cb(picker.startDate, picker.endDate);
            startDate = picker.startDate.format('YYYY-MM-DD');
            endDate = picker.endDate.format('YYYY-MM-DD');
            city = $('#cityFilter').val();
            type = $('#typeFilter').val();
            medium = $('#sourceFilter').val();
            phase = $('#leadPhaseFilter').val();
            assignedTo = $('#assignedTo').val();
                  
             $.ajax({
                    url: '{{ route("admin.lead.assigned.getAssignedlead") }}',
                    type : 'post',
                    'data' : 'city='+city+'&type='+type+'&medium='+medium+'&phase='+phase+'&assignedTo='+assignedTo+'&startDate='+startDate+'&endDate='+endDate,
                    success: function(response)
                    {
                       $('#sales-box').show();
                       var html = startDate+' - '+endDate
                       
                       $('#dateBetween').html(html);
                       $('#table_sales').html(response.html);
                       $('#total_count').html(response.total);
                       
                    }
                });
            leadTable.draw(false);
        });

        $('#datePickerLead').on('cancel.daterangepicker', function(ev, picker) {
            $('#datePickerLead span').html('Select Date Range');
            startDate = '';
            endDate = '';
        });

    });
    </script>
    {{ Html::script("js/backend/lead.js?t=".time()) }}
@endsection
