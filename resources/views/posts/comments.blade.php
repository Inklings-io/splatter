
<div class="comments">
  @foreach ($comments as $comment) 
    <div class="comment u-comment h-cite">
      <div class='comment_header'>
        <span class="minicard h-card u-author">
          <img class='comment_author u-photo' src="{{$comment->author->image ?: '/image/person.png' }}" />
          <a class="p-name u-url" href="{{$comment->author->url ?: $comment->source_url }}" rel="nofollow" title="{{$comment->author->name ?: 'View Author'}}" >
            {{$comment->author->name ?: 'A Reader'}}
          </a>
        </span>

        <a href="{{$comment->source_url}}" class="u-url permalink">
          <time class="date dt-published" datetime="<?php echo $comment['published']?>">
            <?php echo date("F j, Y g:i A", strtotime($comment['published']))?>
          </time>
        </a>
        
        @if($comment->vouch_url)
          <a href="{{$comment->vouch_url}}" class="vouch">Vouched</a>
        @endif

       <span class="other-controls">
          @foreach($comment['actions'] as $actiontype => $action)
            <indie-action do="{{$actiontype}}>" with="{{$comment->permalink}}">
              <a href="{{$action->link}}" title="{{$action->title}}">
                {{$action->icon}}
              </a>
            </indie-action>
          @endforeach
        </span>
      </div>
      <div class='comment_body p-content p-name'>
        {{$comment->content}}
      </div>

      @foreach($comment->comments as $subcomment)
        <div class="subcomment u-comment h-cite">  
          <div class='comment_header'>
            <span class="minicard h-card u-author">
              <img class='comment_author' src="{{ $subcomment->author->image ?: '/image/person.png' }}" />
              <a class="p-name u-url" href="{{$subcomment->author->url ?: $subcomment->source_url}}" rel="nofollow" title="{{$subcomment->author->name ?: 'View Author'}}" >
                {{$subcomment->author->name ?: 'A Reader'}}
              </a>
            </span>

            <a href="{{$subcomment->source_url}}" class="u-url permalink">
              <time class="date dt-published" datetime="{{$subcomment->published}}">
                <?php echo date("F j, Y g:i A", strtotime($subcomment['published']))?>
              </time>
            </a>
          </div>
          <div class='comment_body p-content p-name'>
            {{$subcomment->content}}
          </div>
        </div>
      @endforeach
    </div>
  @endforeach
</div>
