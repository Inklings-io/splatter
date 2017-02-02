  <aside id="sidebar">
    <div id="secondary" class="widget-area" role="complementary">
<section id="search-2" class="widget widget_search">
  <form role="search" method="get" class="search-form" action="https://www.google.com/search">
    <label>
        <span class="screen-reader-text">Search for:</span>
        <input type="search" class="search-field" placeholder="Search &hellip;" value="" name="q" title="Search for:" />
        <input type="hidden" name='as_sitesearch' value='ben.thatmustbe.me' />
        <input type="hidden" name="tbs" value="sbd:1,cdr:1,cd_min:1/1/1970"/>
    </label>
    <input type="submit" class="search-submit" value="Search" />
  </form>
</section>

<?php if(isset($moderation_count) && $moderation_count) { ?>
    <section id="moderation" class="widget widget_recent_entries">
    <a href="<?php echo $moderation_url?>" style="color:red"><h3 class="widget-title">Pending Webmentions <span style="color:red;font-weight:bold">(<?php echo $moderation_count?>)</span></h3></a>
    </section>
<?php } ?>

<?php if(isset($recent_interactions) && $recent_interactions) { ?>
    <section id="recent-interactions-2" class="widget widget_recent_entries">
    <h3 class="widget-title">Recent interactions</h3>
    <ul>
    <?php foreach($recent_interactions as $interaction){ ?>
        <li>
            <a href="<?php echo $interaction['post']['permalink']?>"><?php echo $interaction['interaction_type'] . ' by ' . $interaction['author_name']?></a>
        </li>
    <?php } // end foreach recent_drafts?>
    </ul>
    </section>
<?php } ?>

@if($recent_drafts)
    <section id="recent-drafts-2" class="widget widget_recent_entries">
    <h3 class="widget-title">Recent Drafts</h3>
    <ul>
    @foreach($recent_drafts as $post)
        <li>
            <a href="{{$post->permalink}}">{{$post->name}}</a>
        </li>
    @endforeach
    </ul>
    </section>
@endif

@if (isset($archives))
<section id="archives-2" class="widget widget_archive">
<h3 class="widget-title">Archives</h3>
<ul>
    <?php foreach($archives as $arch){ ?>
    <li><a href='<?php echo $arch['permalink']?>'><?php echo $arch['name'] ?></a></li>
    <?php } // end foreach archives ?>
</ul>
</section>
@endif

@if (isset($categories))
<section id="categories-2" class="widget widget_categories">
<h3 class="widget-title">Categories</h3>
<ul>
    @foreach($categories as $category)
        <li class="cat-item cat-item-1">
            <a href="<?php echo $category['permalink'];?>" type="text/html" title="Posts filed under <?php echo $category['name'];?>"><?php echo $category['name'];?></a>
        </li>
    @endforeach
</ul>
</section>
@endif

@if (isset($mylinks))
<section id="linkcat-3" class="widget widget_links"><h3 class="widget-title">Elsewhere</h3>
    <ul>
        <?php foreach($mylinks as $mylink){?>
            <li><a href="<?php echo $mylink['url'];?>" rel="<?php echo $mylink['rel']?>" title="<?php echo $mylink['title'];?>" target="<?php echo $mylink['target'];?>"><?php echo $mylink['value'];?></a></li>
        <?php } ?>
    </ul>
</section>
@endif

@if (isset($recent_mentions))
<?php if(!empty($recent_mentions)){ ?>
<section id="mentions" class="widget widget_links"><h3 class="widget-title">Recent Mentions</h3>
    <ul>
        <?php foreach($recent_mentions as $mention){?>
            <li><a href="<?php echo $mention['source_url'];?>" title="External Web Mention"><?php echo $mention['source_url'];?></a></li>
        <?php } ?>
    </ul>
</section>
<?php } //end if recent mentions ?>
@endif

@if (isset($recent_tags))
<?php if(!empty($recent_tags)){ ?>
<section id="tags" class="widget widget_links"><h3 class="widget-title">Recent Tags</h3>
    <ul>
        <?php foreach($recent_tags as $mention){?>
            <li><a href="<?php echo $mention['source_url'];?>" title="External Web Mention"><?php echo $mention['source_url'];?></a></li>
        <?php } ?>
    </ul>
</section>
<?php } //end if recent mentions ?>
@endif


@if (isset($login))
<section id="login" class="widget">
    <?php if(isset($user_name)) { ?>
    <h3 class="widget-title">Logged In As "<?php echo $user_name?>"</h3>
    <ul><li>
        <a href="<?php echo $logout?>">Log Out</a>
    </li></ul>
    <?php } else { ?>
    <h3 class="widget-title">Log In with IndieAuth</h3>
    <ul><li>
        <form action="<?php echo $login?>" method="get">
          <label for="indie_auth_url">Web Address:</label>
          <input id="indie_auth_url" type="text" name="me" placeholder="yourdomain.com" />
          <p><button type="submit">Log In</button></p>
        </form>
    </li></ul>
    <?php } ?>
</section>
@endif

<?php if(isset($webaction)){ ?>
<section id='webaction' class="widget">
    <button onclick=" window.navigator.registerProtocolHandler('web+action', '<?php echo $webaction;?>' , 'InkBlot');" value="" >Register Your Handler</button>
</section>
<?php } ?>

<?php if(isset($is_owner)){ ?>
        <section id='toggle_notifications' class="widget">
            <button class="js-push-button" disabled> 
              Enable Push Messages  
            </button>
        </section>

    <script src="/view/javascript/push.js?v=1"></script>
<?php } // end is_owner?>
</div><!-- #secondary .widget-area -->

</aside>
