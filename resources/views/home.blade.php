@extends('baselayout', ['bodyclass' => 'h-card'])

@section('content')
<div class="postfeed h-feed" id="posts-stream">
    @foreach($posts as $post)
        @include('posts.mini-default', ['post' => $post])
    @endforeach
    {{ $posts->links() }}
</div>
@stop
