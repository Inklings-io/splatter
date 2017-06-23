<article id="post-{{$post->id}}" class="container-fluid postbox {{$post->type}} h-entry {{ $post->draft == 1 ? 'draft':'' }} {{$post->type == 'snark' ? 'snark':''}} {{$post->deleted_at ? 'deleted':''}} " >


  <header>
    <div class="row">
      <span class="col-lg-4">      
        Posted on <a class="u-url" href="{{$post->permalink}}" title="<?php echo date("g:i A", strtotime($post['published']))?>">
          <time class="dt-published" datetime="<?php echo date("c", strtotime($post->published))?>" >
            <?php echo date("F j, Y", strtotime($post->published))?>
          </time>
        </a>
      </span>

      <span class="col-lg-2">      
        <a class="shortlink" href="{{$post->shortlink}}">shortlink</a>
      </span>

      <span class="col-lg-6">      
        @if(!empty($post->inReplyTos->all()))
          In Reply To 
          @foreach($post->inReplyTos as $replyTo)
            <a class="u-in-reply-to" href="{{$replyTo->url}}">{{str_limit($replyTo->url, 30, '...')}}</a>
          @endforeach
        @endif
      </span>
    </div>

    @if($post->name && $post->type != 'listen')
      <div class="row">
        <span class="col-lg-12">
          <h3 class="p-name">
            <a class="u-url" href="{{$post->permalink}}" title="Permalink to <?php echo $post['name']?>" >
              <?php echo $post['name']?>
            </a>
          </h3>
        </span>
      </div>
    @endif

  </header><!-- .entry-header -->



  <div class="row {{$post['summary_html'] ? 'p-summary' : 'e-content'}}">
    @if($post->weight)
     <h4 class="col-sm-12 h-measure p-weight">
       Weight: <data class="p-num" value="{{$post->weight->num}}"> {{$post->weight->num}} </data>
       <data class="p-unit" value="{{$post->weight->unit}}">{{$post->weight->unit}}</data>
     </h4>
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
    @if(isset($post['summary_html']))
      <a href="{{$post->permalink}}" class="u-url">More...</a>
    @endif

                 
    @if(!empty($post->reacjis) )
      <span id="general-reacjis">
        @foreach($post->reacjis as $content=> $reacji)
          <span class="reacji-container">
            <span class="reacji">{{$content}}</span>
            <span class="reacji-count">{{$reacji->count()}}</span>
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
    <span class="reposts-link"><a href="{{$post->permalink}}#reposts" title="reposts of <?php echo $post['name']?>"><i class="fa fa-retweet"></i> {{$post->reposts->count()}}</a></span>
      <span class="sep"> | </span>
    @endif

    @if($post->categories)
      @foreach($post->categories as $category)
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

    @if(!empty($post->syndications))
      <div class="syndications">
        @foreach($post->syndications as $elsewhere)
          @if(isset($elsewhere->site))
            <a class="u-syndication" href="{{$elsewhere->url}}" ><img style='height:20px;width:20px;' src="{{$elsewhere->site->image}}" title="{{$elsewhere->site->name}}" /></a>
          @else
            <a class="u-syndication" href="{{$elsewhere->url}}" ><i class="fa fa-link"></i></a>
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
