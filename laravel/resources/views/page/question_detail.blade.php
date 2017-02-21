<div ng-controller="questionDetailController">
    <div class="page-header">
        <p><h4><a href="#" ui-sref="question.detail({id:Question.question_detail.id})">[:Question.question_detail.title:]</a> <small ng-if="Question.question_detail.user_id == mineInfo.id "><a href="javascript:;" onclick="editQuestion()" >编辑</a></small></h4>
        </p>
        <p>[:Question.question_detail.desc:]</p>
        <p class="text-muted"><span
                    class="text-muted pointer" ng-click="Question.showComment = !Question.showComment" ><span class="glyphicon glyphicon-comment"></span> [:Question.question_detail.comments.length:]条评论</span></p>
        <comment ng-if="Question.showComment" question-id="Question.question_detail.id"></comment>
    </div>
    <div>
        <strong ng-if="!Question.current_answer_id">[:Question.question_detail.answers.length:]条回答</strong>
        <a ng-if="Question.current_answer_id" href="#" ui-sref="question.detail({id:Question.question_detail.id})">查看全部[:Question.question_detail.answers.length:]条回答</a>
        <hr>
    </div>
    <div ng-if="!Question.current_answer_id||Question.current_answer_id == item.id" class="page-header" ng-repeat="item in Question.question_detail.answers">
        <div class="pull-left vote-btn">
            <div class="vote-btn-set" style="margin-top: 0">
                <div ng-click="Timeline.vote({id:item.id,vote:1})" class="up-btn">
                    <span class="glyphicon glyphicon-chevron-up"></span>
                    <div>[:item.up_count:]</div>
                </div>
                <div ng-click="Timeline.vote({id:item.id,vote:2})" class="down-btn">
                    <span class="glyphicon glyphicon-chevron-down"></span>
                    <div>[:item.down_count:]</div>
                </div>
            </div>
        </div>
        <div class="" style="margin-left: 60px">
            <p><strong><a href="#" ui-sref="user({id:item.user.id})" class="text-muted">[:item.user.username:]</a> ，</strong><span class="text-muted"> [:item.user.intro:]</span></p>
            <p class="text-muted">[:item.up_count:]人赞同</p>
            <p>[:item.content:]
                <a href="#">显示全部</a>
            </p>
            <p><span class="text-muted">编辑于[:item.created_at:]</span>&nbsp;&nbsp;&nbsp;<span
                        class="text-muted pointer" ng-click="item.showComment=!item.showComment"><span class="glyphicon glyphicon-comment"></span> [:item.comments.length:]条评论</span>&nbsp;&nbsp;&nbsp;
                <span class="text-muted">• 作者保留权力</span>
                <a href="javascript:;" ng-if="mineInfo.id == item.user_id" ng-click="Answer.current_answer = item" >编辑</a>
            </p>
            <comment ng-if="item.showComment" answer-id="item.id"></comment>
        </div>
    </div>

    <div ng-if="mineInfo" class="page-header">
        <form name="answer_form"  ng-submit="Answer.addOrUpdate(Question.question_detail.id)">
        <p><a href="#" ui-sref="user({id:mineInfo.id})">[:mineInfo.username:]</a>,<strong>[:mineInfo.intro:]</strong> </p>
        <div class="form-group">
            <textarea required name="content" ng-model="Answer.current_answer.content" class="form-control" rows="10"></textarea>
        </div>
        <div class="text-right">
            <button ng-disabled="answer_form.$invalid" type="submit" class="btn btn-primary">发布回答</button>
        </div>
        </form>
    </div>


    <div class="modal fade" id="question_add_modal"  >
        <div class="modal-dialog">
            <form name="question_form" ng-submit="question_form.$valid&&Question.update()">
                <div class="modal-content">
                    <div class="modal-header">
                        修改提问
                        <button type="button" data-dismiss="modal" class="close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div >

                            <div class="form-group">
                                <input name="title" ng-minlength="5" ng-maxlength="255" required class="form-control" type="text" placeholder="写下你的问题" ng-model="Question.question_detail.title" />
                            </div>

                            <div class="form-group">
                                <p>问题说明（可选）：</p>
                                <textarea rows="4" cols="50" class="form-control"  ng-model="Question.question_detail.desc" placeholder="请输入问题描述"></textarea>
                            </div>
                            <div class="form-group" ng-if="question_form.$submitted">
                                <div ng-if="question_form.title.$error.required" class="alert alert-danger">问题不能为空</div>
                                <div ng-if="question_form.title.$error.minlength||question_form.title.$error.maxlength" class="alert alert-danger">问题标题长度在5-255之间</div>
                                <div ng-if="" class="alert alert-danger">需要登录</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="pull-right">
                            <a href="javascript:;" data-dismiss="modal">取消</a>&nbsp;&nbsp;&nbsp;&nbsp;
                            <button class="btn btn-primary" type="submit">发布</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function editQuestion() {
        $(function () { $('#question_add_modal').modal({

        })});
    }
</script>