@extends ('backend.layouts.app')

@section ('title', 'Re-Pitch Assigned')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datatables/dataTables.bootstrap.min.css") }}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.4/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.1.1/css/dataTables.dateTime.min.css" />
    <style type="text/css">
        .pagination
        {
            float: right;
        }
        .dt-buttons
        {
            padding: 30px 0px 0px 30px;
        }
        .dataTables_length
        {
            padding: 30px 0px 0px 30px;
        }
    </style>

@endsection

@section('page-header')
    <h1>Re-Pitch Assigned</h1>
    <div class="pull-right">
     
    </div>
@endsection

@section('content')
<div class="row">
    <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class=" col-lg-12 col-md-12 col-sm-12 col-xs-12">    
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Re-Pitch Assigned</h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">
                            
                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="row ">
                        <div class="col-md-3">
                            <div class="card">
                              <div class="card-body"><h3>Total Assigned Leads: <b>{{$assigned_total}}</b> </h3></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                              <div class="card-body"><h3>Total Sale: <b>{{$assignedSale_total}}</b></h3></div>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-auto text-center"> 
                        <form method="get" action="{{ route('admin.reports.expiringPlanReSale') }}">
                            <div class="col-md-2">
                                @php $value = request()->get('filter'); @endphp
                                <select class="form-control" name="filter">
                                    <option value="0" {{ $value == 0 ? 'selected' : '' }}>Assigned</option>
                                    <option value="1" {{ $value == 1 ? 'selected' : '' }} >Sale</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit">Filter</button>
                            </div>
                        </form>
                        <div class="col-md-2">
                            <label>From Assign Date:</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="min" name="min">
                        </div>
                        <div class="col-md-2">
                            <label>To Assign Date:</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="max" name="max">
                        </div>
                    </div>
                   
                    <table id="example" class="display nowrap" style="width:100%">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>CC</th>
                                <th>Mobile</th>
                                <th>Package Name</th>
                                <th>Purchase Date</th>
                                <th>Executive Name</th>
                                <th>Assign Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i=1; @endphp
                            @foreach($resale_data as $data)
                            <tr>
                                <td>{{$data->customer_name}}</td>
                                <td>{{$data->country_code}}</td>
                                <td>{{$data->user_mobile}}</td>
                                <td>{{$data->package_name}}</td>
                                <td>{{date('Y-m-d', strtotime($data->created_at))}}</td>
                                <td>{{$data->executive_name}}</td>
                                <td>{{date('Y-m-d', strtotime($data->assi_at))}}</td>
                                <td>
                                    @if($data->lead_status == 'open')
                                    <b>OPEN</b>
                                    @elseif($data->lead_status == 'hot')
                                    <b>HOT</b>
                                    @elseif($data->lead_status == 'mild')
                                    <b>MILD</b>
                                    @elseif($data->lead_status == 'cold')
                                    <b>COLD</b>
                                    @elseif($data->lead_status == 'dead')
                                    <b>DEAD</b>
                                    @elseif($data->lead_status == 'sale')
                                    <b>SALE</b>
                                    @elseif($data->lead_status == 'no_answer')
                                    <b>NO ANSWER</b>
                                    @elseif($data->lead_status == 'busy')
                                    <b>BUSY</b>
                                    @elseif($data->lead_status == 'not_interested')
                                    <b>NOT INTERESTED</b>
                                    @elseif($data->lead_status == 'feedback')
                                    <b>FEEDBACK</b>
                                    @elseif($data->lead_status == 'reference')
                                    <b>REFERENCE</b>
                                    @elseif($data->lead_status == 'guidence')
                                    <b>GUIDENCE</b>
                                    @elseif($data->lead_status == 'already_sale')
                                    <b>ALREADY SALE</b>
                                    @else
                                    <b>OPEN</b>
                                    @endif
                                </td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                               <th>Customer Name</th>
                                <th>CC</th>
                                <th>Mobile</th>
                                <th>Package Name</th>
                                <th>Purchase Date</th>
                                <th>Executive Name</th>
                                <th>Assign Date</th>
                                <th>Status</th>
                            </tr>
                        </tfoot>
                    </table>
                 
                </div><!-- /.box-body -->
            </div><!--box-->
        </div>
    </div>
</div>
      

@endsection

@section('after-scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    {{ Html::script("js/backend/plugin/datatables/jquery.dataTables.min.js") }}
    {{ Html::script("js/backend/plugin/datatables/dataTables.bootstrap.min.js") }}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>



    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/datetime/1.1.1/js/dataTables.dateTime.min.js"></script>



    <script type="text/javascript">
        var minDate, maxDate;
 
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var min = minDate.val();
                var max = maxDate.val();
                var date = new Date( data[4] );
         
                if (
                    ( min === null && max === null ) ||
                    ( min === null && date <= max ) ||
                    ( min <= date   && max === null ) ||
                    ( min <= date   && date <= max )
                ) {
                    return true;
                }
                return false;
            }
        );
         
        $(document).ready(function() {
            // Create date inputs
            minDate = new DateTime($('#min'), {
                format: 'YYYY-MM-DD'
            });
            maxDate = new DateTime($('#max'), {
                format: 'YYYY-MM-DD'
            });
         
            // DataTables initialisation
            var table = $('#example').DataTable( {
                        dom: 'Blfrtip',
                        bPaginate: true,
                        bLengthChange: true,
                        lengthMenu:[ 10, 25, 50, 100, 200, -1 ],
                        buttons: [
                            'copy', 'csv', 'excel', 'pdf', 'print'
                        ]
                    } );
            
            // Refilter the table
            $('#min, #max').on('change', function () {
                table.draw();
            });
        });
    </script>
      {{ Html::script("js/backend/lead.js?time=".time()) }}
@endsection
