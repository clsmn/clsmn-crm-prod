@extends ('backend.layouts.app')

@section ('title', 'Facebook Campaigns')

@section('after-styles')
    <style type="text/css">
        .pagination
        {
            float: right;
        }
          .spinner {
           display: inline-block;
           opacity: 0;
           max-width: 0;
           -webkit-transition: opacity 0.25s, max-width 0.45s;
           -moz-transition: opacity 0.25s, max-width 0.45s;
           -o-transition: opacity 0.25s, max-width 0.45s;
           transition: opacity 0.25s, max-width 0.45s;
           /* Duration fixed since we animate additional hidden width */
           }
           .has-spinner.active {
           cursor: progress;
           }
           .has-spinner.active .spinner {
           opacity: 1;
           max-width: 50px;
           /* More than it will ever come, notice that this affects on animation duration */
           }
           #myInput {
                background-image: url(/css/searchicon.png);
                background-position: 10px 10px;
                background-repeat: no-repeat;
                width: 100%;
                font-size: 16px;
                padding: 12px 20px 12px 40px;
                border: 1px solid #ddd;
                margin-bottom: 12px;
            }
    </style>
@endsection

@section('page-header')
    <h1>
        Facebook Campaigns
    </h1>


@endsection

@section('content')
    
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Facebook Campaigns</h3>
            <span id="refresh-msg"></span>
            <div class="box-tools pull-right">
             <div class="pull-right mb-10">
                
                <button class="btn btn-success btn-xs has-spinner" id="refresh-performance" onclick="refershCampaigns();"><span class="spinner"><i class="fa fa-refresh fa-spin"></i></span> Refresh</button>
             </div>
             <div class="clearfix"></div>
          </div>
        </div><!-- /.box-header -->
        <div class="text-center mx-auto" style="padding: 20px;">
            <div class="row" style="background-color: #d2cece;">
                <div class="col-md-6">
                    <input type="text" id="myInput" onkeyup="sourceSearch()" style="width:100%;margin-top: 15px;color: #000;" class="form-control" placeholder="Search for Campaign.." title="Type in a Campaign">
                </div>
                <div class="col-md-6">
                    <div class="row" >
                        <form method="get">
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label style="margin-top:6%">Select Status</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="status"  style="width:100%" class="form-control">
                                        <option value="all" @if($s == 'all') selected @endif>All</option>
                                        @if($status)
                                            @foreach($status as $val)
                                                <option value="{{$val->status}}" @if($val->status == $s) selected @endif>{{$val->status}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success" style="width:100%" style="float: left;">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table id="Fb-campaign-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Campaign Name</th>
                            <th>FB Campaign ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>New</th>
                            <th>Existing</th>
                            <th>Source Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($campaigns as $value)
                            <tr>
                                <th>{{ $value->name }}</th>
                                <td>{{ $value->campaign_id }}</td>
                                <td>{{ $value->status }}</td>
                                <td>{{ date('d M Y h:i A', strtotime($value->created_at)) }}</td>
                                <td>{{ $value->total }}</td>
                                <td>{{ $value->new }}</td>
                                <td>{{ $value->existing }}</td>
                                <td>
                                    <textarea class="form-control" id="Fb-cmp-{{$value->id}}" onfocus="textFocus({{$value->id}});" name="Fb-cmp" rows="2" style="resize: none">{{ $value->campaign_source_name }}</textarea>
                                    <input type="hidden" id="Fb-cmp-hidden-{{$value->id}}" value="{{ $value->campaign_source_name }}">
                                    <span style="float:right;" class="mt-5 hidden" id="row-button-{{$value->id}}">
                                        <button type="button" class="btn btn-success btn-xs"  onclick="updateRow({{$value->id}},'{{$value->campaign_id}}')">Save</button>
                                        <button type="button" class="btn btn-danger btn-xs" onclick="textCancel({{$value->id}});">Cancel</button>
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($status != "" )
                    {!! $campaigns->appends(['status' => $s])->links() !!}
                @else
                    {!! $campaigns->links() !!}
                @endif
                <div class="mt-3">Showing {{($campaigns->currentpage()-1)*$campaigns->perpage()+1}} to {{$campaigns->currentpage()*$campaigns->perpage()}}  of  {{$campaigns->total()}} entries
                  </div>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->

@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript">
        function textFocus(id)
        {
            // alert();
            $('#row-button-'+id).removeClass("hidden");
            // $('#row-button-'+id).removeClass('hidden');
        }
         function textCancel(id)
        {
            // alert();
            var old = $('#Fb-cmp-hidden-'+id).val();
            $('#Fb-cmp-'+id).val(old);

            $('#row-button-'+id).addClass("hidden");
            // $('#row-button-'+id).removeClass('hidden');
        }
        function updateRow(id,cmp_idd)
        {
            var value = $('#Fb-cmp-'+id).val();
            $.ajax({
                    url : baseURL + '/admin/ajax/fb/updateCompaignData',
                    type : 'post',
                    'data' : 'id='+id+'&value='+value+'&cmp_id='+cmp_idd,
                    success: function(response)
                    {
                        
                            $('#Fb-cmp-hidden-'+id).val(value);
                            $('#row-button-'+id).addClass("hidden");
                        
                    }
                });
        }

        function refershCampaigns(id)
        {
            $.ajax({
                    url : baseURL + '/admin/ajax/fb/refreshCompaignList',
                    type : 'get',
                    success: function(response)
                    {
                        
                            $('#refresh-msg').html('List updated');
                            // window.location.reload();
                        
                    }
                });
        }
    function sourceSearch() {
      var input, filter, table, tr, td, i, txtValue;
      input = document.getElementById("myInput");
      filter = input.value.toUpperCase();
      table = document.getElementById("Fb-campaign-table");
      tr = table.getElementsByTagName("tr");
      console.log(tr.length);
      for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("th")[0];
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
@endsection