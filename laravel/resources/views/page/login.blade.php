<div ng-controller="loginController" class="form-custom" >
    <div class="form-group">
        <h1>LCJ</h1>
    </div>
    <div class="form-group">
        <ul class="nav nav-tabs">
            <li><a ui-sref="signup" href="#">注册</a></li>
            <li  class="active"><a ui-sref="login" href="#">登录</a></li>
        </ul>
    </div>
    <form name="login_form" ng-submit="User.login()">
        <div class="form-group">
            <input type="text" autocomplete="off"  ng-model="User.login_data.username" required name="username" class="form-control" placeholder="用户名" />
        </div>
        <div class="form-group">
            <input type="password"  autocomplete="off"  ng-model="User.login_data.password" required name="password"  class="form-control" placeholder="密码" />
        </div>
        <div class="form-group">
            <div  ng-if="User.login_error" class="alert alert-danger">用户名密码错误</div>
        </div>
        <div class="form-group">
            <button ng-disabled="login_form.username.$error.required || login_form.password.$error.required" class="btn btn-primary btn-lg" type="submit">登录</button>
        </div>
    </form>
</div>