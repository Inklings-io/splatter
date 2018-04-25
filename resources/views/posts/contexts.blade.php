
<div id="contexts" class="contexts">
  @foreach($contexts as $context)
    <div class="context postbox h-cite">
      <div class='context_header'>
        <span class="minicard h-card u-author">
          <img class='context_author u-photo' src="{{$context->author->image ?: '/image/person.png'}}" />
          <a class="p-name u-url" href="{{$context->author->url ?: $context->url}}" rel="nofollow" title="{{$context->author->name ?: 'View Author'}}" >
            {{$context->author->name ?: 'someone'}}
          </a>
        </span>

        <a href="{{$context->url}}" class="u-url permalink">
          <time class="date dt-published" datetime="{{$context->published}}">{{date("F j, Y g:i A", strtotime($context->published)) }}</time>
        </a>
        @if(!empty($context->vouch_url) )
          <a href="{{$context->vouch_url}}" class="vouch">Vouched</a>
        @endif
<!-- todo: indieactions -->
      </div>
      <div class='context_body p-content p-name'>
        {{$context->content}}
      </div>

        @if( $context->syndications->count() > 0)
          <div class="syndications">
            @foreach($context->syndications as $elsewhere)
              @if(isset($elsewhere->site))
                <a class="u-syndication" href="{{$elsewhere->url}}" ><img style='height:20px;width:20px;' src="{{$elsewhere->site->image}}" title="{{$elsewhere->site->name}}" /></a>
              @else
                <a class="u-syndication" href="{{$elsewhere->url}}" ><i class="fa fa-link"></i></a>
              @endif
            @endforeach
          </div>
        @endif

    </div>
  @endforeach
</div>
