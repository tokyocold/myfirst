(function(){
    'use strict';
    angular.module('xiaohu',['ui.router','common','question','answer','user'])
    .provider('lcj',function () {
        this.name = "lcj";
        this.$get = function () {
            var lcj = this;
            return {
                'gender':'male',
                'getname':function () {
                    return lcj.name;
                }
            }
        }
    })
    .service('errorHandler',function ($state) {
        var me=this;
        me.error = function (msg) {
            if(msg == 'need login')
            {
                $state.go('login');
            }
        }
    })
    .config(function($interpolateProvider,$stateProvider,$urlRouterProvider,lcjProvider){
        $interpolateProvider.startSymbol('[:');
        $interpolateProvider.endSymbol(':]');

        lcjProvider.name="Ash";

        $urlRouterProvider
            .when('/user/:id','/user/:id/question')
            .otherwise('/home');
        $stateProvider
        .state('home',{
            url:'/home',
            templateUrl:"/tpl/page/home"
        })
        .state('login',{
            url:'/login',
            templateUrl:"/tpl/page/login"
        })
        .state('logout',{
            url:'/logout',
            controller:function ($scope,$state,$http) {
                $http({
                    url:'/api/logout'
                }).success(function (data) {
                    window.location.href="/";
                })
            },
        })
        .state('signup',{
            url:'/signup',
            templateUrl:"/tpl/page/signup"
        })
        .state('question',{
            abstract:true,
            url:'/question',
            template:'<div ui-view></div>'
        })
        .state('question.add',{
            url:'/add',
            templateUrl:"/tpl/page/question_add"
        })
        .state('question.detail',{
            url:'/detail/:id',
            templateUrl:"/tpl/page/question_detail"
        })
        .state('question.answer',{
            url:'/detail/:id/:answer_id',
            templateUrl:"/tpl/page/question_detail"
        })
        .state('user',{
            url:'/user/:id',
            templateUrl:"/tpl/page/user"
        })
        .state('user.question',{
            url:'/question',
            templateUrl:'/tpl/page/user-question',
            controller:'userController'
        })
        .state('user.answer',{
            url:'/answer',
            templateUrl:'/tpl/page/user-answer',
            controller:'userController'
        })

        ;
    })
    .run(function($rootScope,$http){
        $rootScope.mineInfo = {};
        $http({
            url:'/api/is_logged_in'
        }).success(function (data) {
            if(data.status)
            {
                $rootScope.mineInfo = data.data.data;
            }
        })
    })
})();