@extends ('backend.layouts.app')
@section ('title', 'Lead Performance')
@section('after-styles')
{{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
{{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
{{ Html::style("css/backend/plugin/datetimepicker/bootstrap-datetimepicker.min.css") }}
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css" />
@endsection
@section('page-header')
<h1>Lead Performance</h1>
@endsection
<style type="text/css">
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
   /*table.header tr td { font-size: 13px; }
   .t-height {
   display:block;
   height:80vh !important;
   overflow:auto;
   }
   thead, tbody tr {
   display:table;
   width:100%;
   table-layout:fixed;
   }
   thead {
   }
   th {
   text-align: left;
   }*/
   /*td {
   text-align: left;
   }*/
   .table-scroll {
   position: relative;
   width:100%;
   z-index: 1;
   margin: auto;
   overflow: auto;
   height: 700px;
   }
   .table-scroll table {
   width: 100%;
   min-width: 1280px;
   margin: auto;
   border-collapse: separate;
   border-spacing: 0;
   font-size: 13px;
   }
   .table-wrap {
   position: relative;
   }
   .table-scroll th,
   .table-scroll td {
   padding: 5px 10px;
   /*border: 1px solid #000;*/
   /*background: #fff;*/
   vertical-align: top;
   }
   .table-scroll thead th {
   background: #b5b7b9;
   color: #000;
   position: -webkit-sticky;
   position: sticky;
   top: 0;
   }
   /* safari and ios need the tfoot itself to be position:sticky also */
   .table-scroll tfoot,
   .table-scroll tfoot th,
   .table-scroll tfoot td {
   position: -webkit-sticky;
   position: sticky;
   bottom: 0;
   background: #666;
   color: #fff;
   z-index:4;
   }
   a:focus {
   background: red;
   } /* testing links*/
   th:first-child {
   position: -webkit-sticky;
   position: sticky;
   left: 0;
   z-index: 2;
   background: #d0d2d3;
   }
   thead th:first-child,
   tfoot th:first-child {
   z-index: 5;
   }
   .multiselect
   {
   width: 290px;
   }
   .multiselect-container 
   {
   width: 100%;
   height: 300px;
   overflow-x: scroll;
   padding: 10px;
   z-index: 1;
   }
   .btn-group
   {
   background: #fff;
   cursor: pointer;
   padding: 5px 5px;
   /* border: 1px solid #ccc; */
   width: 310px;
   /* position: absolute; */
   /* right: 170px; */
   margin-right: 20px;
   }
   .filter-div
   {
   right: 540px;
   position: absolute;
   top: 0;
   }
   .multiselect-container
   {
   padding: 0px 10px !important;
   }
#myInput {
  background-image: url('/css/searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

</style>
@section('content')
<div class="box box-success">
   <div class="box-header with-border mb-10">
      <h3 class="box-title">Lead Performance</h3>
      <span class="text-danger" id="resultPerformance"></span>
      <span class="has-spinner" id="calendar-filter">
      <span class="spinner"><i class="fa fa-refresh fa-spin"></i></span></span>
      <span class="has-spinner text-danger" style="display:none" id="filter-message" ></span>
      <div class="filter-div">
         <label>Filter Source</label>
         <select name="langOpt[]" class="pull-right" multiple id="langOpt">
            @foreach($dataMediums as $source)
            <option value="{{ $source }}">{{ $source }}</option>
            @endforeach
         </select>
         <button class="btn btn-success" onclick="filterSource();" id="source-filter"> Filter </button>
      </div>
      <div class="box-tools pull-right">
         <div id="datePickerLead" style="background: #fff; cursor: pointer; padding: 5px 5px; border: 1px solid #ccc; width: 310px;position: absolute;right: 170px;margin-right: 20px;">
            <i class="fa fa-calendar"></i>&nbsp;
            <span>Select Date Range</span> <i class="fa fa-caret-down"></i>
         </div>
         <input type="hidden" name="endDate" id="endDate" value="">
         <input type="hidden" name="startDate" id="startDate" value="">
         <div class="pull-right mb-10">
            <button class="btn btn-success btn-xs has-spinner" id="refresh-performance" onclick="refershPerformance();" ><span class="spinner"><i class="fa fa-refresh fa-spin"></i></span> Refresh</button>
            <button id="btnExport" class="btn btn-success btn-xs" onclick="fnExcelReport();" {{ $html == '' ? 'disabled' : ''}}> Excel Export</button>
         </div>
         <div class="clearfix"></div>
      </div>
      <!--box-tools pull-right-->
   </div>
   <!-- /.box-header -->
   <div class="box-body">
      <div id="table-scroll" class="table-scroll">
        <input type="text" id="myInput" onkeyup="sourceSearch()" placeholder="Search for source.." title="Type in a source">
         <table id="workforce-table" class="main-table table-hover table-striped">
            @if($html == '')
            <thead>
               <tr>
                  <th scope="col">Lead Source</th>
                  <th scope="col">New Leads</th>
                  <th scope="col">Open</th>
                  <th scope="col">Hot</th>
                  <th scope="col">Mild</th>
                  <th scope="col">Cold</th>
                  <th scope="col">Dead</th>
                  <th scope="col">Sale</th>
                  <th scope="col">No Answer</th>
                  <th scope="col">Busy</th>
                  <th scope="col">Not Intrested</th>
               </tr>
            </thead>
            <tbody>
            </tbody>
            @else
            {!! $html !!}
            @endif
         </table>
      </div>
   </div>
   <!-- /.box-body -->
</div>
<!--box-->
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
   $(function() {
      var startDate = '';
      var endDate = '';
   
      var start = moment();
      var end = moment();
   
      function cb(start, end) {
          $('#datePickerLead span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
      }
    
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
        $('#calendar-filter').toggleClass('active');
        $('#refresh-performance').attr("disabled", true);
        $('#btnExport').attr("disabled", true);
        $('#filter-message').html('');
        $('#filter-message').html('<small>Preparing data. Please do not close or refresh page. </small>');
        $('#source-filter').attr("disabled", true);
        cb(picker.startDate, picker.endDate);
        startDate = picker.startDate.format('YYYY-MM-DD');
        endDate = picker.endDate.format('YYYY-MM-DD');
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);
        $('#source-filter').attr("disabled", true);
           var open = [];
           $("#langOpt option:selected").each(function(){
            open.push($(this).val());
           });
           var sources = open.join(",");
        $.ajax({
            url : baseURL + '/admin/lead/getleadPerformanceByDate',
            type : 'get',
            'data' : 'startDate='+startDate+'&endDate='+endDate+'&sources='+sources,
            success: function(response)
            {
                $("#calendar-filter").removeClass("active");
                $('#workforce-table').html(response);
                $('#resultPerformance').html('<small>Display results from '+startDate+' to '+endDate+'. </small>');
                $('#filter-message').html('');
                $('#btnExport').attr("disabled", false);
                $('#refresh-performance').attr("disabled", false);
                if(sources.length <= 0)
                {
                    $("#langOpt").multiselect( 'refresh' );
                    $("#langOpt").multiselect("clearSelection");
                }
                $('#source-filter').attr("disabled", false);
            }
        });
   
    });
   
    $('#datePickerLead').on('cancel.daterangepicker', function(ev, picker) {
        $('#datePickerLead span').html('Select Date Range');
        startDate = '';
        endDate = '';
    });
   });
   
   function refershPerformance()
   {
    $('#filter-message').html('');
    $('#refresh-performance').toggleClass('active');
    $('#refresh-performance').attr("disabled", true);
    $('#btnExport').attr("disabled", true);
    $('#source-filter').attr("disabled", true);
   
    $('#filter-message').html('<small>Data has been updating. Please do not close or refresh page. </small>');
    $.ajax({
        url : baseURL + '/admin/lead/getleadPerformance',
        type : 'get',
        success: function(response)
        {
            $('#resultPerformance').html('');
            $('#workforce-table').html(response);
            $("#refresh-performance").removeClass("active");
            $('#refresh-performance').attr("disabled", false);
            $('#filter-message').html('');
            $('#btnExport').attr("disabled", false);
            $('#source-filter').attr("disabled", false);
        }
    });
   }
   function fnExcelReport()
   {
   var tab_text="<table><tr bgcolor='#87AFC6'>";
   var textRange; var j=0;
   tab = document.getElementById('workforce-table'); // id of table
   
   for(j = 0 ; j < tab.rows.length ; j++) 
   {     
    tab_text=tab_text+tab.rows[j].innerHTML+"</tr>";
    //tab_text=tab_text+"</tr>";
   }
   
   tab_text=tab_text+"</table>";
   tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
   tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
   tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params
   
   var ua = window.navigator.userAgent;
   var msie = ua.indexOf("MSIE "); 
   
   if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
   {
    txtArea1.document.open("txt/html","replace");
    txtArea1.document.write(tab_text);
    txtArea1.document.close();
    txtArea1.focus(); 
    sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Classmonitor.xls");
   }  
   else                 //other browser not tested on IE 11
    sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  
   
   return (sa);
   }
   
   $(document).ready(function() {
   $('#langOpt').multiselect({
   columns: 1,
   placeholder: 'Select Source',
   search: true
   });
   });
   
   function filterSource()
   {
   $('#calendar-filter').toggleClass('active');
   $('#refresh-performance').attr("disabled", true);
   $('#btnExport').attr("disabled", true);
   $('#filter-message').html('<small>Preparing data. Please do not close or refresh page. </small>');
   $('#source-filter').attr("disabled", true);
   var open = [];
   $("#langOpt option:selected").each(function(){
    open.push($(this).val());
   });
   var sources = open.join(",");
   var startDate = $('#startDate').val();
   var endDate = $('#endDate').val();

   $.ajax({
            url : baseURL + '/admin/lead/getleadPerformanceByfilter',
            type : 'get',
            'data' : 'startDate='+startDate+'&endDate='+endDate+'&sources='+sources,
            success: function(response)
            {
                $("#calendar-filter").removeClass("active");
                $('#workforce-table').html(response);
                $('#filter-message').html('');
                $('#btnExport').attr("disabled", false);
                $('#refresh-performance').attr("disabled", false);
                $('#source-filter').attr("disabled", false);
            }
        });
   }

function sourceSearch() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("workforce-table");
  tr = table.getElementsByTagName("tr");
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