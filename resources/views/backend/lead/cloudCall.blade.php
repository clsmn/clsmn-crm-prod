@extends ('backend.layouts.app')

@section ('title', 'Cloud Calls History')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('page-header')
    <h1>
        Cloud Calls History
    </h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
             <div class="nav-tabs-custom" style="padding:20px;">
             <div class="table-responsive">
            <table id="called-list-table" class="table table-condensed table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Call Type</th>
                        <th>Call Status</th>
                        <th>Customer Number</th>
                        <th>Executive Office24by7 username</th>
                        <th>Audio Recording</th>
                    </tr>
                </thead>
                <tbody>
                    @if($office24response)
                        @foreach($office24response as $response)
                        @php
                            $reoponse1 = json_decode($response->response);
                            
                        @endphp
                            <tr>
                                <td>{{ $reoponse1->Param_Call_Time }}</td>
                                <td>{{ $reoponse1->Param_Call_Type }}</td>
                                <td>{{ $reoponse1->Param_Call_Status }}</td>
                                <td>{{ $reoponse1->CallerNumber }}</td>
                                <td>{{ $reoponse1->Param_Agent_UserName }}</td>
                                <td>
                                    <audio controls>
                                      <source src="{{ $reoponse1->Param_Recording_FilePath }}" type="audio/mp3">
                                      
                                    </audio>
                                    
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
            <!-- nav-tabs-custom -->
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
        $(document).ready( function () {
    $('#called-list-table').DataTable();
} );
    </script>
   
    {{ Html::script("js/backend/lead.js?time=".time()) }}
@endsection
