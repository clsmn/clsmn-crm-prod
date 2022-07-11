<li>
    <i class="fa fa-{{ $historyItem->icon }} {{ $historyItem->class }}"></i>
    <div class="timeline-item">
        <span class="time" style="margin-top: 20px;"><i class="fa fa-clock-o"></i>{{ $historyItem->created_at->diffForHumans() }} </span>
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
    </div>
</li>