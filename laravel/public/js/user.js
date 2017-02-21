(function(){
    'use strict';
    angular.module('user',[])
        .service("UserService",function($http,$state){
            var me = this;
            me.signup_data={};
            me.login_data={};
            me.basic_data = {}
            me.signup=function()
            {
                $http({
                    method:"GET",
                    url:"/api/user/",
                    params:me.signup_data,
                }).success(function (data) {
                    if(data.status )
                    {
                        me.signup_data={};
                        $state.go('login');
                    }
                })
            };
            me.exists=function(username)
            {
                $http({
                        method:'GET',
                        url:'/api/user/exists',
                        params:{username:username}
                    }
                ).success(
                    function (data) {
                        if(data.status && data.data.count)
                        {
                            me.username_exists = true;
                        }else{
                            me.username_exists = false;
                        }
                    }
                ).error(function (e) {
                    console.log(e);
                })
            };
            me.login=function () {;
                $http({
                    method:"POST",
                    url:"/api/login/",
                    params:me.login_data,
                }).success(function (data) {
                    if(data.status)
                    {
                        window.location.href="/";
                    }else{
                        me.login_error = true;
                    }
                })
            }
            me.read = function (userid) {
                if(me.basic_data[userid])
                    return;
                $http({
                    url:'/api/user/read',
                    params:{id:userid}
                }).success(function(data){
                    if (data.status)
                    me.basic_data[data.data.data.id] = data.data.data;
                })
            }
        })
        .controller('signupController',[
            '$scope','UserService',
            function($scope,UserService){
                $scope.User = UserService;
                $scope.$watch('User.signup_data.username',function (n,o) {
                    if(n!=o)
                    {
                        UserService.exists(n);
                    }
                },true);
            }
        ])
        .controller('loginController',function ($scope,UserService) {
            $scope.User=UserService;
        })
        .controller('userController',function($scope,$stateParams,$location,$state,UserService,QuestionService,AnswerService,TimelineService){
            $scope.state = $stateParams;
            $scope.isActive = function (viewLocation) {
                return viewLocation == $location.path();
            }
            $scope.User = UserService;
            $scope.Answer = AnswerService;
            $scope.Question = QuestionService;
            $scope.userId = $stateParams.id;
            $scope.Timeline = TimelineService;
            //获取用户信息
            UserService.read($stateParams.id);

            //获取用户提问
            if($state.current.name == 'user.question')
            {

            QuestionService.getUserQuestion($stateParams.id);
            }

            //获取用户回答
            if($state.current.name == 'user.answer')
            {
                AnswerService.getUserAnswer($stateParams.id);
                console.log(AnswerService.all_answer);
            }



        })
})();