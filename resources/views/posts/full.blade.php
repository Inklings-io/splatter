<!-- todo: add contexts -->


<article id="post-{{$post->id}}" class="container-fluid postbox {{$post->type}} {{ $post->draft == 1 ? 'draft':'' }} {{$post->deleted_at == 1 ? 'deleted':''}} " >

  <header class="row">
    <span class="col-lg-6 postauthor  p-author h-card">
      <img class="u-photo" alt='Post by' src='{{$author['image']}}' height='40' width='40' />
      <a class="u-url p-name" href="{{$author['url']}}" title="{{$author['name']}}">
        {{$author['name']}}
      </a>
    </span>

    <span class="col-lg-2 permalink">
      <a class="u-url" href="{{$post->permalink}}" title="Permalink to <?php echo $post['name']?>" >
        <i class="fa fa-link"></i> 
      </a>
    </span>

    <span class="col-lg-2">      
      <a class="u-url" href="{{$post->permalink}}" title="<?php echo date("g:i A", strtotime($post['published']))?>">
        <time class="dt-published" datetime="<?php echo date("c", strtotime($post->published))?>" >
          <?php echo date("F j, Y", strtotime($post->published))?>
        </time>
      </a>
    </span>

    @if($post['in-reply-to'])
      <span class="col-lg-2">      
        In Reply To <a class="u-in-reply-to" href="<?php echo $post['in-reply-to']?>">This</a>
      </span>
    @endif

    <div class="col-lg-6">      
      @if($post['type'] != 'listen')
        <h1 class="p-name">
          <a class="u-url" href="{{$post->permalink}}" title="Permalink to <?php echo $post['name']?>" >
            <?php echo $post['name']?>
          </a>
        </h1>
      @endif
    </div>

    <div class="col-lg-6">      
      @if($post['type'] == 'snark')
        <h3>Sarcasm Alert</h3>
      @endif
    </div>

  </header><!-- .entry-header -->



  <div class="row {{$post['summary_html'] ? 'p-summary' : 'e-content'}}">
    @if($post->weight)
     <h2 class="col-sm-12 h-measure p-weight">
       Weight: <data class="p-num" value="{{$post->weight->num}}"> {{$post->weight->num}} </data>
       <data class="p-unit" value="{{$post->weight->unit}}">{{$post->weight->unit}}</data>
     </h2>
    @endif

    @if(isset($post['bookmark-of']) and !empty($post['bookmark-of']))
      <div class="col-sm-12">
        <i class="fa fa-bookmark-o"></i> 
        <a class="u-bookmark-of" href="{{$post['bookmark-of']}}">
          @if(isset($post['name']) and !empty($post['name']))
            {{$post['name']}}
          @else
            {{$post['bookmark-of']}}
          @endif
        </a>
      </div>
    @endif

    @if(isset($post['like-of']) and !empty($post['like-of'])) 
      <div class="col-sm-12">
        <i class="fa fa-heart-o"></i>
        <a class="u-like-of" href="<?php echo $post['like-of']?>">
          <?php echo htmlentities($post['like-of']);?>
        </a>
      </div>
    @endif

    @foreach($post->media as $media)
      <div class="col-sm-12">
        @if($media->type == 'photo')
          <img src="{{$media->path}}" class="u-photo" alt="{{$media->alt}}"/><br>
        @else
          <a href="{{$media->path}}">{{$media->type}}</a>
        @endif
      </div>
    @endforeach

    @if($post['type'] == 'listen')
      <div class="col-sm-12">
        I listend To <span class="song-title">{{$post['name']}}</span>
        by <span class="song-artist">{{$post['artist']}}</span>
      </div>
    @endif
        
    @if(isset($post['rsvp']) and !empty($post['rsvp'])) 
      <div class="col-sm-12">
        <i class="fa fa-calendar"></i>
        <a class="" href="<?php echo $post['in-reply-to']?>">Event</a>
        <i class="fa fa-envelope-o"></i>
        <data class="p-rsvp" value="<?php echo $post['rsvp']?>">
          <?php echo (strtolower($post['rsvp']) == 'yes' ? 'Attending' : 'Not Attending' );?>
        </data>
      </div>
    @endif

    <div class="col-sm-12">
      @if(isset($post->summary) and $post->summary)
        {!!html_entity_decode($post->summary)!!}
      @else
        {!!html_entity_decode($post->content)!!}
      @endif
    </div>

    @if(isset($post['place_name']) and !empty($post['place_name']))
      <div class="col-sm-12">
        Checked In At {{$post['place_name']}}
      </div>
    @endif

    @if(isset($post->location) and !empty($post->location))
      <div class="col-sm-12">
        @if(isset($post->location->longitude))
          <img id="map" style="width: 200px; height: 200px" src="//maps.googleapis.com/maps/api/staticmap?zoom=13&size=200x200&maptype=roadmap&markers=size:mid%7Ccolor:blue%7C{{$post->location->latitude}},{{$post->location->longitude}}"/>
        @endif

        @if(isset($post->location->name))
          at {{$post->location->name}}
        @endif
      </div>
    @endif

  </div><!-- .entry-content -->


  <footer class="row entry-meta">

    @if(!empty($post->reacjis) )
      <span id="general-reacjis">
        @foreach($post->reacjis as $content => $reacjigroup)
          <span class="reacji-container">
            <span class="reacji">{{$content}}</span>
            <span class="reacji-count">{{$reacjigroup->count()}}</span>
            <span class="reacji-sources">
              @foreach($reacjigroup as $reacji)
                <div class="h-cite u-comment">
                  <time class="date dt-published" style="display:none" datetime="{{$reacji->published}}">{{date("Y-m-d", strtotime($reacji->published))}}</time></a>
                  <span class="h-card u-author">
                    <a class="u-url" href="{{$reacji->author->url ?: $reacji->url}}" rel="nofollow" title="View Profile">
                      <img class='comment_author u-photo' src="{{$reacji->author->image ?: '/image/person.png'}}" />
                      <span class="p-name" style="display:none">{{$reacji->author->name ?: 'someone'}}</span>
                    </a>
                  </span>
                  <a href="{{$reacji->url}}" class="u-url permalink" title="{{date("Y-m-d", strtotime($reacji->published)) }}">{{$reacji->author->name ?: 'someone'}}</a>
                  <div class='p-content p-name' style="display:none">{{$reacji->content}}</div>
                </div>
              @endforeach
            </span>
          </span>
          <span class="sep"> | </span>
        @endforeach
      </span>
    @endif


    @if($post->comments->count() > 0) 
      <span class="comments-link"><a href="{{$post->permalink}}#comments" title="Comments for <?php echo $post['name']?>"><i class="fa fa-comment-o"></i> {{$post->comments->count()}}</a></span>
      <span class="sep"> | </span>
    @endif

    @if($post->reposts->count() > 0)
      <span class="repost-container">
        <span class="repost"><i class="fa fa-retweet"></i></span>
        <span class="repost-count">{{$post->resposts->count()}}</span>
        <span class="repost-sources">
          @foreach($reposts as $repost)
            <div class="h-cite u-repost">
              <time class="date dt-published" style="display:none" datetime="{{$repost->published}}">{{date("Y-m-d", strtotime($repost->published))}}</time></a>
              <span class="h-card u-author">
                <a class="u-url" href="{{$repost->author->url ?: $repost->url}}" rel="nofollow" title="View Profile">
                  <img class='comment_author u-photo' src="{{$repost->author->image ?: '/image/person.png'}}" />
                  <span class="p-name" style="display:none">{{$repost->author->name ?: 'someone'}}</span>
                </a>
              </span>
              <a href="{{$repost->url}}" class="u-url permalink" title="{{date("Y-m-d", strtotime($repost->published)) }}">{{$repost->author->name ?: 'someone'}}</a>
            </div>
          @endforeach
        </span>
      </span>
      <span class="sep"> | </span>
    @endif

    @if($post['categories'])
      @foreach($post['categories'] as $category)
        @if(isset($category['person_name']))
          <span class="category-link">
            <a class="u-category h-card" href="<?php echo $category['url']?>" title="<?php echo $category['url']?>">
              {{$category['person_name']}}
            </a>
          </span>
        @else
          <span class="category-link">
            <a class="u-category" href="<?php echo $category['permalink']?>" title="<?php echo $category['name']?>">
              {{$category['name']}}
            </a>
          </span>
        @endif
      @endforeach
    @endif

    @if(!empty($post['syndications']))
      <div class="syndications">
        @foreach($post['syndications'] as $elsewhere)
          @if(isset($elsewhere['image']))
            <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" >
              <img src="<?php echo $elsewhere['image']?>" title="<?php echo $elsewhere['site_name']?>" />
            </a>
          @else
            <a class="u-syndication" href="<?php echo $elsewhere['syndication_url']?>" >
              <i class="fa fa-link"></i>
            </a>
          @endif
        @endforeach
      </div>
    @endif

    <div class="admin-controls">
      @if (isset($post->actions))
        @foreach($post['actions'] as $actiontype => $action)
          <indie-action do="<?php echo $actiontype?>" with="{{$post->permalink}}">
            <a href="<?php echo $action['link'] ?>" title="<?php echo $action['title']?>"><?php echo $action['icon']?></a>
          </indie-action>
        @endforeach
      @endif
    </div>

  </footer>

