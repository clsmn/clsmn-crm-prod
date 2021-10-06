@extends ('backend.layouts.app')

@section ('title', 'Lead Performance')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>Lead Performance</h1>
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Lead Performance</h3>
            <div class="box-tools pull-right">
                <div class="pull-right mb-10"></div>
                <div class="clearfix"></div>
            </div><!--box-tools pull-right-->
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="table-responsive">
                <table id="workforce-table" class="table table-condensed table-hover">
                    <thead>
                        <tr>
                            <th>Lead Source</th>
                            <th>Total Leads</th>
                            <th>Total Sales</th>
                            <th>% of sale</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->data_medium }}</td>
                            <td>{{ $row->totalLeads }}</td>
                            <td>{{ $row->totalSales }}</td>
                            <td>{{ round(($row->totalSales/$row->totalLeads)*100).'%' }}</td>
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
       
    });
    </script>
@endsection
