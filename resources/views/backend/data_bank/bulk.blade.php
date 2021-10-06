@extends ('backend.layouts.app')

@section ('title', 'Create Bulk Lead')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
    {{ Html::style("plugins/fileuploads/css/dropify.min.css") }}
    {{ Html::style("plugins/EasyAutocomplete/easy-autocomplete.min.css") }}
@endsection

@section('page-header')
    <h1>Bulk Lead</h1>
@endsection

@section('content')

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
                                <th>Name</th>
                                <th>Country Code</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Ad Platform</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($data as $key=>$row)
                            <tr id="frm_{{ $key }}" class="frm_row" data-val="{{ $key }}">
                                <td>
                                    <input type="text" class="form-control name" value="{{ $row['name'] }}">
                                    <span class="name_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control country_code" value="{{ $row['country_code'] }}">
                                    <span class="country_code_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control phone" value="{{ $row['phone'] }}">
                                    <span class="phone_error error"></span>
                                </td>
                                <td>
                                    <input type="text" class="form-control city" value="{{ $row['city'] }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control ad_platform" value="{{ $row['ad_platform'] }}">
                                </td>
                                <td id="msg-{{ $key }}"></td>
                            </tr>
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
                            self.html('<td colspan="6" class="text-center">'+res.data+'</td>');
                            // setTimeout(function(){
                            //     self.remove();
                            // }, 5000);
                        }else{
                            $('#msg-'+key).html(res.data);
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
                    }
                })
            });
        });
    });
    </script>
@endsection
