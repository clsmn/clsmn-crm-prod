@extends ('backend.layouts.app')

@section ('title', 'Delight Sales')

@section('after-styles')
    <style type="text/css">
        .pagination
        {
            float: right;
        }
    </style>
@endsection

@section('page-header')
    <h1>
        Delight Sales
    </h1>


@endsection

@section('content')
    
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Delight Sales</h3>
        </div><!-- /.box-header -->
        @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
        <div class="text-center mx-auto" style="padding-left: 20px;">
            <form method="get">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-1">
                            
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-1">
                            <label style="margin-top:6%">Select Executive</label>
                        </div>
                        <div class="col-md-3">
                            <select name="id" class="form-control" >
                                <option disabled selected>Select Executive</option>
                                <option value ="all" @if($id == 'all' || $id == null) selected @endif>All</option>
                                <option value="none" @if($id == 'none') selected @endif>Assigned to none</option>
                                @if($executives)
                                    @foreach($executives as $users)
                                        <option value="{{$users->id}}" @if($users->id == $id) selected @endif>{{$users->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success" style="float: left;">Filter</button>
                        </div>
                    </div>
                </div>
                
                
            </form>
        </div>
        @endif
        <div class="box-body">
            <div class="table-responsive">
                <table id="data-bank-user-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Sale Date</th>
                            <th>Package Name</th>
                            <th>Client Name</th>
                             @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                            <th>Assigned To</th>
                            @else
                            <th>Country Code</th>
                            <th>Phone Number</th>
                            @endif
                            <th>Follow Up 1</th>
                            <th>Follow Up 2</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!$sales->isEmpty())
                            @foreach($sales as $value)
                                <tr>
                                    <td>{{ date('d M Y h:i A', strtotime($value->created_at)) }}</td>
                                    <td>{{ $value->product_name }}</td>
                                    <td>{{ $value->client_name }}</td>
                                    @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                                    <td>@if(isset($value->user->name)) {{ $value->user->name }} @endif</td>
                                    @else
                                    <td>{{ $value->country_code }}</td>
                                    <td>{{ $value->phone }}</td>
                                    @endif
                                    <td id="row-td-f1-{{$value->id}}" >
                                        @if($value->followup_1 == 1)
                                            <input type="checkbox" checked disabled>
                                        @else
                                            <input type="checkbox" id="followUp1-{{$value->id}}" onclick="updateRow({{$value->id}},'f1')" name="followUp1" value="1">
                                        @endif
                                    </td>
                                    <td id="row-td-f2-{{$value->id}}" >
                                        @if($value->followup_2 == 1)
                                            <input type="checkbox" checked disabled>
                                        @else
                                            <input type="checkbox" id="followUp2-{{$value->id}}" onclick="updateRow({{$value->id}},'f2')" name="followUp2" value="1"></td>
                                        @endif
                                    <td>
                                        <textarea class="form-control" id="Comment-{{$value->id}}" onfocus="textFocus({{$value->id}});" name="Comment" rows="2" style="resize: none">{{ $value->comment }}</textarea>
                                        <input type="hidden" id="Comment-hidden-{{$value->id}}" value="{{ $value->comment }}">
                                        <span style="float:right;" class="mt-5 hidden" id="row-button-{{$value->id}}">
                                            <button type="button" class="btn btn-success btn-xs"  onclick="updateRow({{$value->id}},'save')">Save</button>
                                            <button type="button" class="btn btn-danger btn-xs" onclick="textCancel({{$value->id}});">Cancel</button>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No Data Found</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                @if($id != "" )
                    {!! $sales->appends(['id' => $id])->links() !!}
                @else
                    {!! $sales->links() !!}
                @endif
                <div class="mt-3">Showing {{($sales->currentpage()-1)*$sales->perpage()+1}} to {{$sales->currentpage()*$sales->perpage()}}  of  {{$sales->total()}} entries
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
            var old = $('#Comment-hidden-'+id).val();
            $('#Comment-'+id).val(old);

            $('#row-button-'+id).addClass("hidden");
            // $('#row-button-'+id).removeClass('hidden');
        }
        function updateRow(id,type)
        {
            var value;
            if(type == 'f1')
            {
                value = $('#followUp1-'+id).val();
                $('#followUp1-'+id).attr("disabled", true);
            }
            if(type == 'f2')
            {
                value = $('#followUp2-'+id).val();
                $('#followUp2-'+id).attr("disabled", true);
            }
            if(type == 'save')
            {
                value = $('#Comment-'+id).val();
            }
            $.ajax({
                    url : baseURL + '/admin/ajax/sales/updateDelightSale',
                    type : 'post',
                    'data' : 'id='+id+'&type='+type+'&value='+value,
                    success: function(response)
                    {
                        // console.log(response);
                        if(type == 'f1')
                        {
                            value = $('#followUp1-'+id).val();
                            $('#followUp1-'+id).attr("disabled", true);
                            $('row-td-f1-'+id).html('<input type="checkbox" checked>')
                        }
                        if(type == 'f2')
                        {
                            value = $('#followUp2-'+id).val();
                            $('row-td-f2-'+id).html('<input type="checkbox" checked>')
                        }
                        if(type == 'save')
                        {
                            $('#Comment-hidden-'+id).val(value);
                            $('#row-button-'+id).addClass("hidden");
                        }
                    }
                });
        }
    </script>
@endsection