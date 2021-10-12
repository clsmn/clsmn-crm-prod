@extends ('backend.layouts.app')

@section ('title', 'Create Bulk Lead')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("plugins/fileuploads/css/dropify.min.css") }}
    {{ Html::style("plugins/EasyAutocomplete/easy-autocomplete.min.css") }}
@endsection

@section('page-header')
    <h1>Bulk Lead / <small style="color: #000000;">{{ $camp }}</small></h1>
    <style type="text/css">
        
#overlay{   
  position: fixed;
  top: 0;
  left: 0;
  z-index: 100;
  width: 100%;
  height:100%;
  display: none;
  background: rgba(0,0,0,0.6);
}
.cv-spinner {
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;  
}
.spinner {
  width: 40px;
  height: 40px;
  border: 4px #ddd solid;
  border-top: 4px #2e93e6 solid;
  border-radius: 50%;
  animation: sp-anime 0.8s infinite linear;
}
@keyframes sp-anime {
  100% { 
    transform: rotate(360deg); 
  }
}
.is-hide{
  display:none;
}
    </style>
@endsection

@section('content')
<div id="overlay">
  <div class="cv-spinner">
    <span class="spinner"></span>
  </div>
</div>
   
    @if(count($data) == 0)
        <form action="{{ route('admin.data.bank.bulk.post') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Bulk Lead (Max rows 250)</small></h3>
                    <div class="box-tools pull-right">
                        <div class="pull-right mb-10">
                            <small>Enter Campaign Name: </small> &nbsp;&nbsp;
                            <input type="text" name="camp" id="campValue" value="{{ $camp }}"> &nbsp;&nbsp; 
                            <span class="checkbox" style="display: inline-block">
                                <input type="hidden" value="0" name="changeSource"/>
                                <label><input type="checkbox" value="1" name="changeSource"/> Change Source Name</label>
                            </span> &nbsp;&nbsp;  &nbsp;&nbsp; 
                            <a href="{{ url('/storage/Bulk-Create-Sample.xlsx') }}">Sample File</a> &nbsp;&nbsp;
                            <button class="btn btn-success btn-xs" type="submit">Upload</button>
                        </div>
                        <div class="clearfix"></div>
                    </div><!--box-tools pull-right-->
                </div><!-- /.box-header -->

                <div class="box-body">
                    <input type="file" name="import_file" class="dropify" data-height="300" />
                    <br>
                    <div class="alert alert-warning">
                        <p>Campaign name shall be Alphanumeric & can have - (hypen) & _ (underscore)<br>
                        Excel needs to strictly have one sheet only.<br>
                        Phone Number is the only mandatory field (minimum 10 digits)<p>

                        <p>Entered leads first go in the data bank of system
                        In data bank we match the leads with our internal data based on phone numbers. <br>
                        Some leads name might change because of this. </p>
                    </div>
                </div><!-- /.box-body -->
            </div><!--box-->
        </form>
        <!-- end col -->
    @else
        <div class="row">
            <div class="col-md-2">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total Uploaded Leads:  <span id="">{{count($data)}}</span></h3>
                        <input type="hidden" name="errorLead" id="errorLead" value="0">
                        <div class="box-tools pull-left">
                            <div class="pull-right mb-10">
                              
                            </div>
                            <div class="clearfix"></div>
                        </div><!--box-tools pull-right-->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <ul id=""  style="list-style:none;">
                        </ul>
                    </div><!--box-->
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total Existing Leads: <span id="alreadyExistText">0</span></h3>
                        <input type="hidden" name="alreadyExist" id="alreadyExist" value="0">
                        <div class="box-tools pull-left">
                            <div class="pull-right mb-10">
                              
                            </div>
                            <div class="clearfix"></div>
                        </div><!--box-tools pull-right-->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <ul id="user-detailsAlready" style="list-style:none;">
                        </ul>
                    </div><!--box-->
                </div>
            </div>
            <div class="col-md-4">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Total New Leads:  <span id="newLeadText">0</span></h3>
                        <input type="hidden" name="newLead" id="newLead" value="0">
                        <div class="box-tools pull-left">
                            <div class="pull-right mb-10">
                              
                            </div>
                            <div class="clearfix"></div>
                        </div><!--box-tools pull-right-->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <ul id="user-detailsNew"  style="list-style:none;">
                        </ul>
                    </div><!--box-->
                </div>
            </div>
            <div class="col-md-2">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Not Uploaded Leads:  <span id="errorLeadText">0</span></h3>
                        <input type="hidden" name="errorLead" id="errorLead" value="0">
                        <div class="box-tools pull-left">
                            <div class="pull-right mb-10">
                              
                            </div>
                            <div class="clearfix"></div>
                        </div><!--box-tools pull-right-->
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <ul id="user-detailsError"  style="list-style:none;">
                        </ul>
                    </div><!--box-->
                </div>
            </div>
        </div>
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Bulk Lead</h3>
                <div class="box-tools pull-right">
                    <div class="pull-right mb-10">
                        <input type="text" id="camp" name="camp" readonly disabled value="{{ $camp }}"> &nbsp;&nbsp; 
                        @if($changeSource=='1')
                            <span class="text-danger">Yes, Update Source</span>
                        @else
                            <span class="text-default">Don't Udpate Source</span>
                        @endif
                        &nbsp;&nbsp; 
                        <button class="btn btn-success btn-xs" id="addAll">ADD ALL</button>
                    </div>
                    <div class="clearfix"></div>
                </div><!--box-tools pull-right-->
            </div><!-- /.box-header -->

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-striped m-b-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Country Code</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Ad Platform</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach($data as $key=>$row)
                            <tr id="frm_{{ $key }}" class="frm_row" data-val="{{ $key }}">
                                <td>{{$i}}</td>
                                <td>
                                    <input type="text" class="form-control name" name="datauser_name[]" value="{{ $row['name'] }}">
                                    <span class="name_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control country_code" name="datauser_country_code[]" value="{{ $row['country_code'] }}">
                                    <span class="country_code_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control phone" name="datauser_phone[]" value="{{ $row['phone'] }}">
                                    <span class="phone_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control city" name="datauser_city[]" value="{{ $row['city'] }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control ad_platform" name="datauser_ad_platform[]" value="{{ $row['ad_platform'] }}">
                                </td>
                                <td id="msg-{{ $key }}"></td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div><!-- /.box-body -->
        </div><!--box-->
    @endif

