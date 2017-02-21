(function () {
    'use strict';
    angular.module('question',[])

        .service("QuestionService",function($http,$state,AnswerService,VoteDataService){
            var me=this;
            me.new_question={};
            me.all_question = {}
            me.current_answer_id =0;
            me.question_detail = {};
            me.go = function(){
                $state.go('question.add');
            }
            me.add=function () {
                $http({
                    method:"POST",
                    url:"/api/question/add",
                    params:me.new_question
                }).success(function (data) {
                    //   angular.element('#question_add_modal').modal('hide');
                    $('#question_add_modal').modal('hide')
                        .on('hidden.bs.modal',function () {
                            if(data.status)
                            {
                                //成功
                                $state.go('home');
                            }else{
                                //失败
                                $state.go('login');
                            }
                        });
                })
            }
            me.update = function () {
                $http({
                    method:"POST",
                    url:"/api/question/edit",
                    data:me.question_detail
                }).success(function(data){
                    if(data.status)
                    {
                        $('#question_add_modal').modal('hide')
                        on('hidden.bs.modal',function () {
                            $state.reload();
                        });

                    }
                })
            }
            me.getUserQuestion = function (userid) {
                if(me.all_question[userid])
                    return;
                $http({
                    url:'api/question/read',
                    params:{user_id:userid}
                }).success(function (data) {
                    if(data.status)
                    {
                        me.all_question[userid] = data.data;
                    }

                })
            }
            me.read = function (id) {
                $http({
                    url:'/api/question/read',
                    params:{id:id}
                }).success(function (data) {
                    var curData = data.data.answers?data.data.answers:[];
                    me.question_detail = data.data;
                    AnswerService.vote_count(curData);
                    //console.log(curData);
                    VoteDataService.data = curData;
                })
            }
        })
        .controller('questionAddController',function ($scope,QuestionService) {
            $scope.Question=QuestionService;
        })
        .controller('questionDetailController',function ($scope,$stateParams,AnswerService,QuestionService,TimelineService) {
            $scope.Question=QuestionService;
            $scope.Answer = AnswerService;
            $scope.Timeline = TimelineService;
            QuestionService.read($stateParams.id);
            if($stateParams.answer_id)
            {
                QuestionService.current_answer_id = $stateParams.answer_id;
            }else
                QuestionService.current_answer_id = 0;
        })

})()