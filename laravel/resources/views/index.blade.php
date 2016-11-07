<!doctype html>
<html lang="zh" ng-app="xiaohu">
<head>
    <meta charset="UTF-8">
    <title>晓乎</title>
    <script></script>
    <link rel="stylesheet" href="/node_modules/normalize-css/normalize.css">
    <link rel="stylesheet" href="/css/base.css">
    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <script src="/js/base.js"></script>
</head>
<body>
<div class="navbar clearfix">
    <div class="container">
    <div class="fl">
        <div class="item">晓乎</div>
        <div class="item"><input type="text"></div>
    </div>
    <div class="fr">
        <a ui-sref="home" class="item">首页</a>
        <a ui-sref="login" class="item">登陆</a>
        <a ui-sref="signup" class="item">注册</a>
    </div>
    </div>
</div>

<div class="page">
    <div ui-view></div>
</div>



<script type="text/ng-template" id="home.tpl">
<div class="home container">
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dolore eum excepturi fugiat labore, neque optio placeat quas quasi repudiandae voluptatem. Accusamus distinctio dolores incidunt obcaecati porro praesentium quae similique veniam.
</div>
</script>


<script type="text/ng-template" id="login.tpl">
<div class="login container">
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut, dolorum eaque harum iure maxime neque officiis recusandae reiciendis repudiandae similique temporibus unde? Architecto cum cupiditate et fugit non pariatur sint.
</div>
</script>

<script type="text/ng-template" id="signup.tpl">
<div class="signup container" ng-controller="signupController">
    <div class="card">
        <h1>注册</h1>
        <form ng-submit="User.signup()">
            <button type="submit">注册</button>
        </form>
    </div>
</div>
</script>



</body>
</html>