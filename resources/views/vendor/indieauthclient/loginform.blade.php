<span>Login with IndieAuth:</span>
<form method="POST" action="/indieauth_login">
    {!! csrf_field() !!}
    <input type="text" name="me" placeholder="www.example.com" />
    <input type="hidden" name="redirect" value="{{Request::path()}}" />
    <input type="hidden" name="scope" value="{{$scope}}" />
    <input type="submit" name="submit" value="Login"/>
</form>
