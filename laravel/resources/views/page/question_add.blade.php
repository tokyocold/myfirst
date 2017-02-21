<div class="modal fade" id="question_add_modal" ng-controller="questionAddController" >
    <div class="modal-dialog">
        <form name="question_form" ng-submit="question_form.$valid&&Question.add()">
            <div class="modal-content">
                <div class="modal-header">
                    提问
                    <button type="button" data-dismiss="modal" class="close">&times;</button>
                </div>
                <div class="modal-body">
                    <div >

                        <div class="form-group">
                            <input name="title" ng-minlength="5" ng-maxlength="255" required class="form-control" type="text" placeholder="写下你的问题" ng-model="Question.new_question.title" />
                        </div>

                        <div class="form-group">
                            <p>问题说明（可选）：</p>
                            <textarea rows="4" cols="50" class="form-control"  ng-model="Question.new_question.desc" placeholder="请输入问题描述"></textarea>
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
<script>
    $(function () { $('#question_add_modal').modal({

    })});
</script>