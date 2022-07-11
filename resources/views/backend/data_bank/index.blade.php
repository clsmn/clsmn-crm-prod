@extends ('backend.layouts.app')

@section ('title', trans('labels.backend.data_bank.users'))

@section('after-styles')
        {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/select2/select2.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
   <!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script> -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <style>
        #data-bank-user-table_paginate .paginate_button{
            padding: 0 15px;
            cursor: pointer;
        }
        #data-bank-user-table_paginate .paginate_button.disabled{
            cursor: not-allowed;
        }
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
        {{ trans('labels.backend.data_bank.users') }}
    </h1>
    <button id="btnExport">Export</button>


@endsection

@section('content')
    
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans('labels.backend.data_bank.users') }}</h3>

            <div class="box-tools pull-right">
                <label style="position: absolute; right: 360px;width: 200px;margin-top: 5px;">Last Call Before: </label>
                <input id="lastCallDatePicker" class="form-control" placeholder="Select Date" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 150px;position: absolute;right: 200px;"/>
                <!-- <div id="datePickerLead" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 310px;position: absolute;right: 141px;">
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
                         <select id="stageFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="">Select Lead Status</option>
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
                        <select id="leadPhaseFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="">Select Phase</option>
                            <option value="buy_attempt">Buy Attempt</option>
                            <option value="cart">Cart Abandon</option>
                            <option value="trial">Trial Started</option>
                            <option value="kit_purchased">Kit Purchased</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="leadFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="">Show All</option>
                            <option value="0">Not moved to Lead</option>
                            <option value="1">Moved to Lead</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="subscriptionTypeFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="all">Select Subscription Type</option>
                            <option value="NULL">No Subscription</option>
                            <option value="FREE">Free</option>
                            <option value="PAID">Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="leadSourceFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="">Select Source</option>
                            @foreach($sources as $source)
                            <option value="{{ $source }}">{{ $source }}</option>
                            @endforeach
                        </select> 
                    </div>
                     <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <div id="datePickerLead" style="background: #fff; cursor: pointer; padding: 3px 10px; border: 1px solid #ccc; width: 100%;position: unset;right: 0px; border-radius: 5px;">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" style="margin-bottom: 10px;">
                        <select id="countryFilter" class="form-control" style="right: 0px; position:unset;width: 100%;">
                            <option value="" selected disabled>Select Country</option>
                            <option value="IN">India</option>
                            <option value="Other">International</option>
                        </select> 
                    </div>
                    <div class="col-md-2 col-sm-12 mb-3" id="countryCode_txt" style="margin-bottom: 10px; display: none;">
                        <input id="countryCodeFilter" class="form-control" style="right: 0px; position:unset;width: 100%;border-radius: 5px !important;
    height: 30px;" placeholder="Enter Country Code">
                    </div>
                </div>
                <hr>
            <div class="table-responsive">

             <!--      <select id="select-state" placeholder="Pick a state..."  class="form-control">
                        <option value="">Select a state...</option>
                        <option value="AL">Alabama</option>
                        <option value="AK">Alaska</option>
                        <option value="AZ">Arizona</option>
                        <option value="AR">Arkansas</option>
                        <option value="CA">California</option>
                        <option value="CO">Colorado</option>
                        <option value="CT">Connecticut</option>
                        <option value="DE">Delaware</option>
                        <option value="DC">District of Columbia</option>
                        <option value="FL">Florida</option>
                        <option value="GA">Georgia</option>
                        <option value="HI">Hawaii</option>
                        <option value="ID">Idaho</option>
                        <option value="IL">Illinois</option>
                        <option value="IN">Indiana</option>
                      </select> -->
               
                
                
                
                {{-- <select id="cityFilter" class="form-control">
                    <option value="">Select City</option>
                    @foreach($cities as $city)
                    <option value="{{ $city }}">{{ $city }}</option>
                    @endforeach
                </select> --}}
                
                <table id="data-bank-user-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th><input type="checkbox" name="select_all" value="1" id="selectAllUser"></th>
                            <th>{{ trans('labels.backend.data_bank.table.id') }}</th>
                            <th>CC</th>
                            <th>{{ trans('labels.backend.data_bank.table.phone') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.name') }}</th>
                            <th>Child Name</th>
                            <th>Child Age</th>
                            <th>Message</th>
                            <th>{{ trans('labels.backend.data_bank.table.city') }}</th>
                            {{-- <th>{{ trans('labels.backend.data_bank.table.learning') }}</th> --}}
                            <th>{{ trans('labels.backend.data_bank.table.data_medium') }}</th>
                            <th>Lead From</th>
                            <th>{{ trans('labels.backend.data_bank.table.phase') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.updated_at') }}</th>
                            <th>Last Call</th>
                            <th>Lead Status</th>
                            <th>{{ trans('labels.backend.data_bank.table.assigned_to') }}</th>
                            <th>{{ trans('labels.backend.data_bank.table.action') }}</th>
                        </tr>
                    </thead>
                </table>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->

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


    <script type="text/javascript">
    //     $(document).ready(function () {
    //         new TomSelect("#stageFilter",{
    //     create: false,
    //     sortField: {
    //         field: "text",
    //         direction: "asc"
    //     }
    // });
    //         });

    $(document).ready(function () {
    //change selectboxes to selectize mode to be searchable
       $("select").select2();
    });
    </script>
    <script>
    $(function() {
        var startDate = '';
        var endDate = '';

        var start = moment();
        var end = moment();

        function cb(start, end) {
            $('#datePickerLead span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        // $.fn.dataTable.ext.errMode = 'none';

        // $('#data-bank-user-table').on( 'error.dt', function ( e, settings, techNote, message ) {
        //     console.log( 'An error has been reported by DataTables: ', message );
        // } ) ;

        var table = $('#data-bank-user-table').DataTable({
                       
            processing: true,
            serverSide: true,
            autoWidth : false,          
            "lengthMenu": [[5,10, 25, 30,50, 100, 200, 500, 1000, 2000], [5,10,25,30,50, 100, 200, 500, 1000, 2000]],
            "pageLength": 25,            
            ajax: {
                url: '{{ route("admin.data.bank.get") }}',
                type: 'post',
                data: function(d) {
                    console.log(d)
                    // d.city = $('#cityFilter').val();
                    d.moved_to_lead = $('#leadFilter').val();
                    d.medium = $('#leadSourceFilter').val();
                    d.phase = $('#leadPhaseFilter').val();
                    d.stage = $('#stageFilter').val();
                    d.subscription_type = $('#subscriptionTypeFilter').val();
                    d.searchTerm = $('.dataTables_filter input[type="search"]').val();
                    d.last_call_before = $('#lastCallDatePicker').val();
                    d.start_date = startDate;
                    d.end_date = endDate;
                    d.country = $('#countryFilter').val();
                    d.country_code = $('#countryCodeFilter').val();
                }
            },
            'columnDefs': [{
                'targets': 0,
                'searchable': false,
                'orderable': false,
                'className': 'dt-body-center',
                'render': function (data, type, full, meta){
                    return '<input type="checkbox" class="leadCheck" name="id[]" value="' + $('<div/>').text(data).html() + '">';
                    // if(full.phase != 'Kit Purchased')
                    // {
                    // }else{
                    //     return '';
                    // }
                }
            }],
            columns: [
                {data: 'id'},
                {data: 'id', name:  'du.id', searchable: false, sortable: false},
                {data: 'country_code', name:  'du.country_code', sortable: false},
                {data: 'phone', name:  'du.phone', sortable: false},
                {data: 'name', name:  'du.name', sortable: false},
                {data: 'child_name', name:  'du.child_name', sortable: false},
                {data: 'ageGroup', name:  'du.ageGroup', sortable: false},
                {data: 'message', name:  'du.message', sortable: false},
                {data: 'city', name:  'du.city', searchable: false, sortable: false},
                // {data: 'learning', name:  'du.learning', searchable: false, sortable: false},
                {data: 'data_medium', name:  'du.data_medium', searchable: false, sortable: false},
                {data: 'lead_from', name:  'du.lead_from', searchable: true, sortable: false},
                {data: 'phase', name:  'du.phase', searchable: false, sortable: false},
                {data: 'updated_at', name:  'du.updated_at', searchable: false, sortable: false},
                {data: 'lead_last_call', name:  'du.lead_last_call', searchable: false, sortable: false},
                {data: 'lead_status', name:  'du.lead_status', searchable: false, sortable: false},
                {data: 'user_name', name:  'user_name', searchable: false, sortable: false},
                {data: 'action', name: 'action', searchable: false, sortable: false}
            ],            
            order: [[1, "asc"]],
            searchDelay: 500,
            "pagingType": "input",
        });
        $("#btnExport").click(function(e){
            window.open('data:application/vnd.ms-excel,' + encodeURIComponent($('#data-bank-user-table').parent().html()));
        });
         // Handle click on "Select all" control
        $('#selectAllUser').on('click', function(){
            // Get all rows with search applied
            var rows = table.rows({ 'search': 'applied' }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"]', rows).prop('checked', this.checked);

            enableDisableMassAssignment();
        });

        // Handle click on checkbox to set state of "Select all" control
        $('#data-bank-user-table tbody').on('change', 'input[type="checkbox"]', function(){
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

        $('#cityFilter, #leadSourceFilter, #subscriptionTypeFilter, #leadFilter, #leadPhaseFilter, #stageFilter, #countryFilter').change(function(){
            var c = $('#countryFilter').val();
            if(c == 'IN')
            {
                $('#countryCode_txt').hide();
                $('#countryCodeFilter').val('');
            }
            else if(c == 'Other')
            {
                $('#countryCode_txt').show();
            }
            else
            {
                $('#countryCode_txt').hide();
                $('#countryCodeFilter').val('');
            }
            table.draw();
        });
        $('#countryCodeFilter').change(function(){
            table.draw();
        });

        $('#schoolFilter').select2()

        $('#datePicker12').datepicker({
            format : 'yyyy-mm-dd'
        }).on('changeDate', function(ev){
            table.draw();
            $('#datePicker12').datepicker('hide');
        });

        $('body').on('click', '.assignLead', function(){
            $('#dataUserId').val($(this).attr('data-val'));
            $('#assignLeadModal').modal('show');
            $('#assignToMass').val('false');
        });

        $('#assignLeadModal').on('show.bs.modal', function (e) {
            $('.datemask').inputmask("dd/mm/yyyy");
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
                            table.draw(false);
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
            table.draw(false);
        });

        $('#datePickerLead').on('cancel.daterangepicker', function(ev, picker) {
            $('#datePickerLead span').html('Select Date Range');
            startDate = '';
            endDate = '';
        });

        $('#lastCallDatePicker').datepicker({
            endDate: 'today',
            format : 'yyyy-mm-dd',
        }).on('changeDate', function(ev){
            table.draw();
            $('#lastCallDatePicker').datepicker('hide');
        });
    });
    </script>
@endsection