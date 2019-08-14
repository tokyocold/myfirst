# angular#
## ng-app
初始化一个angularJs应用程序

## ng-model
把一个输入域的值绑定到应用程序 Input/textarea
ng-model 指令可以为应用数据提供状态值(invalid, dirty, touched, error):
如果要通过formname.inputField访问操作input，必须先要在input上绑定ng-model指令才可。




## $scope
作用域，可以理解为mvc中的model，每一个controller都有自己的变量，这些变量应该只存在于本身的作用域中。
例如：User Order中都可以有一个名为name的变量，但是name只在自身所在的controller中才有效。这就是作用域


## ng-controller
控制器，定义了控制器后，控制器内的变量或者方法就必须由$scope来保存

## 过滤器  |
相当于管道  currency  lowercase orderby  uppercase filter

## Service
服务是一个函数或者对象，ag内建了30多个服务可供使用

## select
- ng-options 创建一个下拉列表
    \<select ng-options = 'x for x in names'></select>
- ng-repeat
    \<select>
    \<option ng-repeat="x in names">{{x}}</option>
    \</select>

## HTML DOM
### ng-disabled     
    html disabled属性
### ng-show
    display属性
### ng-hide
    类display属性


## 事件
### ng-click
    <button ng-click="click=click+1">点</button>

## 依赖注入
- value
- factory
- service
- provider
- constant

# 总结
基本流程：
使用ng-app 声明为angularjs应用
输入元素可以使用ng-model来绑定到变量
指定div为一个Controller。
在该js文件controller中可以声明$scope的方法 变量
在html文件中 controller内可以调用$scope的方法变量 

# 避免使用全局函数

# ui-router
ui-router 是angular-ui提供的客户端路由框架，解决了原生ng-route的很多不足

- 视图不能嵌套。
- 同一URL下不支持多个视图。


## $stateProvider
stateProvide是$state服务的基础生成服务，用于定义路由规则：url 模板。

## $stateParams
路由内定义的参数可以通过$stateParams对象获取到。

## $urlRouterProvider
$urlRouterProvider则定义了除了stateprovide之外其他的Url规则，也可以指定stateprovider中的url对应的跳转规则。
$urlRouterProvider
    .when('/user/:id','/user/:id/question')
    .otherwise('/home');




## ui-view
路由定义的模板内容不论是template 还是templateUrl 中的内容都必须要放到父路由的ui-view标签下。
定义子路由时，父路由默认也会被载入激活，子路由的模板内容必须要放在父路由的ui-view下，因此父路由的模板必须有ui-view。





## 子路由
父路由必须指定template:"<div ui-view></div>，子路由的templateUrl才可以生效，因为子路由的模板是需要插入到父路由中。




# FAQ
## angularJs中路由跳转后，调用$("#ID").modal('hide')隐藏bootstrap的modal，背景（modal-backdrop)没有消失的问题：
是因为在调用ajax执行完，关闭Modal开始跳转后，替换掉了container中的模板，这样bootstrap找不到了对应的引用，或者说还没有关闭完成就替换掉了container，因此解决这个的方式就是保证替换container之前彻底关闭掉modal，当所有的modal元素都已经关闭删除了再执行跳转（替换模板）。
可以使用：
  $('#myModal').modal('hide')
        .on('hidden.bs.modal',function () {
            $state.go(**);
      });
通过调用.on(hidden.bs.modal)事件，让跳转方法在modal彻底隐藏掉才执行。


## factory和service的区别  
factory和service都是可以作为服务注入的。

- factory : 注入的时候必须提供返回值，factory返回的是一个对象。
- service: service是一个构造器。 被new调用。

注意： 在angularjs中，所有的服务（factory service)都是单例。这就意味着，当注入一个服务到控制器中，修改控制器的方法属性会影响到注入到其他控制器中的服务。因此，可以用各种服务来实现controller之间的通信共享。因此，实际上factory和service本质上并没有多少区别。

## provider
provider是唯一可以传入app.config函数的对象，如果想在使用服务前初始化，就使用provider。 provider必须有一个$get方法用于返回服务的实例化对象。
例如，$state服务是$stateProvider的实例化，$stateProvider用于在config中配置路由，而一些具体的操作方法则可以通过注入$state对象到controller中来获取。
$provider('lcj',function(){
    this.name="lcj"

    //lcj 这个service就是下面的内容。
    this.$get=function(){
        var that=this;
        return {
            name:"ash"
            getname:function(){
                return this.name; // ash
                return that.name //lcj           
            }
        }
    }
})
lcjProvider可以注入到config中，在config中进行初始化。
在controller中可以注入lcj服务。调用起包含的方法或者属性。




