@extends('baselayout', ['bodyclass' => 'h-feed'])

@section('content')
<div class="postfeed" id="posts-stream">
    <h1 class="p-name feed-title">{{$feed_name}}</h1>
    @foreach($posts as $post)
        @include('posts.mini-default', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
</div>
@stop
