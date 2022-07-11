@extends ('backend.layouts.app')

@section ('title', 'Subscriptions Expiring')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style type="text/css">
        .pagination
        {
            float: right;
        }
    </style>

@endsection

@section('page-header')
    <h1>Reports</h1>
    <div class="pull-right">
     
    </div>
@endsection

@section('content')
<div class="row">
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">    
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Subscription Expiring Soon</h3>
                    <p>Subscription Between {{$startDate}} - {{$endDate}} </p>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">
                            <button class="btn btn-success btn-xs disabled" disabled id="massAssignment">Mass Assigned To</button>
                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="text-center mx-auto" style="padding-left: 20px;">
                    <form method="get">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-1">
                                    <label style="margin-top:6%">Show Entries</label>
                                </div>
                                <div class="col-md-1">
                                    <select name="p" class="form-control" onchange="display_entries(this.value);" >
                                        <option value="5" {{($pageinate == 5 ? 'selected' : "")}}>5</option>
                                        <option value="10" {{($pageinate == 10 ? 'selected' : "")}}>10</option>
                                        <option value="15" {{($pageinate == 15 ? 'selected' : "")}}>15</option>
                                        <option value="20" {{($pageinate == 20 ? 'selected' : "")}}>20</option>
                                        <option value="25" {{($pageinate == 25 ? 'selected' : "")}}>25</option>
                                        <option value="30" {{($pageinate == 30 ? 'selected' : "")}}>30</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-1">
                                    <label style="margin-top:6%">Select Package</label>
                                </div>
                                <div class="col-md-3">
                                    <select name="package_name" class="form-control" >
                                        <option disabled>Select Package</option>
                                        @if($all_packages_name)
                                            @foreach($all_packages_name as $package)
                                                <option value="{{$package->package_name}}" @if($package->package_name == $package_search) selected @endif>{{$package->package_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success" style="float: left;">Filter</button>
                                </div>
                              <!--   <div class="col-md-12">
                                    <h2 style="color:red">Lead Assign has been disabled for 10-15 minutes for some technical changes. Please do not assign any lead.</h2>
                                </div> -->
                            </div>
                        </div>
                        
                        
                    </form>
                </div>
                <div class="box-body">
                   <table class="main-table table-hover table-striped mt-5" style="width:100%;margin-top: 20px;border-collapse: separate;    border-spacing: 0 1em;" id="oldLeads">
                    <thead>
                      <tr>
                        <th><input type="checkbox" name="select_all" value="1" id="selectAllUser"></th>
                        <th>DataUser ID</th>
                        <th>Lead ID</th>
                        <th>Name</th>
                        <th>CC</th>
                        <th>Phone</th>
                        <th>Package Name</th>
                        <th>Source</th>
                        <th>Valid From</th>
                        <th>Valid To</th>
                        <!-- <th>Action</th> -->
                      </tr>
                    </thead>
                    <tbody id="table_sales">
                        @if($Oldleads)
                            @php $i=1; @endphp
                              
                            @foreach ($Oldleads as $leads)
                              
                              <tr>
                                <td>
                                        @if($leads->datauser_id != '')
                                            <input type="checkbox" onclick="enableDisableMassAssignment();" class="leadCheck" id="du-{{$leads->datauser_id}}" data-id="{{$leads->id}}" name="id[]" value="{{$leads->datauser_id}}">
                                        @else
                                            
                                        @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->datauser_id}}
                                        </a>
                                    @else
                                        {{$leads->datauser_id}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a onclick="incoming({{$leads->lead_id}})"><span id="lead-dataval{{$leads->lead_id}}" data-val="{{$leads->lead_id}}" data-name="{{$leads->user_name}}"></span>
                                        {{$leads->lead_id}}
                                        </a>
                                       
                                    @else
                                        {{$leads->lead_id}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->user_name}}
                                        </a>
                                    @else
                                        {{$leads->user_name}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->country_code}}
                                        </a>
                                    @else
                                        {{$leads->country_code}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->user_mobile}}
                                        </a>
                                    @else
                                        {{$leads->user_mobile}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->package_name}}
                                        </a>
                                    @else
                                        {{$leads->package_name}}
                                    @endif
                                    
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->data_medium}}
                                        </a>
                                    @else
                                        {{$leads->data_medium}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->valid_from}}
                                        </a>
                                    @else
                                        {{$leads->valid_from}}
                                    @endif
                                </td>
                                <td>
                                    @if($leads->lead_id != '')
                                        <a style="cursor: pointer" onclick="incoming({{$leads->lead_id}})">
                                        {{$leads->valid_thru}}
                                        </a>
                                    @else
                                        {{$leads->valid_thru}}
                                    @endif
                                </td>
                                
                              </tr>
                               
                                
                              @php $i++;  @endphp
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" >No data found!</td>
                            </tr>
                        @endif
                    </tbody>
                  </table>
                  @if($package_search != "" && $pageinate != "")
                    {!! $Oldleads->appends(['package_name' => $package_search,'p'=>$pageinate])->links() !!}
                  @elseif($package_search != "" && $pageinate == "")
                    {!! $Oldleads->appends(['package_name' => $package_search])->links() !!}
                  @elseif($package_search == "" && $pageinate != "")
                    {!! $Oldleads->appends(['p' => $pageinate])->links() !!}
                  @else
                    {!! $Oldleads->links() !!}
                  @endif
                  <div class="mt-3">Showing {{($Oldleads->currentpage()-1)*$Oldleads->perpage()+1}} to {{$Oldleads->currentpage()*$Oldleads->perpage()}}  of  {{$Oldleads->total()}} entries
                  </div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
