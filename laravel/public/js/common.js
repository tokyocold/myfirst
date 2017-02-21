(function(){
    'use strict';
    angular.module('common',[])
        .service("TimelineService",function ($http,AnswerService,VoteDataService,$state) {
            var me=this;
            me.data=[];
            me.pendding = false;
            me.current_page = 1;
            me.no_more_data = false;
            me.get = function () {
                if(me.pendding) return;
                me.pendding = true;
                $http({
                    method:"GET",
                    url:"/api/timeline",
                    params:{page:me.current_page}
                }).success(function(data){
                    if(data.status)
                    {
                        if(!data.data)
                        {
                            me.no_more_data = true;
                        }else{
                            //获取点赞情况
                            AnswerService.vote_count(data.data);


                            me.data = me.data.concat(data.data);
                            me.current_page++;
                        }
                    }
                    me.pendding = false;
                }).error(function(){
                    me.pendding = false;
                })
            }

            me.vote = function(conf)
            {
                AnswerService.vote(conf)
                    .then(function (res) {
                        if(res.data.status){
                            AnswerService.read(conf.id).then(function (data) {
                                var answer_n = data.data.data;
                                var answerList = [me.data,VoteDataService.data];
                                //开始更新数据
                                for (var j=0;j<answerList.length;j++)
                                {
                                    var curData= answerList[j];
                                    for(var i=0;i<curData.length;i++)
                                    {
                                        var item = curData[i];
                                        if(item.question_id && item.id == answer_n.id)
                                        {
                                            curData[i] = answer_n;
                                        }
                                    }
                                    AnswerService.vote_count(curData);
                                }

                            })
                        }else{
                            if(res.data.msg == 'need login')
                            {
                                $state.go('login')
                            }
                        }
                    })
            }


        })
        .service("VoteDataService",function () {
            this.data = {};
        })
        .service("CommentService",function ($http) {
            var me=this;
            me.commentData = {};
            me.readComment = function (conf) {
                return $http({
                    url:'api/comment/read',
                    params:conf
                }).success(function (data) {
                    if(data.status)
                        me.commentData = data.data;
                })
            }
            me.newComment = {};
            me.addComment = function () {
                return $http({
                    url:'api/comment/add',
                    method:"POST",
                    data:me.newComment
                }).success(function(){
                    me.newComment = {};
                })
            }
        })

        .directive('comment',function(CommentService){
            return {
                restrict:"E",
                scope:{
                    answerId:"=",
                    questionId:"="
                },
                templateUrl:'/tpl/page/comment',
                link:function (scope,element,attrs) {

                    scope.btnShow=false;
                    scope.comment = CommentService;

                    function readComment() {
                        CommentService.readComment({answer_id:scope.answerId,question_id:scope.questionId})
                            .then(function () {
                                scope.commentData = CommentService.commentData;
                                scope.commentLength = Object.keys(CommentService.commentData).length;

                            });
                    }
                    readComment();
                    scope.addComment = function () {
                        if(scope.answerId)
                        {
                            CommentService.newComment.answer_id = scope.answerId;
                        }else if(scope.questionId)
                        {
                            CommentService.newComment.question_id = scope.questionId;
                        }
                        CommentService.addComment()
                            .then(function () {
                                readComment();
                            });
                    }



                }
            }

        })

        .directive("lcjTest",function(){
            return{
                restrict:"A",
                scope:{
                    lcjTest:"=",
                    lalala:"=",
                    lala:"&"
                },
                template:'<a href="javascript:;" ng-click="lala()">123{{lcjTest}}</a>',
                link:function (scope,element,attrs) {
                    if(scope.lcjTest)
                    {
                        $(element).show();
                    }else{
                        $(element).hide();
                    }
                    scope.lalala='abc';
                    console.log($(element).html());
                }

            }
        })
        .controller('lcjController',function ($scope) {
            $scope.lalala='123123';
            $scope.display=true;
            $scope.lala=function () {
                alert('123');
            }
        })

        .controller('searchController',function ($scope,QuestionService) {
            $scope.Question=QuestionService;

        })
        .controller('homeController',function ($scope,TimelineService) {
            $scope.Timeline = TimelineService;
            TimelineService.get();
            $(window).on('scroll',function () {
                if($(window).scrollTop()+$(window).height() - $(document).height()>=-30)
                {
                    TimelineService.get();
                }
            })
            $scope.lala="llallala";
            $scope.$on('$destroy', function() {
                $(window).unbind('scroll');
            });
        })
})();