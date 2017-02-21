<div class="panel panel-default comment" >
    {{--<div class="triangle"></div>--}}
    <div ng-if="commentLength==0" class="panel-body text-center">
        <p>暂无评论</p>
        <hr>
    </div>
    <div ng-repeat="item in commentData" class="panel-body">
        <p><a href="#" ui-sref="user({id:item.user.id})">[:item.user.username:]</a></p>
        <p>[:item.content:]</p>
        <p class="text-muted">[:item.created_at:]</p>
        <hr>
    </div>
    <div class="panel-body">
        <form name="commentForm" ng-submit="addComment()">
        <div class="form-group">
            <input name="content" required ng-maxlength="255" type="text" ng-model="comment.newComment.content"  class="form-control" ng-focus="btnShow = true"  />
        </div>
        <div class="form-group clearfix text-right" ng-if="btnShow">
            <a href="javascript:;" ng-click="$parent.btnShow =false" >取消</a>
            <button type="submit" ng-disabled="commentForm.$invalid" class="btn btn-primary">评论</button>
        </div>
        </form>
    </div>
</div>