注意：注意this关键字的作用域。js的this关键字会依次向上寻找其作用域。getname中的this,作用在return对象中，该对象的this是window。 而lcjProvider中的this,作用域会找到lcj这个function。







## promise编程模式
主要用在异步回调返回值如何让外部调用捕获的问题。例如，外部调用方法如何能够获取到内部ajax方法执行完毕后的返回值。angularjs采用的是一种promise编程机制。使用$q(function(resole,reject){})来注册promise实例。外部即可通过then方法来捕获到返回值，例如：
function test()
{
    return $q(function(resolve,reject){
         setTimeout(function(){
                resolve( 123);            
            },2000);
    });
}

外部：
test().then(function(data){console.log(data)},function(err){console.log(err)});

$http，只要return $http.get()...外部就可以调用。


## angularJs中 使用 bootstrap 的nav的问题。
bootstrap中的导航元素可以方便的做出标签切换的效果。通过在a标签上添加data-toggle="tab"以及href="#ID"的方式来实现，但这样做的话就会跳转到http://URL/#ID。从而扰乱angularjs原本定义的router。因此，一种比较优雅的做法是：彻底放弃bootstrap中导航切换的形式，尽可能少的进行dom操作，通过给各个标签定义新的路由，将对应的模板绑定到父路由的ui-view中即可；
其次，结合ng-class指令，对于active的判断，可以使用$location服务中path()方法，进行比较：
<li  ng-class="{active:isActive('/user/'+state.id+'/question')}"></li>

$scope.isActive = function (viewLocation) {
    return viewLocation == $location.path();
}


##  Circular dependency  （循环依赖）
问题：timeline本身依赖answer服务，里面的vote方法会调用answer服务中的read()方法。 现在，answer服务中增加了一个方法，$http请求后获取到的数据需要传递给timeline服务的data属性。起初的做法是，answer服务也增加一个timeline的依赖，在$http请求完成后将数据直接放入timeline的data属性，结果执行时报出了 circular dependency的错误。  因此此时，timeline依赖了answer，而answer也依赖timeline。

解决方案：
采用加入第三个服务的方式解决依赖问题：增加了一个voteData的服务，专门用于传递data。   answer依赖voteData,将$http后的数据放入voteData的data中。  另一边，timeline也依赖voteData。则可以直接获取voteData里面的数据，完美解决依赖的问题。


## 应用初始化。
例如，应用中的大部分controller都需要获取到当前用户是否登录的状态以及用户名，id，简介等信息。
.config中不可以注入service实例。
因此针对这种情况最好的方式是使用.run()方法和$rootScope.
app.run(function($rootScope,$http){
    $http.success(function(data){
        $rootScope.data = data.data.
     })
})






## 为什么出现子路由获取不到controller的情况



## angularjs中的事件


## 自列表折叠
简单实现：
<a href="javascript:;" ng-click="show=!show">点</a>
<div ng-if="show"></div>
即可


## directive  

- scope隔离   directive里如果return的对象里包含scope{}则会对变量进行隔离，即，未隔离时，directive里的template中变量是controller中scope的变量，一旦隔离后，template中的变量就是这个directive本身的scope。与外界的scope无关
指定scope后，directive和controller里的变量交互的方式就是通过scope内绑定属性，这个绑定是双向的，即directive修改后controller也生效。

- link
主要的行为都要定义在link中，directive执行后即生效，可以类比成controller。



- directive之间是完全一个个独立的，但是服务的实例只有一个，因此如果要实现不同的directive中可以展示不同的数据，则模板的变量不能为service的变量而是要绑定到directive上。

directive可以看做是一个完整的controller和template。拥有自己的模板变量范围。



## ng-if  内 ng-click无法操作ng-if的变量
ng-if会有一个自己独立的scope，可以使用$parent.var；







# JQuery
## 插件
//定义
    (function($){
        $.fn.fnName = function(){

    })(jQuery)
//调用
    $.fn.fnName();
    $(obj).fnName();


## 获取多个input的值
- $("input") .map(function(){return $(this).val();}).get();
- $("input").serialize();

## select显示的元素:
input:checked:visible




