@extends('baselayout', ['bodyclass' => 'h-entry'])

@section('content')
    <div class="postfull">
        @include('posts.full', ['post' => $post])
    </div>
@stop
