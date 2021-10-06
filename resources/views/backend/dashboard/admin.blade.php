@extends('backend.layouts.app')

@section('page-header')
    <h1>
        {{ app_name() }}
        <small>{{ trans('strings.backend.dashboard.title') }}</small>
    </h1>
@endsection

@section('content')

    <div class="callout callout-info">
        <h4>Good Morning, Tina</h4>
        <p>Tip of the day to get more sales. Pitch on well.</p>
    </div>

@endsection