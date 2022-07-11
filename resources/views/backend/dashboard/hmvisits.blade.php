@extends ('backend.layouts.app')

@section ('title', 'Visits')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>Visits</h1>
   
@endsection

@section('content')
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">Visits</h3>
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
                            <th>Name</th>
                            <th>Email</th>
                            <th>Country Code</th>
                            <th>Phone Number</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $row)
                        <tr>
                            <td>{{ $row->name }}</td>
                            <td>{{ $row->email }}</td>
                            <td>{{ $row->countryCode }}</td>
                            <td>{{ $row->mobile }}</td>
                            <td>{{ $row->message }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {!! $data->links() !!}
            </div><!--table-responsive-->
        </div><!-- /.box-body -->
    </div><!--box-->
@endsection

@section('after-scripts')
    
  
@endsection
