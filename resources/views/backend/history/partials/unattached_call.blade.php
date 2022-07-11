<li>
    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
    <div class="timeline-item">
        <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
        <div class="box-body">
            <div class="col-md-5">
            <label for="">Call By:</label> {{ $historyItem->call_record->user->name }}<br>
                <label for="">Call Duration:</label> {{ ($historyItem->call_record->duration != null)? duration($historyItem->call_record->duration) : 'N/A' }}<br>
                <label for="">Source:</label> {{ $historyItem->call->data_medium }} <br>
            </div>
            <div class="col-md-5">
                <label for="">Time:</label> {{ $historyItem->call_record->created_at->format(config('access.date_time_format')) }}<br>
                <label for="">Call Agenda:</label> {{ $historyItem->call->call_type != '' ? ucfirst($historyItem->call->call_type) : 'N/A' }}<br>
                @if($historyItem->call_record->acrfilename != null || $historyItem->call->office_ref_id != '' || $historyItem->call->office24by_audioURL != '')
                    <audio src="{{ url('storage/call_records/'.$historyItem->call_record->acrfilename) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload">
                    </audio><br><button onclick="fetchAudio({{ $historyItem->call->id }});" class="btn btn-primary">Fetch audio</button>
                        <span id="err-audio{{ $historyItem->call->id }}"></span>
                @else
                    <span>No audio available</span>
                @endif
            </div>
            <div class="col-md-2">
                <button class="btn pull-right btn-danger">Call Not Attached</button>
            </div>
        </div>
    </div>
</li>