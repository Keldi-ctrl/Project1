{{ content() }}
<div class="jumbotron">
    <h1>Вы не авторизованы!</h1>
    <p>You are not authorized to view this page</p>
    <p>{{ link_to('sign', 'log in', 'class': 'btn btn-info') }}</p>
</div>