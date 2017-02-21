<!DOCTYPE html>
<html lang="en" ng-app="xiaohu">
<head>
    <meta charset="UTF-8">
    <title>start</title>
    <link href="/node_modules/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <script src="/node_modules/jquery/dist/jquery.js"></script>
    <script src="/node_modules/angular/angular.js"></script>
    <script src="/node_modules/angular-ui-router/release/angular-ui-router.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="/js/base.js"></script>
    <script src="/js/user.js"></script>
    <script src="/js/common.js"></script>
    <script src="/js/question.js"></script>
    <script src="/js/answer.js"></script>

    <style>
        .nav-nopadding li a {
            padding-top: 0;
            padding-bottom: 0;
        }

        body {
            #background: -prefix-linear-zgradient(left, #7b4397, white);
            #background: linear-gradient(to right, #7b4397, #dc2430);

        }
        .form-custom {
            width: 500px;
            margin: 100px auto;
            text-align: center;
        }
        .comment .panel-body{
            padding-bottom: 0px;
        }
        .comment hr{
            margin:0;
        }
        .triangle{
            position: relative;
            top: -17px;
            left: 120px;
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 8px;
            border-color: transparent transparent #ddd transparent;
        }
        .vote-btn .vote-btn-set{
            margin-top:10px;
        }
        .vote-btn-set .up-btn,
        .vote-btn-set2 .up-btn{
            padding: 7px 0;
            line-height: 15px;
            background: #e5eff5;
        }
        .vote-btn-set2 .up-btn{
            padding:7px 5px;
        }
        .vote-btn-set .up-btn:hover,
        .vote-btn-set2 .up-btn:hover{
            background: #99c5e0;
            color:#FFF;
        }
        .vote-btn-set .down-btn{
            /*background: #e6e6e6;*/
            /*color:#989898;*/
            background: #e5eff5;
            padding:3px;
        }
        .vote-btn-set2 .down-btn{
            background: #e5eff5;
            padding:7px 5px;
            line-height: 15px
        }

        .vote-btn-set .down-btn:hover,
        .vote-btn-set2 .down-btn:hover{
            background: #ababab;
            color:#FFF
        }
        .vote-btn-set >*,
        .vote-btn-set2 >*{
            border-radius: 3px;
            text-align: center;
            margin-bottom:2px;
            color:#91b3c7;
            cursor: pointer;
            margin-left:auto;
            margin-right:auto;
        }
        .vote-btn-set{
            width:45px;
        }
        .vote-btn-set2{
            min-width: 45px;
            display: inline-block;
            vertical-align: middle;
        }



        .user-info-set{
            margin-top: 15px;
        }
        .user-info-set li{
            margin-bottom:15px;
        }
        .page-header .user-answer{
            line-height:27px ;
        }
        .pointer{
            cursor: pointer;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container">

        <div class="navbar-header navbar-left ">
            <a class="navbar-brand" href="#">
                LCJ
            </a>
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#mainMenu">
                <span class="sr-only">切换导航</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse" id="mainMenu">
            <form class="navbar-form navbar-left" ng-submit="Question.add()">
                <div class="form-group" ng-controller="searchController">
                    <input type="text" style="width:300px" ng-model="Question.new_question.title" class="form-control" placeholder="Search">
                </div>
                <div class="form-group" ng-controller="questionAddController">
                    <button type="button" class="btn btn-success " ng-click="Question.go();" >提问</button>
                </div>
            </form>
            <div>
                <ul class="nav navbar-nav ">
                    <li class="active"><a href="#">首页</a></li>
                    <li><a href="#">话题</a></li>
                    <li><a href="#">发现</a></li>
                    <li><a href="#">消息<span class="badge">50</span></a></li>
                </ul>
            </div>

            @if (is_logged_in())
              <ul class="nav navbar-nav navbar-right ">
                   <li><img src="./img/下载.png" class="img-thumbnail " style="height: 45px;"/></li>
                   <li><a href="#" ui-sref="user({id:mineInfo.id})">{{session('username')}}</a></li>
                  <li><a href="#" ui-sref="logout">登出</a></li>
               </ul>
            @else
            <ul class="nav navbar-nav navbar-right">
                <li><a href="" ui-sref="login">登录</a></li>
                <li><a href="" ui-sref="signup">注册</a></li>
            </ul>
            @endif
        </div>
    </div>
</nav>
<div class="container " ui-view>

</div>

<script src="/node_modules/bootstrap/dist/js/bootstrap.js"></script>
</body>
</html>