</div>
<input type="hidden" name="package_names" id="package_names" value="{{$package_search}}">
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
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script type="text/javascript">

    function incoming(lead_id)
    {
        $("#showLeadModal").attr('data-val', $('#lead-dataval'+lead_id).attr('data-val'));
        var html = $('#lead-dataval'+lead_id).attr('data-name') + ' <span></span>';
        $("#showLeadModal").find('.modal-title').html(html);
        $("#showLeadModal").modal({
            backdrop: 'static',
            keyboard: false,
            show: true
        });
    }

//     $(document).ready( function () {
//         $('#oldLeads').DataTable({
//     // display everything
//     // "aLengthMenu": [[25, 50, 75, -1], [25, 50, 75, "All"]],
//     // "iDisplayLength": -1
//     "aaSorting": [[ 0, "asc" ]] // Sort by first column descending
// });
//     } );

    function assignLeadOld(id)
    {
        $('#dataUserId').val(id);
        $('#assignLeadModal').modal('show');
    }
  </script>

  <script type="text/javascript">
          // Handle click on "Select all" control
        $('#selectAllUser').on('click', function(){
            // Get all rows with search applied
            // var rows = leadTable.rows({ 'search': 'applied' }).nodes();
            // Check/uncheck checkboxes for all rows in the table
            $('input[type="checkbox"]').prop('checked', this.checked);

            enableDisableMassAssignment();
        });

        // Handle click on checkbox to set state of "Select all" control
        // $('#lead-list-table tbody').on('change', 'input[type="checkbox"]', function(){
        //     // If checkbox is not checked
        //     if(!this.checked){
        //         var el = $('#selectAllUser').get(0);
        //         // If "Select all" control is checked and has 'indeterminate' property
        //         if(el && el.checked && ('indeterminate' in el)){
        //             // Set visual state of "Select all" control
        //             // as 'indeterminate'
        //             el.indeterminate = true;
        //         }
        //     }

        //     enableDisableMassAssignment();
        // });

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
            // var ids = $('input[name="id[]"]');
            var ids = $('input[name="id[]"]').serialize();

            var dataid = [];
            $.each($("input[name='id[]']:checked"), function(){
                dataid.push($(this).attr("data-id"));
            });
            // console.log(dataid); debugger;
            // var dataid = $(this).attr("data-id")
            // var ids1 = $('input[name="id1[]"]').serialize();
            
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
                    url : baseURL + '/admin/ajax/moveToLead1',
                    type : 'post',
                    'data' : 'callDate='+callDate+'&executive='+executive+'&dataUserId='+dataUserId+'&assignToMass='+assignToMass+'&'+ids,
                    success: function(response)
                    {
                        if(response.Status == '200')
                        {
                            $.ajax({
                                url : baseURL + '/admin/Updatesubscription',
                                type : 'POST',
                                data : { dataid: dataid, executive:executive},
                                success: function(response)
                                {
                                    if(response.Status == '200')
                                    {
                                        
                                        $('#assignLeadModal').modal('hide');
                                        $(".leadCheck").prop("checked", false);
                                        // $('input:checked').not('.selectAllUser').parents("tr").remove();
                                        location.reload();
                                      
                                    }
                                }
                            });
                            $('#assignLeadModal').modal('hide');
                            $(".leadCheck").prop("checked", false);
                            // $('input:checked').not('.selectAllUser').parents("tr").remove();
                          
                        }
                    }
                });
                
            }
        });
function display_entries(value)
{
    var pname = $('#package_names').val()
    if(pname != '')
    {
        window.location = baseURL + '/admin/lead/expiringPlan?p=' + value + '&package_name='+pname;
    }
    else
    {
        window.location = baseURL + '/admin/lead/expiringPlan?p=' + value;
    }
}

  </script>

      {{ Html::script("js/backend/lead.js?time=".time()) }}
@endsection