</article>

@if(!empty($post->comments) )
  <div id="comments" class="comments">

    @foreach($post->comments as $comment)

        <div class="comment u-comment h-cite">

            <div class='comment_header'>
                <span class="minicard h-card u-author">
                    <img class='comment_author u-photo' src="{{$comment->author->image ?: '/image/person.png'}}" />
                    <span class="p-name u-url" href="{{$comment->author->url ?: $comment->url}}" rel="nofollow" title="{{$comment->author->name ?: 'View Author'}}" >
                        {{$comment->author->name ?: 'someone'}}
                    </span>
                </span>
                <a href="{{$comment->url}}" class="u-url permalink">
                    <time class="date dt-published" datetime="{{$comment->published}}">{{date("F j, Y g:i A", strtotime($comment->published)) }}</time>
                </a>
                @if(!empty($comment->vouch_url) )
                    <a href="{{$comment->vouch_url}}" class="vouch">Vouched</a>
                @endif
<!-- todo: indieactions -->
            </div>
            <div class='comment_body p-content p-name'>
                {{$comment->content}}
            </div>

        </div>
        @if(!empty($comment->comments) )
            @foreach($comment->comments as $subcomment)
                <div class="subcomment u-comment h-cite">
                    <div class='comment_header'>
                        <span class="minicard h-card u-author">
                            <img class='comment_author u-photo' src="{{$subcomment->author->image ?: '/image/person.png'}}" />
                            <span class="p-name u-url" href="{{$subcomment->author->url ?: $subcomment->url}}" rel="nofollow" title="{{$subcomment->author->name ?: 'View Author'}}" >
                                {{$subcomment->author->name ?: 'someone'}}
                            </span>

                        </span>
                        <a href="{{$subcomment->url}}" class="u-url permalink">
                            <time class="date dt-published" datetime="{{$subcomment->published}}">{{date("F j, Y g:i A", strtotime($subcomment->published)) }}</time>
                        </a>
                    </div>
                    <div class='comment_body p-content p-name'>
                        {{$subcomment->content}}
                    </div>
                </div>
            @endforeach
        @endif

    @endforeach
  </div>
@endif
