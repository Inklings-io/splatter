@extends('baselayout', ['bodyclass' => 'h-card', 'added_headers' => ['<link rel="alternate" type="application/jf2feed+json" href="/jf2feed">', '<link rel="alternate" type="text/feed+x-yaml" href="/yamlfeed">']] )

@section('content')
<div class="postfeed h-feed" id="posts-stream">
    <h3 class="p-name">{{$feed_name}}</h3>
    @foreach($posts as $post)
        @include('posts.mini-default', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
</div>
@stop
