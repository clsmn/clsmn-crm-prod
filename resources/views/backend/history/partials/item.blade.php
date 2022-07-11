
   <style type="text/css">
    .edit-btn
    {
        float: right;
        position: absolute;
        right: 5px;
        top: 5px;
        font-size: 10px;
        padding: 5px;
    }
    .class-btn-history
    {
        width: 50%;
        margin: 0 auto;
        display: block;
        margin-top: 20px;
    }
</style>
@php 
    $user_1 = json_decode($historyItem->assets);
        if(@$user_1->user_string)
        {
            $user_ = $user_1->user_string;
        }
        else
        {
            $user_ = "";
        }
    $user_current = Auth::user()->name;


@endphp



@if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
    @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
       @include('backend.history.partials.type_call')
    @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
       @include('backend.history.partials.unattached_call')
    @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
       @include('backend.history.partials.note')
    @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
       @include('backend.history.partials.call_record')
    @else
        @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
        <strong>{{ $historyItem->user->name }}</strong> 
        @endif
       
        @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
        <li>
            <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
            <div class="timeline-item">
                <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                <h3 class="timeline-header no-border">
                    {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                </h3>
            </div>
        </li>
        @else
            @if($user_ == $user_current)
            <li>
                <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                <div class="timeline-item">
                    <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                    <h3 class="timeline-header no-border">
                        {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                    </h3>
                </div>
            </li>
            @endif
        @endif
    @endif
@else

    @if($source->data_medium == 'FBL_REM_MS')
        @if($total_history_count == $total_history_no_answer_count)

            @if($user_ == $user_current)
                    @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
                    <strong>{{ $historyItem->user->name }}</strong> 
                    @endif
                   
                    @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                    <li>
                        <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                        <div class="timeline-item">
                            <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                            <h3 class="timeline-header no-border">
                                {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                            </h3>
                        </div>
                    </li>
                    @else
                        @if($user_ == $user_current)
                            @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
                               @include('backend.history.partials.type_call')
                            @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
                               @include('backend.history.partials.unattached_call')
                            @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
                               @include('backend.history.partials.note')
                            @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
                               @include('backend.history.partials.call_record')
                            @else
                                @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
                                <strong>{{ $historyItem->user->name }}</strong> 
                                @endif
                               
                                @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                                <li>
                                    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                                    <div class="timeline-item">
                                        <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                                        <h3 class="timeline-header no-border">
                                            {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                                        </h3>
                                    </div>
                                </li>
                                @else
                                    @if($user_ == $user_current)
                                    <li>
                                        <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                                        <div class="timeline-item">
                                            <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                                            <h3 class="timeline-header no-border">
                                                {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                                            </h3>
                                        </div>
                                    </li>
                                    @endif
                                @endif
                            @endif
                        @else
                            <li>
                                <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                                <div class="timeline-item">
                                    <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                                    <h3 class="timeline-header no-border">
                                        {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                                    </h3>
                                </div>
                            </li>
                        @endif
                    @endif
            @endif
        @else
           
            @if(Auth::user()->id == $historyItem->user->id)
                @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
                   @include('backend.history.partials.type_call')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
                   @include('backend.history.partials.unattached_call')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
                   @include('backend.history.partials.note')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
                   @include('backend.history.partials.call_record')
                @else
                    @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
                    <strong>{{ $historyItem->user->name }}</strong> 
                    @endif
                   
                    @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                    <li>
                        <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                        <div class="timeline-item">
                            <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                            <h3 class="timeline-header no-border">
                                {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                            </h3>
                        </div>
                    </li>
                    @else
                        @if($user_ == $user_current)
                        <li>
                            <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                            <div class="timeline-item">
                                <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                                <h3 class="timeline-header no-border">
                                    {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                                </h3>
                            </div>
                        </li>
                        @endif
                    @endif
                @endif
            @else
                @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
                   @include('backend.history.partials.type_call')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
                   @include('backend.history.partials.unattached_call')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
                   @include('backend.history.partials.note')
                @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
                   @include('backend.history.partials.call_record')
                @else
                    @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
                    <strong>{{ $historyItem->user->name }}</strong> 
                    @endif
                   
                    @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
                    <li>
                        <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                        <div class="timeline-item">
                            <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                            <h3 class="timeline-header no-border">
                                {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                            </h3>
                        </div>
                    </li>
                    @else
                        @if($user_ == $user_current)
                        <li>
                            <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                            <div class="timeline-item">
                                <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                                <h3 class="timeline-header no-border">
                                    {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                                </h3>
                            </div>
                        </li>
                        @endif
                    @endif
                @endif
            @endif
        @endif
    @endif
    @if($source->data_medium != 'FBL_REM_MS')
        @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
           @include('backend.history.partials.type_call')
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
           @include('backend.history.partials.unattached_call')
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
           @include('backend.history.partials.note')
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
           @include('backend.history.partials.call_record')
        @else
            @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
            <strong>{{ $historyItem->user->name }}</strong> 
            @endif
          
            @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
            <li>
                <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                <div class="timeline-item">
                    <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                    <h3 class="timeline-header no-border">
                        {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                    </h3>
                </div>
            </li>
            @else
                @if($user_ == $user_current)
                <li>
                    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
                    <div class="timeline-item">
                        <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                        <h3 class="timeline-header no-border">
                            {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                        </h3>
                    </div>
                </li>
                @endif
            @endif
        @endif          
    @endif
@endif
<script type="text/javascript">
    function fetchAudio(id)
    {
        $.ajax({
                url : baseURL + '/api/acr/getaudio/',
                type : 'get',
                'data' : 'id='+id,
                success: function(response)
                {
                   if(response.status == 200)
                   {

                       $('#audio_palyer_'+id).append('<audio src="'+response.data+'" id="audioFile'+id+'" preload="auto" controls  controlsList="nodownload"></audio> <br>')
                       $( "#fetchAudio_"+id ).remove();
                   }
                   else
                   {
                    $('#err-audio'+id).html('<p class="text-danger">'+response.data+'</p>');
                   }
                }
            });
    }

    function edit_history(id,agenda,lead,note)
    {
        $('.updateHistory').html('');
        var html ='<h4 class="text-center">Update Call History</h4>';
        html +='<hr class="text-center" style="width: 10%; border-top: 1px solid #000;">';
        html +='<div class="col-md-6">';
        html +='<label for="call_agenda" class="control-label">Call Agenda</label> ';
        html +='<select class="form-control" name="call_agenda" id="call_agenda">';
        html +='<option value="training" '+(agenda == "training" ? 'selected' : '')+'>TRAINING</option>';
        html +='<option value="sale" '+(agenda == "sale" ? 'selected' : '')+'  >Sales</option>';
        html +='</select>';
        html +='</div>';
        html +='<div class="col-md-6">';
        html +='<label for="lead_status" class="control-label">Lead Status</label> ';
        html +='<select class="form-control" name="lead_status" id="lead_status">';
        html +='<option value="sale"' +(lead == "SALE" ? 'selected' : '')+'>SALE</option>';
        html +='<option value="hot"' +(lead == "HOT" ? 'selected' : '')+'>HOT</option>';
        html +='<option value="mild"' +(lead == "MILD" ? 'selected' : '')+'>MILD</option>';
        html +='<option value="cold"' +(lead == "COLD" ? 'selected' : '')+'>COLD</option>';
        html +='<option value="no_answer"' +(lead == "NO ANSWER" ? 'selected' : '')+'>NO ANSWER</option>';
        html +='<option value="busy"' +(lead == "BUSY" ? 'selected' : '')+'>BUSY</option>';
        html +='<option value="not_interested"' +(lead == "NOT INTERESTED" ? 'selected' : '')+'>NOT INTERESTED</option>';
        html +='<option value="dead"' +(lead == "DEAD" ? 'selected' : '')+'>DEAD</option>';
        html +='</select>';
        html +='</div>';

        html +='<div class="col-md-12">';
        html +='<label for="note" class="control-label">Notes</label>';
        html +='<textarea id="note_update" cols="30" rows="2" class="form-control">'+note+'</textarea>';
        html +='<input name="history_id" id="history_id" type="hidden" value="'+id+'"> ';
        html +='</div>';
        html +='<div class="col-md-6 text-center mt-5">';
        html +='<button onclick="historyUpdate()" class="btn btn-success btn-block class-btn-history" >Update</button>';
        html +='</div>';
        html +='<div class="col-md-6 text-center mt-5">';
        html +='<button onclick="cancelUpdate()" class="btn btn-warning btn-block class-btn-history" >Cancel</button>';
        html +='</div>';
        $('#updateHistory'+id).html(html);
    }

    function historyUpdate()
    {
        // $('.updateHistory').html('');
        var call_agenda = $('#call_agenda').val();
        var lead_status = $('#lead_status').val();
        var note_update = $('#note_update').val();
        var history_id = $('#history_id').val();
        $.ajax({
            url : baseURL + '/api/acr/updateCallHistory',
            type : 'post',
            'data' : 'history_id='+history_id+'&call_agenda='+call_agenda+'&lead_status='+lead_status+'&note_update='+note_update,
            success: function(response)
            {
                if(response.status == 200)
                {
                    $('#call_agenda'+history_id).text(call_agenda.charAt(0).toUpperCase() + call_agenda.slice(1));
                    $('#call_status_btn'+history_id).text(lead_status.toUpperCase());
                    $('#note_'+history_id).text(note_update);
                    $('.updateHistory').html('');
                }
            }
        });
    }

    function cancelUpdate()
    {
        $('.updateHistory').html('');
    }
</script>