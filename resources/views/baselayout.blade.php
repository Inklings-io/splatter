<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{mix('css/app.css')}}" />

    @yield('header')

    <link rel="profile" href="http://microformats.org/profile/specs" />
    <link rel="profile" href="http://microformats.org/profile/hatom" />

    <link rel="authorization_endpoint" href="https://indieauth.com/auth">
    <link rel="webmention" href="/api/webmentions">
    <link rel="token" href="/api/token">
    <link rel="micropub" href="/api/micropub">

    <link rel="authorization_endpoint" href="https://indieauth.com/auth">

    @if(!empty($added_headers))
      @foreach($added_headers as $added_header)
        {!! $added_header !!}
      @endforeach
    @endif

    <!--
      <link rel="stylesheet" href="/css/old/normalize.css">
      <link rel="stylesheet" href="/css/old/main.css">
      <link rel='stylesheet' id='sempress-style-css'  href='/css/old/stylesheet.css' type='text/css' media='all' />
    -->
    <link href="/vendor/css/font-awesome.min.css" rel="stylesheet">

  </head>


  <body class="home container {{$bodyclass}}">
    <div class="row">
      <div class="col-sm-12">
        <header class="{{($bodyclass == 'h-card' ? '' : 'p-author h-card') }}" id="headBanner" role="banner">
          <h1 id="site-title"><a href="{{$app_url}}" title="{{$site_name}}" rel="home">{{$site_name}}</a></h1>
          <h2><a class="p-name u-url" href="{{$owner['url']}}" title="{{$owner['name']}}">{{$owner['name']}}</a></h2>
          @if(isset($owner['role']) && !empty($owner['role']))
            <h3 id="site-description" class="p-role">{{$owner['role']}}</h3>
          @endif

          <img class="u-photo" src='{{$owner['image']}}' height='40' width='40' style="display:none;"/>

        </header>

        <div class="notification">
            @if (Session::has('success'))
                <div class="flash_success">{{ session('success') }}</div>
            @endif
            @if (Session::has('error'))
                <div class="flash_error">{{ session('error') }}</div>
            @endif
        </div>

      </div>
    </div>

    <div class="row">
      <div class="col-sm-12">
          @if(isset($success))
            <div class="success"><?php echo $success?></div>
          @endif
          @if(isset($error))
            <div class="error"><?php echo $error?></div>
          @endif
      </div>
    </div>

    <div id="row">
      <div class="col-sm-12 col-lg-9">

        <section id="primary">

          <main id="content" role="main">
              
            @yield('content')
            
          </main><!-- #content -->
        </section><!-- #primary -->
      </div>

      <div class="col-sm-12 col-lg-3">
        @include('shared.sidebar')
      </div>

    </div>

    <div class="row">
      <div class="col-sm-12">
       @include('shared.footer')

      </div>
    </div>

  </body>
</html>
