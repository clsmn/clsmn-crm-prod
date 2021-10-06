@extends ('backend.layouts.app')

@section ('title', 'Create Lead')

@section('after-styles')
    {{ Html::style("css/backend/plugin/datepicker/datepicker.css") }}
@endsection

@section('page-header')
    <h1>
        Create Lead
    </h1>
@endsection

@section('content')
    {{ Form::open(['route' => 'admin.data.bank.store', 'class' => 'form-horizontal', 'role' => 'form', 'method' => 'post']) }}
        {{ csrf_field() }}
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Lead</h3>
            </div><!-- /.box-header -->

            <div class="box-body">
                <div class="form-group">
                    {{ Form::label('name', 'Name', ['class' => 'col-lg-2 control-label']) }}

                    <div class="col-lg-10">
                        {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']) }}
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="form-group">
                    {{ Form::label('country_code', 'Country Code', ['class' => 'col-lg-2 control-label']) }}

                    <div class="col-lg-10">
                        {{ Form::text('country_code', null, ['class' => 'form-control', 'placeholder' => 'Country Code']) }}
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="form-group">
                    {{ Form::label('phone', 'Phone', ['class' => 'col-lg-2 control-label']) }}

                    <div class="col-lg-10">
                        {{ Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Phone']) }}
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="form-group">
                    {{ Form::label('address', 'Address', ['class' => 'col-lg-2 control-label']) }}

                    <div class="col-lg-10">
                        {{ Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Address', 'id' => 'txtUserAddress']) }}
                        <input type="hidden" id="txtUserLatLng" name="txtUserLatLng" value="{{ old('txtUserLatLng') }}">
                        <input type="hidden" id="txtUserLocality" name="txtUserLocality" value="{{ old('txtUserLocality') }}">
                        <input type="hidden" id="txtUserCity" name="txtUserCity" value="{{ old('txtUserCity') }}">
                        <input type="hidden" id="txtUserState" name="txtUserState" value="{{ old('txtUserState') }}">
                        <input type="hidden" id="txtUserCountry" name="txtUserCountry" value="{{ old('txtUserCountry') }}">
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="form-group">
                    {{ Form::label('medium', 'Source', ['class' => 'col-lg-2 control-label']) }}

                    <div class="col-lg-10">
                        {{ Form::text('medium', 'Manual', ['class' => 'form-control', 'placeholder' => 'source', 'id' => 'txtUserSource']) }}
                    </div><!--col-lg-10-->
                </div><!--form control-->

                <div class="assign-fields">
                    <div class="form-group">
                        {{ Form::label('call_date', 'Call Date', ['class' => 'col-lg-2 control-label']) }}

                        <div class="col-lg-10">
                            {{ Form::text('call_date', null, ['class' => 'form-control', 'placeholder' => 'Call Date', 'id' => "call_date"]) }}
                        </div><!--col-lg-10-->
                    </div><!--form control-->

                    <div class="form-group">
                        {{ Form::label('lead_status', 'Lead Status', ['class' => 'col-lg-2 control-label']) }}

                        <div class="col-lg-10">
                            <select name="lead_status" id="lead_status" class="form-control">
                                <option value="open">Open</option>
                                <option value="hot">Hot</option>
                                <option value="mild">Mild</option>
                                <option value="cold">Cold</option>
                            </select>
                        </div><!--col-lg-10-->
                    </div><!--form control-->

                    <div class="form-group">
                        {{ Form::label('assigned_to', 'Assigned To', ['class' => 'col-lg-2 control-label']) }}

                        <div class="col-lg-10">
                            <select name="assigned_to" id="assigned_to" class="form-control">
                                @foreach($executives as $key => $executive)
                                    <option value="{{ $key }}" {!! (old('assigned_to')==$key)? 'selected="selected"':'' !!}>
                                        {{ $executive }}
                                    </option>
                                @endforeach
                            </select>
                        </div><!--col-lg-10-->
                    </div><!--form control-->
                </div>
            </div><!-- /.box-body -->
        </div><!--box-->

        <div class="box box-info">
            <div class="box-body">
                <div class="pull-right">
                    <input type="hidden" name="assign" value="1">
                    {{ Form::submit(trans('buttons.general.crud.create'), ['class' => 'btn btn-success btn-xs']) }}
                </div><!--pull-right-->

                <div class="clearfix"></div>
            </div><!-- /.box-body -->
        </div><!--box-->

    {{ Form::close() }}
@endsection

@section('after-scripts')
    {{ Html::script('js/backend/access/users/script.js') }}
    <script  src="https://maps.googleapis.com/maps/api/js?libraries=places&amp;key={{ env('GOOGLE_MAP_KEY') }}"></script>
    {{ Html::script("js/backend/plugin/geocomplete/jquery.geocomplete.js") }}
    {{ Html::script("js/backend/plugin/datepicker/bootstrap-datepicker.js") }}
    <script>
    $(function() {

        $('#call_date').datepicker({
            startDate: 'today',
            format : 'dd/mm/yyyy'
        });

        //Add address
        $.fn.addGeolocation = function () { 
            this.geocomplete().bind("geocode:result", function(event, result){
                $("#txtUserCountry").val('');
                $("#txtUserState").val('');
                $("#txtUserCity").val('');
                $("#txtUserLocality").val('');
                $("#txtUserLatLng").val('');
                $("#txtUserAddress").val(result.formatted_address);
                var adrCom = JSON.stringify(result.address_components);
                var addressComponent = result.address_components;
                var userLocality = "";
                var userCity = "";
                var userState = "";
                var userCountry = "";
                for (let index = 0; index < addressComponent.length; index++) 
                {
                    if(addressComponent[index].types[0]=="country")
                    {
                        userCountry = addressComponent[index].long_name;
                    }
                    if(addressComponent[index].types[0]=="administrative_area_level_1")
                    {
                        userState = addressComponent[index].long_name;
                    }
                    if(addressComponent[index].types[0]=="locality")
                    {
                        userCity = addressComponent[index].long_name;
                    }
                    if(addressComponent[index].types[0]=="sublocality_level_1")
                    {
                        userLocality = addressComponent[index].long_name;
                    }
                }
                if(userCity!="" && userState!="" && userCountry!="")
                {
                    $("#txtUserCountry").val(userCountry);
                    $("#txtUserState").val(userState);
                    $("#txtUserCity").val(userCity);
                    $("#txtUserLocality").val(userLocality);
                    $("#txtUserLatLng").val(result.geometry.location.lat()+","+result.geometry.location.lng());
                }else
                {
                    alert("Please select a valid address.");
                }
            })
            .bind("geocode:error", function(event, status){
                $("#txtUserAddress").val("ERROR: " + status);
            })
            .bind("geocode:multiple", function(event, results){
                $("#txtUserAddress").val("Multiple: " + results.length + " results found");
            });
        }

        $('#txtUserAddress').addGeolocation();
    });
    </script>
@endsection