@endsection

@section('after-scripts')
    {{ Html::script('js/backend/access/users/script.js') }}
    {{ Html::script('plugins/EasyAutocomplete/jquery.easy-autocomplete.min.js') }}
    {{ Html::script("plugins/fileuploads/js/dropify.min.js") }}
    <script>
    $(function() {
        var base_url = '{{ env("APP_URL", "") }}';
        $.ajaxSetup({
            headers:
            { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        $('.dropify').dropify({
            messages: {
                'default': 'Drag and drop a file here or click',
                'replace': 'Drag and drop or click to replace',
                'remove': 'Remove',
                'error': 'Ooops, something wrong appended.'
            },
            error: {
                'fileSize': 'The file size is too big (1M max).'
            }
        });
        
        var options = JSON.parse('{!! $options !!}');

        $("#campValue").easyAutocomplete(options);
        
        $('#addAll').click(function(){
            
            $('.frm_row').each(function(){
                let self = $(this);
                let key = $(this).attr('data-val');
                let name = $(this).find('.name').val();
                let country_code = $(this).find('.country_code').val();
                let phone = $(this).find('.phone').val();
                let city = $(this).find('.city').val();
                let ad_platform = $(this).find('.ad_platform').val();
                let change_source = '{{ $changeSource }}';
                
                var notupload = 0;
                $.ajax({
                    url: base_url+'/admin/data/bank/bulk/create',
                    type: 'put',
                    data: {
                        medium: '{{ $camp }}',
                        name: name,
                        country_code: country_code,
                        phone: phone,
                        city: city,
                        ad_platform: ad_platform,
                        change_source: change_source,
                    },
                    dataType: 'json',
                    success: function(res){
                        if(res.status == '200')
                        {
                            var alreadyExist = $('#alreadyExist').val();
                            var newLead = $('#newLead').val();
                            var errorLead = $('#errorLead').val();
                            if(res.leadtype == "existing")
                            {
                               var existCount = +alreadyExist + 1;
                               $('#alreadyExist').val(existCount);
                               $('#alreadyExistText').html(existCount);
                               $('#user-detailsAlready').append('<li>'+existCount+'. '+res.dataUser.name+' - '+res.dataUser.country_code+'-'+res.dataUser.phone+' </li>');

                            }
                            if(res.leadtype == "new")
                            {
                                var newCount = +newLead + 1;
                                $('#newLead').val(newCount);
                                $('#newLeadText').html(newCount);
                                $('#user-detailsNew').append('<li>'+newCount+'. '+res.dataUser.name+' - '+res.dataUser.country_code+'-'+res.dataUser.phone+' </li>');
                            }
                            self.html('<td colspan="6" class="text-center">'+res.data+'</td>');


                            // setTimeout(function(){
                            //     self.remove();
                            // }, 5000);
                        }
                        else{
                            $('#msg-'+key).html(res.data);
                            var errorCount = +errorLead + 1;
                                $('#errorLead').val(errorCount);
                                $('#errorLeadText').html(newCount);
                                $('#user-detailsNew').append('<li>'+errorCount+'. '+res.dataUser.name+' - '+res.dataUser.country_code+'-'+res.dataUser.phone+' </li>');

                        }
                    },
                    error: function(xhr) {
                        if(xhr.status == 422) {
                            if(xhr.responseJSON.phone != undefined)
                            {
                                self.find('.phone_error').html(xhr.responseJSON.phone[0]);
                            }
                            if(xhr.responseJSON.name != undefined)
                            {
                                self.find('.name_error').html(xhr.responseJSON.name[0]);
                            }
                        } else {
                           $('#msg-'+key).html('xhr.responseText');
                        }
                        var errorLead = $('#errorLead').val();
                        var errorCount = +errorLead + 1;
                                $('#errorLead').val(errorCount);
                                $('#errorLeadText').html(newCount);
                                $('#user-detailsError').append('<li>'+errorCount+'. '+res.dataUser.name+' - '+res.dataUser.country_code+'-'+res.dataUser.phone+' </li>');
                    }
                })

            });
            $("#overlay").fadeIn(300);　
            setTimeout(function(){
                bulkUpload();
            }, 10000);
        
        });

    });

function bulkUpload()
{
     $.ajax({
        url : baseURL + '/admin/data/bank/bulk/upload_data',
        type: 'POST',
        dataType: 'json',
        data: {
                medium: '{{ $camp }}',
            },
        success: function(response)
        {
            $("#overlay").fadeOut(300);
            // setTimeout(function(){
            //     $("#overlay").fadeOut(300);
            //   },5000);
        }
        });
}

// $( document ).ajaxComplete(function() {
//     $.ajax({
//     url : baseURL + '/admin/data/bank/bulk/upload_data',
//     type: 'POST',
//     dataType: 'json',
//     data: {
//             medium: '{{ $camp }}',
//         },
//     success: function(response)
//     {
//     }
//     });

// });


// function bulkUpload()
// {
//     var datauser_name = $("input[name='datauser_name[]']")
//               .map(function(){return $(this).val();}).get();
//               // console.log(datauser_name);debugger;
//   var formData = new FormData;
//   // var name = $(name).val();
//   formData.append('datauser_name', datauser_name);

//   $.ajax({
//     url : baseURL + '/admin/data/bank/bulk/create1',
//     type: 'POST',
//     dataType: 'json',
//     data: formData,
//     cache : false,
//     processData: false,
//     success: function(response)
//     {
//        console.log(response);debugger;
//     }
//     });
// }
</script>
@endsection
