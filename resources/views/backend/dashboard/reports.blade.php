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
               
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->
@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
  
@endsection
