(function () {
    'use strict';
    angular.module('answer', [])
        .service("AnswerService", function ($http, $q, $state, VoteDataService, errorHandler) {
            var me = this;
            me.all_answer = {};
            me.vote_count = function (itemData) {
                for (var i = 0; i < itemData.length; i++) {
                    itemData[i].up_count = 0;
                    itemData[i].down_count = 0;
                    if (!itemData[i].question_id || !itemData[i].users) {
                        continue;
                    }

                    for (var j = 0; j < itemData[i].users.length; j++) {
                        if (itemData[i].users[j].pivot.vote == '1') {
                            itemData[i].up_count++;
                        } else {
                            itemData[i].down_count++;
                        }
                    }
                }
            }

            me.vote = function (conf) {
                return $http({
                    url: '/api/answer/vote',
                    method: 'post',
                    params: conf
                }).success(function (data) {
                    if (data.status) {
                        return true;
                    } else
                        return false;
                })
            }

            me.read = function (id) {
                return $http({
                    url: '/api/answer/read?id=' + id,
                    method: "get"
                }).success(function (data) {
                    if (data.status) {
                        return data.data;
                    }
                })
            }

            me.getUserAnswer = function (id) {
                console.log(id);
                if (me.all_answer[id])
                    return;
                me.all_answer[id] = {};
                $http({
                    url: '/api/answer/read?user_id=' + id,
                    method: "get"
                }).success(function (data) {
                    if (data.status) {
                        var answers = data.data ? data.data : [];
                        me.vote_count(answers);
                        me.all_answer[id] = answers;
                        VoteDataService.data = answers;
                    }
                })
            }
            me.current_answer = {};
            me.addOrUpdate = function (question_id) {
                me.current_answer.question_id = question_id;
                if (!me.current_answer.id) {

                    $http({
                        url: 'api/answer/add',
                        method: "POST",
                        data: me.current_answer
                    }).success(function (data) {
                        if (data.status) {
                            me.current_answer = {};
                            $state.reload();
                        } else {
                            errorHandler.error(data.msg);
                        }
                    })

                } else {

                    $http({
                        url: 'api/answer/change',
                        method: "POST",
                        data: me.current_answer
                    }).success(function (data) {
                        if (data.status) {
                            me.current_answer = {};
                            $state.reload();
                        } else {
                            errorHandler.error(data.msg);
                        }
                    })
                }
            }
        })

})();