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

   @if(Auth::user()->roles[0]->name == 'Administrator' || Auth::user()->roles[0]->name == 'Manager')
   <li>
   <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
   <div class="timeline-item">
      <!-- History For Admin and Manager Start -->
      <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
      @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
      <div class="box-body">
         <i onclick="edit_history({{$historyItem->call->id}},'{{$historyItem->call->call_type}}','{{ leadStatus($historyItem->call->lead_status) }}','{{ $historyItem->call->note }}');" class="fa fa-pencil edit-btn btn btn-default"></i>
         <div class="col-md-5">
            <label for="">Call By:</label> {{ $historyItem->call->user->name }}<br>
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : 'N/A' }}<br>
            @else
            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration) : 'N/A' }}<br>
            @endif
            <label for="">Source:</label> {{ $historyItem->call->data_medium }} <br>
         </div>
         <div class="col-md-5">
            <label for="">Time:</label> {{ $historyItem->call->created_at->format(config('access.date_time_format')) }}<br>
            <label for="">Call Agenda:</label><span id="call_agenda{{$historyItem->call->id}}"> {{ $historyItem->call->call_type != '' ? ucfirst($historyItem->call->call_type) : 'N/A' }}</span><br>
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Cloud Call Status:</label> {{ $historyItem->call->office_callStatus }}<br>
            @endif
         </div>
         <div class="col-md-2">
            @if($historyItem->call->office_callType != null && $historyItem->call->office_callType != '')
            <button class="btn btn-{{$historyItem->call->office_callType == 'Outgoing' ? 'success' : 'warning'}}"><i class="fa {{$historyItem->call->office_callType == 'Outgoing' ? 'fa-arrow-up' : 'fa-arrow-down'}}" aria-hidden="true"></i> {{$historyItem->call->office_callType}}</button>
            @endif
            @if($historyItem->call->saved == '1')
            <button class="btn pull-right {{leadStatusBtnClass($historyItem->call->lead_status)}}"><span id="call_status_btn{{$historyItem->call->id}}">{{ leadStatus($historyItem->call->lead_status) }}</span></button>
            @else
            <span id="call_status_btn{{$historyItem->call->id}}"><button class="btn pull-right btn-danger">Call Not Saved</button></span>
            @endif
         </div>
         <div class="col-md-12">
            <label for="">Note</label><br>
            <h4><span id="note_{{$historyItem->call->id}}">{{ $historyItem->call->note }}</span></h4>
         </div>
         <div class="col-md-12 mt-5">
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Call Recording:</label><br>
            @if($historyItem->call->call_record_file != null && $historyItem->call->call_record_file != '')
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            @if($historyItem->call->office24by_audioURL != null && $historyItem->call->office24by_audioURL != '')
            <span id="audio_palyer_{{ $historyItem->call->id }}"></span>
            <button onclick="fetchAudio({{ $historyItem->call->id }});" id="fetchAudio_{{ $historyItem->call->id }}" class="btn btn-primary">Fetch audio</button><br>
            <span id="err-audio{{ $historyItem->call->id }}"></span>
            @else    
            <br><span>No audio available</span>
            @endif
            @endif
            @else
            <label for="">Call Recording:</label><br>
            @if($historyItem->call->call_record_file != null)
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            <span>No audio available</span>
            @endif
            @endif
         </div>
         <!-- <i class="fa fa-pencil"></i> -->
      </div>
      <div class="row updateHistory" style="padding: 10px 100px;" id="updateHistory{{ $historyItem->call->id }}"> 
      </div>
      @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
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
            </audio>
            <br><button onclick="fetchAudio({{ $historyItem->call->id }});" class="btn btn-primary">Fetch audio</button>
            <span id="err-audio{{ $historyItem->call->id }}"></span>
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
         <div class="col-md-2">
            <button class="btn pull-right btn-danger">Call Not Attached</button>
         </div>
         <div class="col-md-12">
            <label for="">Source:</label> {{ $historyItem->note->data_medium }} <br>
            <label for="">Note</label><br>
            <h4>{{ $historyItem->note->note }}</h4>
         </div>
      </div>
      @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
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
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
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
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            <span>No audio available</span>
            @endif
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
      <!-- History For Admin and Manager Ends -->
   </div>
   </li>
   @else
   <!-- History For Executive Start -->
   @php $leadStatus = 0; $medium = '0' @endphp
   @if($historyItem->type_id == '4'  && ($historyItem->sub_type == 'call' || $historyItem->sub_type == 'call_record') || $historyItem->sub_type == 'unattached_call' || $historyItem->sub_type == 'note')
   @php 
   $leadStatus =  leadStatus($historyItem->call->lead_status) ; 
   $medium =  $historyItem->call->data_medium ;
   @endphp
   @endif
   @if($medium == 'FBL_REM_MS')
       @if($leadStatus != 'NO ANSWER' || $leadStatus == '')
           @if($historyItem->sub_type != 'assigned')
               <li>
               <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
               <div class="timeline-item">
                  <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
                  @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
                      <div class="box-body">
                         <i onclick="edit_history({{$historyItem->call->id}},'{{$historyItem->call->call_type}}','{{ leadStatus($historyItem->call->lead_status) }}','{{ $historyItem->call->note }}');" class="fa fa-pencil edit-btn btn btn-default"></i>
                        <div class="col-md-5">
                            <label for="">Call By:</label> {{ $historyItem->call->user->name }}<br>
                            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
                            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : 'N/A' }}<br>
                            @else
                            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration) : 'N/A' }}<br>
                            @endif
                            <label for="">Source:</label> {{ $historyItem->call->data_medium }} <br>
                        </div>
                        <div class="col-md-5">
                            <label for="">Time:</label> {{ $historyItem->call->created_at->format(config('access.date_time_format')) }}<br>
                            <label for="">Call Agenda:</label><span id="call_agenda{{$historyItem->call->id}}"> {{ $historyItem->call->call_type != '' ? ucfirst($historyItem->call->call_type) : 'N/A' }}</span><br>
                            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
                            <label for="">Cloud Call Status:</label> {{ $historyItem->call->office_callStatus }}<br>
                            @endif
                        </div>
                         <div class="col-md-2">
                            @if($historyItem->call->office_callType != null && $historyItem->call->office_callType != '')
                            <button class="btn btn-{{$historyItem->call->office_callType == 'Outgoing' ? 'success' : 'warning'}}"><i class="fa {{$historyItem->call->office_callType == 'Outgoing' ? 'fa-arrow-up' : 'fa-arrow-down'}}" aria-hidden="true"></i> {{$historyItem->call->office_callType}}</button>
                            @endif
                            @if($historyItem->call->saved == '1')
                            <button class="btn pull-right {{leadStatusBtnClass($historyItem->call->lead_status)}}"><span id="call_status_btn{{$historyItem->call->id}}">{{ leadStatus($historyItem->call->lead_status) }}</span></button>
                            @else
                            <span id="call_status_btn{{$historyItem->call->id}}"><button class="btn pull-right btn-danger">Call Not Saved</button></span>
                            @endif
                         </div>
                         <div class="col-md-12">
                            <label for="">Note</label><br>
                            <h4><span id="note_{{$historyItem->call->id}}">{{ $historyItem->call->note }}</span></h4>
                         </div>
                         <div class="col-md-12 mt-5">
                            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
                            <label for="">Call Recording:</label><br>
                            @if($historyItem->call->call_record_file != null && $historyItem->call->call_record_file != '')
                            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
                            <br>
                            @else
                            @if($historyItem->call->office24by_audioURL != null && $historyItem->call->office24by_audioURL != '')
                            <span id="audio_palyer_{{ $historyItem->call->id }}"></span>
                            <button onclick="fetchAudio({{ $historyItem->call->id }});" id="fetchAudio_{{ $historyItem->call->id }}" class="btn btn-primary">Fetch audio</button><br>
                            <span id="err-audio{{ $historyItem->call->id }}"></span>
                            @else    
                            <br><span>No audio available</span>
                            @endif
                            @endif
                            @else
                            <label for="">Call Recording:</label><br>
                            @if($historyItem->call->call_record_file != null)
                            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
                            <br>
                            @else
                            <span>No audio available</span>
                            @endif
                            @endif
                         </div>
                         <!-- <i class="fa fa-pencil"></i> -->
                      </div>
                      <div class="row updateHistory" style="padding: 10px 100px;" id="updateHistory{{ $historyItem->call->id }}"> 
                      </div>
                  @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
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
                            </audio>
                            <br><button onclick="fetchAudio({{ $historyItem->call->id }});" class="btn btn-primary">Fetch audio</button>
                            <span id="err-audio{{ $historyItem->call->id }}"></span>
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
                         <div class="col-md-2">
                            <button class="btn pull-right btn-danger">Call Not Attached</button>
                         </div>
                         <div class="col-md-12">
                            <label for="">Source:</label> {{ $historyItem->note->data_medium }} <br>
                            <label for="">Note</label><br>
                            <h4>{{ $historyItem->note->note }}</h4>
                         </div>
                      </div>
                  @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
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
                            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
                            <br>
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
                            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
                            <br>
                            @else
                            <span>No audio available</span>
                            @endif
                            @endif
                         </div>
                      </div>
                  @else
                      @if($historyItem->sub_type != 'assigned')
                          <h3 class="timeline-header no-border">
                             @if($historyItem->icon !='sign-in' && $historyItem->icon !='sign-out' && $historyItem->icon !='download' && $historyItem->type_id != '4') 
                             <strong>{{ $historyItem->user->name }}</strong> 
                             @endif
                             {!! history()->renderDescription($historyItem->text, $historyItem->assets) !!}
                          </h3>
                      
                      @endif
                    @endif
                    @endif
                  <!-- History For Executive Ends -->
               </div>
               </li>
           @endif
        @endif
    @endif

    @if(@$medium != 'FBL_REM_MS')
        <li>
   <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
   <div class="timeline-item">
      <!-- History For Admin and Manager Start -->
      <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
      @if($historyItem->type_id == '4' && $historyItem->sub_type == 'call')
      <div class="box-body">
         <i onclick="edit_history({{$historyItem->call->id}},'{{$historyItem->call->call_type}}','{{ leadStatus($historyItem->call->lead_status) }}','{{ $historyItem->call->note }}');" class="fa fa-pencil edit-btn btn btn-default"></i>
         <div class="col-md-5">
            <label for="">Call By:</label> {{ $historyItem->call->user->name }}<br>
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration, true) : 'N/A' }}<br>
            @else
            <label for="">Call Duration:</label> {{ ($historyItem->call->duration != null)? duration($historyItem->call->duration) : 'N/A' }}<br>
            @endif
            <label for="">Source:</label> {{ $historyItem->call->data_medium }} <br>
         </div>
         <div class="col-md-5">
            <label for="">Time:</label> {{ $historyItem->call->created_at->format(config('access.date_time_format')) }}<br>
            <label for="">Call Agenda:</label><span id="call_agenda{{$historyItem->call->id}}"> {{ $historyItem->call->call_type != '' ? ucfirst($historyItem->call->call_type) : 'N/A' }}</span><br>
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Cloud Call Status:</label> {{ $historyItem->call->office_callStatus }}<br>
            @endif
         </div>
         <div class="col-md-2">
            @if($historyItem->call->office_callType != null && $historyItem->call->office_callType != '')
            <button class="btn btn-{{$historyItem->call->office_callType == 'Outgoing' ? 'success' : 'warning'}}"><i class="fa {{$historyItem->call->office_callType == 'Outgoing' ? 'fa-arrow-up' : 'fa-arrow-down'}}" aria-hidden="true"></i> {{$historyItem->call->office_callType}}</button>
            @endif
            @if($historyItem->call->saved == '1')
            <button class="btn pull-right {{leadStatusBtnClass($historyItem->call->lead_status)}}"><span id="call_status_btn{{$historyItem->call->id}}">{{ leadStatus($historyItem->call->lead_status) }}</span></button>
            @else
            <span id="call_status_btn{{$historyItem->call->id}}"><button class="btn pull-right btn-danger">Call Not Saved</button></span>
            @endif
         </div>
         <div class="col-md-12">
            <label for="">Note</label><br>
            <h4><span id="note_{{$historyItem->call->id}}">{{ $historyItem->call->note }}</span></h4>
         </div>
         <div class="col-md-12 mt-5">
            @if($historyItem->call->office_ref_id != null && $historyItem->call->office_ref_id != '')
            <label for="">Call Recording:</label><br>
            @if($historyItem->call->call_record_file != null && $historyItem->call->call_record_file != '')
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            @if($historyItem->call->office24by_audioURL != null && $historyItem->call->office24by_audioURL != '')
            <span id="audio_palyer_{{ $historyItem->call->id }}"></span>
            <button onclick="fetchAudio({{ $historyItem->call->id }});" id="fetchAudio_{{ $historyItem->call->id }}" class="btn btn-primary">Fetch audio</button><br>
            <span id="err-audio{{ $historyItem->call->id }}"></span>
            @else    
            <br><span>No audio available</span>
            @endif
            @endif
            @else
            <label for="">Call Recording:</label><br>
            @if($historyItem->call->call_record_file != null)
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            <span>No audio available</span>
            @endif
            @endif
         </div>
         <!-- <i class="fa fa-pencil"></i> -->
      </div>
      <div class="row updateHistory" style="padding: 10px 100px;" id="updateHistory{{ $historyItem->call->id }}"> 
      </div>
      @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'unattached_call')
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
            </audio>
            <br><button onclick="fetchAudio({{ $historyItem->call->id }});" class="btn btn-primary">Fetch audio</button>
            <span id="err-audio{{ $historyItem->call->id }}"></span>
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
         <div class="col-md-2">
            <button class="btn pull-right btn-danger">Call Not Attached</button>
         </div>
         <div class="col-md-12">
            <label for="">Source:</label> {{ $historyItem->note->data_medium }} <br>
            <label for="">Note</label><br>
            <h4>{{ $historyItem->note->note }}</h4>
         </div>
      </div>
      @elseif($historyItem->type_id == '4' && $historyItem->sub_type == 'call_record' )
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
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
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
            <audio src="{{ url('storage/call_records/'.$historyItem->call->call_record_file) }}" id="audioFile{{ $historyItem->call->id }}" preload="auto" controls  controlsList="nodownload"></audio>
            <br>
            @else
            <span>No audio available</span>
            @endif
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
      <!-- History For Admin and Manager Ends -->
   </div>
   </li>

    @endif
       <!--timeline-item-->


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
