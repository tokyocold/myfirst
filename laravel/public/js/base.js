(function(){
    'use strict';
    angular.module('xiaohu',['ui.router'])
    .config(function($interpolateProvider,$stateProvider,$urlRouterProvider){
        $interpolateProvider.startSymbol('[:');
        $interpolateProvider.endSymbol(':]');

        $urlRouterProvider.otherwise('/home');
        $stateProvider
        .state('home',{
            url:'/home',
            templateUrl:"home.tpl"
        })
        .state('login',{
            url:'/login',
            templateUrl:"login.tpl"
        })
        .state('signup',{
            url:'/signup',
            templateUrl:"signup.tpl"
        });

        
    })

    .service("UserService",function(){
        var me = this;
        me.signup=function()
        {
            console.log("singup");
        }
    })

    .controller('signupController',[
        '$scope','UserService',
        function($scope,UserService){
            $scope.User = UserService;

        }
    ])

})();