@extends('baselayout', ['bodyclass' => 'h-entry'])

@section('content')
        @include('posts.mini-default', ['post' => $post])
@stop
