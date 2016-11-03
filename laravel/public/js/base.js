(function(){
    'use strict';
    angular.module('xiaohu',['ui.router'])
    .config(function($interpolateProvider,$stateProvider,$urlRouterProvider){
        $interpolateProvider.startSymbol('[:');
        $interpolateProvider.endSymbol(':]');


        
    })


})();