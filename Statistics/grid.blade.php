@extends('layouts.default')

@section('content')
    @if (is_null($tables) == false)
        @include('stats.general_stat')
    @endif
    @if (is_null($term_group) == false)
        @include('stats.term_group_stat')
    @endif
@stop