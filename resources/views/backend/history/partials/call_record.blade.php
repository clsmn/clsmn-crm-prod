<li>
    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
    <div class="timeline-item">
        <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
        <div class="box-body">
            <div class="col-md-5">
            <label for="">Call By:</label> {{ $historyItem->user->name }}<br>
                @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
                    <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : 'N/A' }}<br>
                @else
                    <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration) : 'N/A' }}<br>
                @endif
                <label for="">Source:</label> {{ $historyItem->call->data_medium }} <br>
            </div>
            <div class="col-md-5">
                <label for="">Time:</label> {{ $historyItem->call_record->created_at->format(config('access.date_time_format')) }}<br>
                 @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
                    <label for="">Call Recording:</label><br>
                    @if($historyItem->call->call_record_file != null || $historyItem->call->call_record_file != '')
                        <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio> <br>
                        
                    @else
                        @if($historyItem->call->office24by_audioURL != null && $historyItem->call->office24by_audioURL != '')
                         <span id="audio_palyer_{{ $historyItem->call->id }}"></span>
                            <button onclick="fetchAudio({{ $historyItem->call->id }});" id="fetchAudio_{{ $historyItem->call->id }}" class="btn btn-primary">Fetch audio</button><br>
                            <span id="err-audio{{ $historyItem->call->id }}"></span>
                        @endif
                            <br><span>No audio available</span>
                    @endif
                @else
                    <label for="">Call Recording:</label><br>
                    @if($historyItem->call->call_record_file != null)
                        <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio> <br>
                    @else
                        <span>No audio available</span>
                    @endif
                @endif
            </div>
           
        </div>
    </div>
</li>