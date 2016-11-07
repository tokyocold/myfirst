# angular#
## ng-app
初始化一个angularJs应用程序

## ng-model
把一个输入域的值绑定到应用程序 Input/textarea

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


## stateProvide
## ui-view