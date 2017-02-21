<div class="row" ng-controller="userController">
    <div class="jumbotron clearfix">
        <br />
        <br />

            <div class="col-md-2">
            <img src="/avatar.jpg"  class="img-rounded img-responsive center-block"  />
            </div>
                <div class="col-md-10">
            <h3>[:User.basic_data[userId].username:]
                <small>[:User.basic_data[userId].intro:]</small>
            </h3>
                    <ul class="list-unstyled user-info-set">
                        <li><span class="text-muted"><span class="glyphicon glyphicon-lock"></span>  互联网</span> </li>
                        <li> <span class="text-muted"><i class="fa fa-venus"></i></span></li>

                    </ul>
                <p>
                    <button class="pull-right btn btn-default btn-lg">编辑个人资料</button>
                </p>
            </div>
    </div>
    <div>
        <ul class="nav nav-tabs">
            <li  ng-class="{active:isActive('/user/'+state.id+'/question')}"><a ui-sref="user.question" href="#" ng-click="alert('123')" >提问</a></li>
            <li  ng-class="{active:isActive('/user/'+state.id+'/answer')}" class=""><a ui-sref="user.answer" href="#">回答</a></li>
        </ul>
        <div class="tab-content" ui-view>

        </div>
    </div>

</div>