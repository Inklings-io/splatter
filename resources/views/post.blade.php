@extends('baselayout', ['bodyclass' => 'h-entry'])

@section('content')
        @include('posts.full', ['post' => $post])
@stop
