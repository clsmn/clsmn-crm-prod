<ul class="timeline">
    @foreach($history as $historyItem)
        @include('backend.history.partials.item')
    @endforeach
</ul>

@if ($paginate)
    <div class="pull-right">
        {{ $history->links() }}
    </div><!--pull-right-->

    <div class="clearfix"></div>
@endif