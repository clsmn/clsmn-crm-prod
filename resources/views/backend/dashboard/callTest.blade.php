@extends ('backend.layouts.app')

@section ('title', 'Call Test')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" />


@endsection

@section('page-header')
    <h1>Call Test
</h1>
@endsection


@section('content')
    <div class="box box-success">
         <div class="box-header with-border mb-10">
            <h3 class="box-title">Call Test</h3>
        </div><!-- /.box-header -->

        <div class="box-body">
            <div id="table-scroll" class="table-scroll">
              <button class="btn btn-success" onclick="getCallResponse();">Call</button>
            </div>


        </div><!-- /.box-body -->
    </div><!--box-->
@endsection

@section('after-scripts')
        {{ Html::script("js/backend/plugin/Bootstrap-Confirmation-2/bootstrap-confirmation.min.js") }}



    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script  src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key={{ env('GOOGLE_MAP_KEY') }}"></script>

    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

    {{ Html::script("js/backend/plugin/geocomplete/jquery.geocomplete.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.date.extensions.js") }}
    {{ Html::script("js/backend/plugin/input-mask/jquery.inputmask.extensions.js") }}
    {{ Html::script("js/backend/plugin/datepicker/bootstrap-datepicker.js") }}
    {{ Html::script("js/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.js") }}
    <script>
 

    function getCallResponse()
    {
        // $.ajax({
        // type:'POST',
        // url: 'https://app.office24by7.com/v1/communication/API/clickToDial',
        // data:{ apiKey:'a138e183-15b5-45c8-a05b-c2ab3f173be5', loginid:'riseom1', callerid:'02235155017' , phonenumber:'9685049688' , format:'json' },
        // dataType:'json',
        // success:function(data){
        //     console.log(data);
        // //        debugger
        // }
        // });
        
        $.ajax({
            url : baseURL + '/admin/lead/getCallResponse',
            type : 'get',
            contentType: "application/json",
            dataType: "json",
            success: function(response)
            {
               console.log(response);
               debugger
            }
        });
    }
  </script>
@endsection
