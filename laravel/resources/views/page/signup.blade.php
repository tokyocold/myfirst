<div class="form-custom" ng-controller="signupController">
    <div class="form-group">
        <h1>LCJ</h1>
    </div>
    <div class="form-group">
        <ul class="nav nav-tabs">
            <li   class="active"><a ui-sref="signup" href="#">注册</a></li>
            <li><a ui-sref="login" href="#">登录</a></l
        </ul>
    </div>

    [: User.signup_data :]
    <form name="signup_form" ng-submit="User.signup()">
        <div class="form-group">
            <input name="username"
                   class="form-control"
                   type="text"
                   placeholder="用户名"
                   ng-minlength="4"
                   ng-maxlength="15"
                   required
                   ng-model-options="{updateOn:'blur'}"
                   ng-model="User.signup_data.username"/>
        </div>
        <div ng-if="signup_form.username.$touched">
            <div class="alert alert-danger"   ng-if="signup_form.username.$error.required">用户名为必填项</div>
            <div class="alert alert-danger"   ng-if="signup_form.username.$error.minlength||signup_form.username.$error.maxlength">用户名长度需在4-15之间</div>
            <div class="alert alert-danger"   ng-if="User.signup_data.username && User.username_exists">用户名已存在</div>
        </div>
        <div class="form-group">
            <input name="password"
                   class="form-control"
                   type="password"
                   placeholder="密码"
                   ng-minlength="6"
                   ng-maxlength="255"
                   required
                   ng-model="User.signup_data.password"/>
        </div>
        <div ng-if="signup_form.password.$touched">
            <div class="alert alert-danger"   ng-if="signup_form.password.$error.required">密码为必填项</div>
            <div class="alert alert-danger"   ng-if="signup_form.password.$error.minlength||signup_form.password.$error.maxlength">密码长度需在6-255之间</div>
        </div>
        <div class="form-group ">
            <input type="submit" ng-disabled="signup_form.$invalid && !User.username_exists" value="注册" class="btn btn-primary btn-lg"/>
        </div>
    </form>
</div>