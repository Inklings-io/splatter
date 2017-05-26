  <header class="h-card" id="headBanner" role="banner">
    <h1 id="site-title" class="p-name"><a href="/" title="Ben Roberts" rel="home" class="u-url">Ben Roberts</a></h1>
    <h2 id="site-description" class="p-role p-summary e-content">Developer &amp; Technologist</h2>

  </header>
    {!! IndieAuth::login_logout_form() !!}

    @if (IndieAuth::is_logged_in())
        <div>Logged In As: {!! IndieAuth::user() !!}</div>

    @endif

    <div class="notification">
        @if (Session::has('success'))
            <div class="flash_success">{{ session('success') }}</div>
        @endif
        @if (Session::has('error'))
            <div class="flash_error">{{ session('error') }}</div>
        @endif
    </div>
