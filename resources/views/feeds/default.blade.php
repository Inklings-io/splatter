@extends('baselayout', ['bodyclass' => 'h-feed'])

@section('content')
<div class="postfeed" id="posts-stream">
    <h3 class="p-name">{{$feed_name}}</h3>
    @foreach($posts as $post)
        @include('posts.mini-default', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
</div>
@stop
