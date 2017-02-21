<div class="page-header">
    <h4>他的回答</h4>
</div>
<div class="page-header clearfix" ng-repeat="item in Answer.all_answer[userId]" style="margin-top: 10px;">
    <p>
    <h3><a href="#" ui-sref="question.answer({id:item.question.id,answer_id:item.id})">[:item.question.title:]</a></h3></p>
    <div class="clearfix" style="margin-bottom: 10px;">
        <img class="pull-left" src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" class="img-thumbnail"
             width="38px" style="vertical-align: middle"/>
        <div class="pull-left" style="margin-left: 20px;">
            <div>[:item.user.username:]</div>
            <div>[:item.user.intro:]</div>
        </div>
    </div>
    <p class="text-muted">[:item.up_count:]人赞同了该答案</p>
    <p class="user-answer">
        [:item.content:]
        <a href="#">显示全部</a>
    </p>
    <div>
        <div class="vote-btn-set2 clearfix" style="display: inline-block">
            <div ng-click="Timeline.vote({id:item.id,vote:1})" class="up-btn pull-left">
                <span class="glyphicon glyphicon-chevron-up"></span>[:item.up_count:]
            </div>
            <div ng-click="Timeline.vote({id:item.id,vote:2})" class="down-btn pull-left" style="margin-left: 5px">
                <span class="glyphicon glyphicon-chevron-down"></span>
            </div>
        </div>

        &nbsp;&nbsp;&nbsp;<span
                class="text-muted pointer" ng-click="item.showComment=!item.showComment"><span class="glyphicon glyphicon-comment"></span> [:item.comments.length:]条评论</span>&nbsp;&nbsp;&nbsp;
        <span class="text-muted">• 作者保留权力</span>
    </div>
    <comment ng-if="item.showComment" answer-id="item.id"></comment>
</div>
