@extends('baselayout', ['bodyclass' => 'h-card', 'added_headers' => ['<link rel="alternate" type="application/json+jf2feed" href="/jf2feed">']] )

@section('content')
<div class="postfeed h-feed" id="posts-stream">
    <h3 class="p-name">{{$feed_name}}</h3>
    @foreach($posts as $post)
        @include('posts.mini-default', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
</div>
@stop
