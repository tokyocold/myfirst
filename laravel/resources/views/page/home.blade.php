<div class="row" ng-controller="homeController">
    <div class="col-md-8">
        <div>
            <div class="pull-left">
                <img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" class="img-thumbnail" width="50px">
            </div>
            <div class="row" style="margin-left: 60px;line-height: 30px;">
                <div class="well clearfix" style="">
                    <ul class="nav nav-pills pull-left nav-nopadding">
                        <li><a href="#"><span class="glyphicon glyphicon-question-sign"></span> 提问</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-pencil"></span> 回答</a></li>
                        <li><a href="#"><span class="glyphicon glyphicon-print"></span> 写文章</a></li>
                    </ul>
                    <div class="text-muted pull-right">
                        草稿
                    </div>
                </div>
            </div>
        </div>
        <div class="page-header" style="margin-top: 10px;">
            <span class="text-muted"><span class="glyphicon glyphicon-list-alt"></span> 最新动态</span>
            <span class="text-muted pull-right"><span class="glyphicon glyphicon-wrench"></span> 设置</span>
        </div>
        <div ng-repeat="item in Timeline.data" class="page-header clearfix" style="margin-top: 10px;">
            <div class="pull-left vote-btn">
                <div><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" class="img-thumbnail" width="50px"></div>
                <div class="vote-btn-set" ng-if="item.question_id">
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
                <p class="text-muted" ng-if="item.question_id" >[:item.user.username:] 回答了问题<a href="#"> <span
                                class="pull-right text-muted glyphicon glyphicon-remove"></span></a></p>
                <p class="text-muted" ng-if="!item.question_id" >[:item.user.username:] 提出了问题<a href="#"> <span
                                class="pull-right text-muted glyphicon glyphicon-remove"></span></a></p>

                <p  ng-if="item.question_id"><a href="#" ui-sref="question.detail({id:item.question.id})">[: item.question.title :]</a></p>
                <p  ng-if="!item.question_id"><a href="#" ui-sref="question.detail({id:item.id})">[: item.title :]</a></p>
                <p><a href="#" class="text-muted" ui-sref="user({id:item.user.id})"> <strong>[: item.user.username :]，</strong></a><span class="text-muted"> [: item.user.intro :]</span></p>
                <p ng-if="item.question_id">[: item.content :]
                    <a href="#">显示全部</a>
                </p>
                <p><span class="text-muted"><span class="glyphicon glyphicon-plus"></span>  关注问题</span>&nbsp;&nbsp;&nbsp;<span
                            class="text-muted pointer" ng-click="item.show_comment=!item.show_comment"><span  class="glyphicon glyphicon-comment"></span> [:item.commentCount:]条评论</span>&nbsp;&nbsp;&nbsp;
                    <span class="text-muted">• 作者保留权力</span></p>

                <div ng-if="item.show_comment">
                <comment  ng-if="item.question_id" answer_id="item.id"></comment>
                <comment  ng-if="!item.question_id" question_id="item.id"></comment>
                </div>
            </div>
        </div>



        <p ng-if="Timeline.pendding">
            <button class="btn btn-block btn-default">正在加载...</button>
        </p>
        <p ng-if="!Timeline.pendding&&Timeline.no_more_data">
        <p ng-if="!Timeline.pendding&&Timeline.no_more_data">
        <p ng-if="!Timeline.pendding&&Timeline.no_more_data">
            <button class="btn btn-block btn-default">没有更多了</button>
        </p>
    </div>
    <div class="col-md-4">
        <p>
            <img class="img-rounded" src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" width="270px" height="225px">
        </p>
        <div class="page-header" style="margin-bottom: 10px;">
            <p class="text-muted"><span class="glyphicon glyphicon-bookmark"></span> 我的收藏 <span
                        class="badge">123</span></p>
            <p class="text-muted"><span class="glyphicon glyphicon-check"></span> 我关注的问题</p>
            <p class="text-muted"><span class="glyphicon glyphicon-file"></span> 邀请我回答的问题</p>
        </div>
        <div class="page-header" style="margin-top: 15px;">
            <p class="text-muted"><span class="glyphicon glyphicon-bookmark"></span> 我的收藏 <span
                        class="badge">123</span></p>
            <p class="text-muted"><span class="glyphicon glyphicon-check"></span> 我关注的问题</p>
            <p class="text-muted"><span class="glyphicon glyphicon-file"></span> 邀请我回答的问题</p>
        </div>
        <div class="page-header" style="margin-top: 15px;">
            <p><strong>专栏</strong></p>
            <p><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" height="25px" width="25px" class="img-rounded"/>&nbsp;
                <span class="text-muted">中文中文</span></p>
        </div>
        <div class="page-header" style="margin-top: 15px;">
            <p><strong>啦啦LIVE</strong> <span class="glyphicon glyphicon-flash"></span> <a href="#"
                                                                                          class="pull-right">查看全部
                    >></a></p>
            <p><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" height="25px" width="25px" class="img-rounded"/>&nbsp;
                <span class="text-muted">中文中文中文25中文中文</span></p>
            <p><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" height="25px" width="25px" class="img-rounded"/>&nbsp;
                <span class="text-muted">中文中文中文25中文中文</span></p>
            <p><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" height="25px" width="25px" class="img-rounded"/>&nbsp;
                <span class="text-muted">中文中文中文中文中文中文</span></p>
        </div>
        <div class="page-header" style="margin-top: 15px;">
            <p><img src="https://pic1.zhimg.com/ca15c55704ec6e19b9a0a36a3e7ff5f0_m.jpg" height="25px" width="25px" class="img-rounded"/>&nbsp;
                <span class="text-muted">中文中文中文中文中文中文</span></p>
        </div>
        <p class="text-muted">
            <a href="#" class="text-muted">刘看山</a> &nbsp;•&nbsp; <a href="#" class="text-muted">知乎指南</a> &nbsp;•&nbsp;
            <a href="#" class="text-muted">建议反馈</a> &nbsp;•&nbsp; <a href="#" class="text-muted">移动应用</a>
        </p>
        <p class="text-muted">
            <a href="#" class="text-muted">刘看山</a> &nbsp;•&nbsp; <a href="#" class="text-muted">知乎指南</a> &nbsp;•&nbsp;
            <a href="#" class="text-muted">建议反馈</a> &nbsp;•&nbsp; <a href="#" class="text-muted">移动应用</a>
        </p>
    </div>
</div>