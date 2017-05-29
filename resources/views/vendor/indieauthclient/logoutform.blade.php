<form method="POST" action="/indieauth_logout">
    {!! csrf_field() !!}
    <input type="hidden" name="redirect" value="{{Request::path()}}" />
    <input type="submit" name="submit" value="Log Out"/>
</form>
