@extends ('backend.layouts.app')

@section ('title', 'OTP')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>OTP</h1>
    <div class="pull-right">
        <form action="{{ route('admin.otp') }}" method="get">
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
            <h3 class="box-title">OTP</h3>
            <div class="box-tools pull-right">
                <div class="pull-right mb-10">

                </div>
                <div class="clearfix"></div>
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="workforce-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>UserID</th>
                            <th>Country Code</th>
                            <th>Phone Number</th>
                            <th>OTP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->country_code }}</td>
                            <td>{{ $row->phone }}</td>
                            <td>{{ $row->otp }}</td>
                            <td id="row_{{ $row->id }}"><a href="javascript:void(0)" class="resetPassword" data-val="{{ $row->id }}">Reset Password</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->
@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script>
    $(function() {
       $('.resetPassword').click(function(){
            var rowID = $(this).attr('data-val');
            $.ajax({
                url: '/admin/reset/default/password',
                type: 'POST',
                data: 'rowID='+rowID,
                dataType: 'json',
                success: function(response) {
                    if(response.status == 'success') {
                        $('#row_'+rowID).html('Password reset successfully.')
                    }
                }
            })
       });
    });
    </script>
@endsection
