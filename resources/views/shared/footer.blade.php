<footer id="colophon" role="contentinfo">
  <div id="site-generator">
    This site is powered by <a href="https://github.com/dissolve/inkblot"><img style="width:20px;" src="/image/inkblot.svg"/>Splatter</a> an 
      <a href="https://inklings.io/"><img style="width:20px;" src="/image/inkling.svg"/>Inkling Project</a>
  </div>
</footer><!-- #colophon -->

</div><!-- #page -->

@if (isset($google_analytics_id))
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', '<?php echo $google_analytics_id?>', 'auto');
  ga('require', 'displayfeatures');
  ga('send', 'pageview');

</script>
@endif
