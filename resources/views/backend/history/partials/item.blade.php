<li>
    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>

    <div class="timeline-item">
        <span class="time"><i class="fa fa-clock-o"></i> {{ $historyItem->created_at->diffForHumans() }}</span>

        @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
            <div class="box-body">
                <div class="col-md-5">
                    <label for="">Call By:</label> {{ $historyItem->call->user->name }}<br>
                    @if($historyItem->call->exotel_sid != null && $historyItem->call->exotel_sid != '')
                        <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : '' }}<br>
                    @else
                        <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration) : '' }}<br>
                    @endif
                    <label for="">Listen Call:</label>
                    <br> <label for="">Source:</label> {{ $historyItem->call->data_medium }}<br>
                </div>
                <div class="col-md-5">
                    <label for="">Time:</label> {{ $historyItem->call->created_at->format(config('access.date_time_format')) }}<br>
                    <label for="">Call Agenda:</label> {{ ucfirst($historyItem->call->call_type) }}<br>
                    @if($historyItem->call->exotel_sid != null && $historyItem->call->exotel_sid != '')
                        <label for="">Cloud Call Status:</label> {{ $historyItem->call->exotel_call_status }}<br>
                        @if($historyItem->call->call_record_file != null)
                            <audio src="{{ $historyItem->call->call_record_file }}" preload="auto" controls  controlsList="nodownload"></audio>
                        @else
                            @if($historyItem->call->exotel_call_status != 'completed')
                                <a href="#" class="fetchCloudCall" data-val="{{ $historyItem->call->id }}" data-lead="{{ $historyItem->call->lead_id }}">Fetch Cloud Call Status</a>
                            @else
                                <span>No audio available</span>
                            @endif
                        @endif
                    @else
                        @if($historyItem->call->call_record_file != null)
                            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" preload="auto" controls  controlsList="nodownload"></audio>
                        @else
                            <span>No audio available</span>
                        @endif
                    @endif
                </div>
                <div class="col-md-2">
                @if($historyItem->call->saved == '1')
                    <button class="btn pull-right {{leadStatusBtnClass($historyItem->call->lead_status)}}">{{ leadStatus($historyItem->call->lead_status) }}</button>
                @else
                    <button class="btn pull-right btn-danger">Call Not Saved</button>
                @endif
                </div>
                <div class="col-md-12">
                    <label for="">Note</label><br>
                    <h4>{{ $historyItem->call->note }}</h4>
                </div>
            </div>
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
            <div class="box-body">
                <div class="col-md-5">
                <label for="">Call By:</label> {{ $historyItem->call_record->user->name }}<br>
                    <label for="">Call Duration:</label> {{ ($historyItem->call_record->duration != null)? duration($historyItem->call_record->duration) : '' }}<br>
                    <label for="">Listen Call:</label>
                    <br> <label for="">Source:</label> {{ $historyItem->call->data_medium }}<br>
                </div>
                <div class="col-md-5">
                    <label for="">Time:</label> {{ $historyItem->call_record->created_at->format(config('access.date_time_format')) }}<br>
                    <label for="">Call Agenda:</label> <br>
                    @if($historyItem->call_record->acrfilename != null)
                        <audio src="{{ url('storage/call_records/'.$historyItem->call_record->acrfilename) }}" preload="auto" controls  controlsList="nodownload">
                        </audio>
                    @else
                        <span>No audio available</span>
                    @endif
                </div>
                <div class="col-md-2">
                    <button class="btn pull-right btn-danger">Call Not Attached</button>
                </div>
            </div>
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'note')
            <div class="box-body">
                <div class="col-md-5">
                    <label for="">Added By:</label> {{ $historyItem->note->user->name }}<br>
                </div>
                <div class="col-md-5">
                    <label for="">Added At:</label> {{ $historyItem->note->created_at->format(config('access.date_time_format')) }}<br>
                </div>
                <div class="col-md-12">
                    <label for="">Note</label><br>
                    <h4>{{ $historyItem->note->note }}</h4>
                    <br> <label for="">Source:</label> <br>
                </div>
            </div>
        @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record')
            <div class="box-body">
                <div class="col-md-5">
                <label for="">Call By:</label> {{ $historyItem->user->name }}<br>
                    <label for="">Call Duration:</label> {{ ($historyItem->call_record->duration != null)? duration($historyItem->call_record->duration) : '' }}<br>
                    <label for="">Listen Call:</label>
                    <br> <label for="">Source:</label> {{ $historyItem->call->data_medium }}<br>
                </div>
                <div class="col-md-5">
                    <label for="">Time:</label> {{ $historyItem->call_record->created_at->format(config('access.date_time_format')) }}<br>
                    @if($historyItem->call_record->acrfilename != null)
                        <audio src="{{ url('storage/call_records/'.$historyItem->call_record->acrfilename) }}" preload="auto" controls  controlsList="nodownload">
                        </audio>
                    @else
                        <span>No audio available</span>
                    @endif
                </div>
            </div>
        @else
        <h3 class="timeline-header no-border">
            @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
            <strong>{{ $historyItem->user->name }}</strong> 
            @endif
            {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
        </h3>
        @endif
    </div><!--timeline-item-->
</li>