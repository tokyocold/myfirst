20160429分享   
	子查询  优化器  依赖式子查询


课题：玩玩Php的多进程实现，与go做比较。结合docker，比较php5 php7

php fast cgi 模式
apache 2.4 使用 fcgid_module
php使用 php_cgi
apachectl -M 当前apache已经加载的模块
a2enmod php7.0  指定apache加载php7.0模块
a2dismod php7.0 指定apache卸载php7.0模块

目前结果是   模块方式加载php的效率要大大优于fast cgi模式


apache配置：
使用fcgid 必须要指定
	Options +Indexes +ExecCGI
否则会报403Forbidden

apache的变量在/etc/apache2/envvars文件中
apache2.4+ 设置目录权限的语句为：
Require all granted 
原来的语句是
Order allow,deny
Allow from all





研究 mpm prework  php-fmp

php-fmp：







nginx配置：
层级：http->server->location


服务器文件夹权限设置：
先查看nginx/apache启动后的进程用户
ps aux|grep apache

可以将目录的组设置为进程的用户。目录的用户设置为执行用户（lcJ)










目标 个人博客



php扩展安装
安装php扩展，可以直接尝试apt-cache找一下有没有对应的软件包，如果没有或者安装包有问题，可以自己编译安装，自己编译安装并不复杂，主要用phpize这个工具，phpize数据php-dev这个软件，安装后就可以使用phpize，二进制库可以在 http://pecl.php.net/中搜索下载：
$ cd extname
$ phpize
$ ./configure
$ make
# make install
执行完成后，在php.ini中添加extension=extname.so重启即可。
整体来说很简单。



----------------------------------------------------------------------------------------------
0        composer autoload
1   laravel / thinkPHP
2          数据库架构
3          负载均衡
4    diy建站
5         单元测试

Q:
apache documentRoot设置为代码目录的子目录

Q:
Exception thrown without a stack frame in Unknown on line 0
A:
如果用自定义的错误函数代替了原来的错误处理函数，而自定义的错误处理函数也出现了错误，则会报出这个奇怪的错误，注释掉原有的错误处理函数即可。
set_exception_handler();

Q:PDO调试问题，以及详细操作。

----------------------------------------------------------------------------------------------


ModernPHP  

trait  和  多重继承？
多重继承 ：一个类继承多个父类
非多层继承


设计模式：
原型模式，主要用clone关键字。用于大对象的重复创建。
第一步：创建原型，并初始化
$pro = new canvas();
$pro -> init();
第二步： 克隆对象
$canvas1 = clone $pro;
$canvas1 -> draw();

$canvas2 = clone $pro;
$canvas2 -> draw();

装饰器模式，动态的添加修改类功能
传统方法，一个类提供一项功能，如果要扩展这个类，修改或者添加类方法，传统的做法是用一个子类继承他，重新实现类的方法



框架：
框架运行流程：
mvc 基本都是采用单入口方式，单入口方便路由。
入口文件->定义常量->引入函数库->自动加载类->启动框架->路由解析->加载控制器->返回结果


#父类方法返回子类实例：PHP延迟静态绑定#
主要在单例模式中应用，如果返回静态变量的方法在父类中，如果父类使用new self() 或者__CLASS__ ，返回的都是父类对象而并非子类。要想返回子类，使用new static()即可；
class baseModel{
	static function getInstance()
	{
		return self(); // 这个返回的是baseModel类，并非继承的子类
		return static(); //返回继承父类的的子类对象	User
	}
}
class User extend baseModel
{
	
}

#get_class()\get_called_class()\__CLASS__区别#
get_class (): 获取当前调用方法的类名；   （父类）
get_called_class():获取静态绑定后的类名； （调用类，子类）
__CLASS__: 当前所处的类     （父类）
因此，在父类方法中，将单例绑定到静态变量时，所选择的key应该是子类，即get_called_class();


#composer autoload设定#
满足psr-4规范的程序，可以在composer.json中添加autoload字段


#rowspan colspan无效问题#
rowspan不会跨越thead/tbody/tfoot作用, 所以要分开写
   <table>
        <thead>
            <tr>
                <th rowspan="3">header0</th>  #不会对tbody生效
                <th>header2</th>
                <th>header3</th>
                <th>header4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="3"></td>
                <td>item1</td>
                <td>item2</td>
                <td>item3</td>
            </tr>

        </tbody>
    </table>


#laravel#
##php artisan##
laravel下 的一个重要的特色就是使用php artisan命令来完成很多工作，
例如  make:migration   make:model 等。

##数据映射模式##
laravel的Eloquent ORM是基于数据映射模式建立的。
默认规则是模型类名的复数作为与其对应的表名，除非在模型类中明确指定了其它名称
例如  User类  对应的表名 users
可以使用 php artisan make:model User --migration /-m 来建立model和表


##依赖注入and控制反转##
- 依赖注入：
类似于我们不能把数据库参数直接写到一个model中，而是引入一个config文件：
第一阶段：
config:
array('host'=>localhost,'username'=>'ash','password'=>123456);
----------------------model-----------------------
class model{
function __construct()
{
	mysql_connect($_config('host'),$_config('username')....)一样
}
这样我们可以通过修改config文件而减少避免出现万一修改密码时需要修改所有的model文件。
依赖注入就是这么个情况，将所依赖的类注入到model中，而避免在model中实例化。

目前的情况，我不想使用Mysql，而想使用redis/Mongodb其他的数据存储方式，问题就出现了。我不得不修改所有的Model中 mysql_connect，替换为新的数据存储类型：
因此，我们可以引入接口：
interface IData
{
	public function get();
	public function save();
	....
}

class model{
	private $dataHandler;
	//一种方式，采用构造函数传入来构造。	
	function __construct(IData $handler)
	{
		$this->dataHandler = $handler;
	}
	
	//另一种方式，setter函数
	function setterDb(IData $handler)
	{	
		$this->dataHandler = $handler;
	}

	
	function save()
	{
		$this->dataHandler->save();
	}
	......
}
class mysql implements IData{
	.....
}
class redis implements IData{
	.....
}

$data = new mysql()
//替换
//$data = new redis();
$model = new model($data);

这样就实现了依赖注入，避免了反复修改多个model类内容（如果有多个model，也需要增加多个new ***()，可以增加一个工厂类，返回一个Data 类型 ，所有的model实例化都传入 Factory::returnDb();）；


ok，截止目前一切看上去都很不错。但是，在一个model类中，除了会使用db之外，还可能会有其他乱七八糟的依赖，例如文件存储/日志处理 等等等等的。
这样，我们就需要不断增加__construct()或者增加setter方法。
因此，每次调用model之前，我们都不得不这样：

$data = new mysql();
$file = new file()
$log = new log();
$model=new model();
$model->setterDb($data);
$model->setterFile($file);
$model->setterLog($log);
......

只有做完这些工作，我们才能正常的使用这个Model。即，model类被所依赖的其他类（db/file/log）的生成所控制，必须先建立依赖类才能使用Model，看上去当然是还存在着耦合，因此，这里就用到了控制反转：
//inversion of control
class IOC
{
	$register=array();

	function bind($name,$val){
		$register[$name] = $val;
	}

	function get($name)
	{
		return $register[$name];
	}
}
$ioc = new IOC();
$ioc->bind("data",function(){
	return new mysql();
});
$ioc->bind("file",function(){
	return new file();
});


class model{
	function save()
	{
		$ioc = new IoC();
		$ioc->get("data")->save();
	}
}
$model = new model();
$model->save();


这样，就实现了控制反转，model类不必在data类实例好之后才能使用。只要在最初将所有需要用到的依赖类绑定到 Ioc上面。这样不仅解除了依赖，甚至在不需要用到save()的时候都不会实例化依赖类，节省了资源。

现在在回想angular的依赖注入，就明白其真正的意义了。


##匿名函数##
赞赞赞
php5.3后引入的。很强大！
可以将匿名函数绑定到变量上。
$a = function(){return 'aaa'}
可以作为回调使用
function call($a,$callback)
{
	$callback();
}
##instanceof关键字##
类运算符，可以确定一个php变量是否属于一个类的实例。
if($classback instanceof Closure)
...



## 错误处理 ##
关于自定义错误处理函数：
set_error_handler();
这个函数只能作用于E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE，即用户自定义的错误。PHP核心错误是无法生效的。
如果需要捕获php核心层面的错误，需要使用：
register_shutdown_function("errorHandler");
function errorHandler()
{
    $arrStrErrorInfo = error_get_last();//获取系统错误
     print_r( $arrStrErrorInfo );//处理错误
}
使用register_shutdown_function来处理。

关于xdebug的trace无法使用的问题？？？
单步

使用PHPSTORM和XDEBUG调试，只要配置好php.ini，phpStorm里Php,xdebug设置正确即可。
浏览器访问时记得加上 XDEBUG_SESSION_START参数！！！切记切记




## 关于电商抢购出现超库存问题##
两种思路：
1 使用前端内存，在执行数据库前提前操作内存库存减1

2 消息队列： 
简单说，就是将用户请求放入缓存中，例如把请求用户的id放入缓存队列中，后台增加一个守护进程，pop队列一个个处理，同时客户端增加一个定时的ajax请求轮询访问返回结果。

可以看到  使用队列需要增加一个守护脚本，客户端定时请求。所以个人倾向于使用前段内存。












#Eloquent中 多对多的操作#
ps：Eloquent确实有意思！
多对多关系中，除了两张表（admins/groups），必然还有一张存储这两张表关系的关系表。(admin_group)
在各自的model中需要确立对应关系，可以在Admin Model中增加一个groups方法：
    public function groups()
    {
        return $this->belongsToMany('\App\Admin')
            ->withPivot('vote')
            ->withTimestamps()
            ;
    }
注意：此时调用$admin->groups()返回的实际上是group对象，所以一切delete update操作都是针对group对象。
如果要操作中间表（admin_group)则可以使用   $admin->groups()->first()->pivot->delete()   pivot属性
来获取中间表对象。


## 三张表以上的对应关系建立：
在实现问题详情是遇到的问题： 如果快速的获取一个问题下的所有回答，以及回答相关的用户。
三张表： question     question hasmany answers
        answer      answer belongsTo user and answer belongstoMany users 
         user
上面是三张表以及对应关系。目的是直接获取到question集合包含answers 以及每一个answer对应的user和users。
两种方式：
    -  分布获取  先获取question question->find(id)  再获取回答  answers->with('user','users')->where(['question_id'=>id])
    -  直接获取   当三张表的对应关系已经建立好以后，其实可以很方便的一步获取到结果：
                question->with('answers','answers.user','answers.users')->find(id)
                  这样获取到的结果以及包含了所有需要的集合。






##debug Sql##
如果要查看 Orm对象实际上执行的SQL 可以使用 $user->where()->toSql();
或者 DB::getQueryLog()；
使用 DB::getQueryLog()前记得要先执行：
DB::connection()->enableQueryLog();

##collection对象##
合并两种不同类型的collection对象时，如果两个collection对象里有相同id的成员，后一个将会覆盖前面的元素，
为了避免这种情况，可以将collection对象先转为array toArray().
然后使用array_merge()方法合并。如果需要排序 使用 array_multisort；

##pivot (中间表对象）##
默认情况下只有模型键(XX_ID)才能用在pivot上,如果要使用其他属性，必须在定义关联关系时进行指定：
        return $this->belongsToMany('\App\Admin')   //基础的多对多关系
            ->withPivot('vote')                     //增加中间表vote
            ->withTimestamps()                      //增加中间表时间戳


##关系表字段返回
建立对应关系后（belongsTo/belongsToMany），查询时使用with，默认会返回关系表内的所有字段，例如question_ins()::with('user')会返回user的所有字段。可以使用->select来限定字段。
return $this->belongsTo('\App\User')->select(array('id','usernname'));

## 关系表的关系表返回
$this->with('answers','answers.user','answers.users','comments')


##attach/detach##
更新多对多中间表中的数据记录。



## keyby只能作用在colection上面，因此必须使用$Model->get()而不能使用DB::(table)
$collections->pluck("field")可以返回合集的指定字段组成的数组。




##php自带的服务器##
php -S localhost:8000 -t ./public
可以使用php自带的服务器快速在一个指定目录下搭建服务器环境
-t 入口文件所在的目录，laravel的入口目录为public

##composer镜像##
可以使用国内全量镜像
可以对系统全局配置/单个项目配置


##migration##
数据库版本控制

##mysql 连接出错  ##
报错：Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' 
排查mysql error log。
使用 ps aux|grep mysql 查看 --log-error 参数 或者error log位置。
根据error log里的内容首先排查。

查看mysqld状态  service mysql.server status 是正常启动的。连不上原因就说丢失了sock文件
查找mysqld.sock文件。 find / -name mysqld.sock
找到文件 /run/mysqld/mysqld.sock   发现是/tmp/mysql.sock的软链接
建立软链接：
ln -s /tmp/mysql.sock /var/run/mysqld/mysqld.sock
问题解决。


##建表##
使用 mysqldump导出的表  第一句都是：
drop table if exists 'tablename'

这样会导致其他人环境数据丢失，这就是使用migration的原因
首先创建一个migration:
php artisan make:migration create_table_users --create=users
接下来在migration中完善字段。


##session##
出现了session失效的问题，google,在$middleware中添加：
\Illuminate\Session\Middleware\StartSession::class 解决，至于原因。。臣妾不知道啊！！
A： 应该是在5.2之后，中间件被分为了全局中间件$middleware和中间件组$middlewareGroup,
StartSession这个中间件并没有注册在全局中而是在中间价组中。其key值为'web',
因此，只要在routes.php中将原有的请求应用上web中间件组或者将sessionstart注册在全局中间件组即可。


##中间件##
可以理解为 一个http请求 到达目的动作之前的层，没一层都可以检查请求并且完全拒绝它。

##pdoexception##
could not find driver  
原因 没有安装php5-mysql扩展

#Cookie以及Session#
session的存在是依赖于cookie的，session是通过cookie传递的PHPSESSID来获取服务器的session。因此，无论单独存储cookie或者phpsession都是非常危险的做法。
安全起见，无论存储session id 还是 uid username 等信息 都需要增加一个签名 sign 来确保当前的cookie确实是真实用户而非伪造的cookie信息。
经过观察，悟空后台仅仅是通过判断是否存在$_SESSION['adminid']即判断管理员是否登陆，因此，将自己的sessionid在新的浏览器中创建对应cookie即获取到了登陆状态
积分则多了一层签名校验，除了基础用户信息之外，额外保存了一个服务器生成的签名，该签名使用服务器存在的 密钥  和 用户信息组合生成，因此当有人伪造cookie时，如果不知道服务器密钥以及
加密方式，则没办法拿到正确的签名，服务器如果校验签名不通过，则认为该cookie信息为伪造的，将$_COOKIE置空即可。

##延伸：##
高访问量网站，拥有多台web服务器时，使用cookie作为用户的校验，而不是通过session id来获取服务器的session文件储存的用户信息，不失为给好方式。避免了多服务器session同步的问题。
Q: jifen的Session 同步如何实现


#::class#
php 5.5开始,使用 ClassName::class 你可以获取一个字符串，包含了类 ClassName 的完全限定名称。这对使用了 命名空间 的类尤其有用。
NS\ClassName

#phpStorm 快速输入#
class .class + Tab 快速生成
id    #id + tab

#xunsearch#
##搜索建议没有结果，以及getExpandedQuery没有结果的问题##
经过排查，问题出在没有强制刷新日志：
util/Indexer.php --flush-log demo
没有经过这一步的话，db/demo文件夹下不会有logdb目录。

## 关于分词
自定义分词，我希望把 支付宝 作为一个词语，而不是分为 “支付” “宝” 这种，除了要在dict_user中加入 支付宝 ，还要在搜索是指定：
$searchObj->setScwsMulti(0)->setQuery($key)->search();
即可。 CNM；



# thinkphp

## composer显示指定包信息：composer show -a laravel/laravel

## 配置
### 状态配置：
入口文件指定 define('APP_STATUS','office');
对应的配置文件：Application/Common/Conf/office.php

入口文件：define('APP_STATUS','home');
对应的配置文件：Application/Common/Conf/home.php

### 读取配置：
C("URL_MODEL");
二维配置：
C('USER_CONFIG.USER_TYPE');

### 动态设置：
C("DATA_CACHE_TIME",60);

### 扩展配置
'LOAD_EXT_CONFIG'=>'user,db'
位置：Application/Common/Conf/user.php  Application/Common/Conf/db.php


## 架构
### 模块化
- 自动生成模块 控制器 模型 
\Think\Build::buildController("Admin","User);
\Think\Build::buildModel("Admin","User);
- 禁止访问
 'MODULE_DENY_LIST'      =>  array('Common','Runtime','Api'),
- 允许访问
'MODULE_ALLOW_LIST'    =>    array('Home','Admin','User'),
'DEFAULT_MODULE'       =>    'Home',

- 多入口设计：

### URL
http://serverName/index.php/模块/控制器/操作
- 大小写
'URL_CASE_INSENSITIVE'  =>  true,    不区分大小写
- 普通模式
- pathInfo模式
http://localhost/index.php/home/user/login/var/value/
path模式下依然可以使用普通模式：
http://localhost/index.php/home/user/login?var=value
- rewrite模式

### 多层MVC 
- Model层
默认的模型是Model类构成，随着项目增大可以采用多层Model，在模块下创建
Model Logic Service这些目录。把对用户表操作分成三层。

UserModel UserLogic UserService 统一继承Model类即可。
D('User','Logic')   实例化UserLogic
D('User','Service') 实例化UserService

- View层 
默认视图层设置： 默认view
'DEFAULT_V_LAYER'       =>  'Mobile',

- Controller层

### CBD模式
#### behavior
- 标签位 
 例如 app_init app_begin app_end等
自定义标签：
tag('my_tag'); // 添加my_tag 标签侦听
// 下面的写法作用一致
\Think\Hook::listen('my_tag');

- 行为定义
行为类必须定义执行入口方法run

- 行为绑定
建立tags.php，内容：
return array(
     '标签名称1'=>array('行为名1','行为名2',...), 
     '标签名称2'=>array('行为名1','行为名2',...), 
 );

- 单独执行
B方法
B('Home\Behavior\AuthCheck')


##  路由
### 路由定义
- 开启
'URL_ROUTER_ON'   => true, 
- 规则 
定义在模块目录（Admin)下conf内：
'URL_ROUTE_RULES' => array(
    'page/:id'=>'Index/read?id=:1'  //找Admin模块下控制器/操作/参数
)
全局路由（Common)下：
'URL_ROUTE_RULES' => array(
    'page/:id\d'=>'Admin/Index/read'  //要指明模块名 参数限制数字
)

可以传入额外参数：
'blog/:id'=>'blog/read?status=1&app_id=5',

- 路由参数：
'page/:id'=>array('Admin/Index/read','id=:1',array('ext'=>'html')),  //限制后缀为html
array('method'=>'get')  //限制只有GET请求
array('callback'=>'checkfun'), 自定义checkFun函数检测，返回false表示不生效

### 规则路由
参数中以“：”开头的表示动态参数，对应一个GET参数，
'page/:id'=>'Admin/Index/read'
中$_GET['id']获取:id.

### 闭包支持
'pic/:id'=>function($id){
    echo '哈哈哈 这是闭包'.$id;
    $_SERVER['PATH_INFO'] = 'Index/read'; //继续执行，默认执行完闭包后不会继续
    return true;
}



##控制器
### 定义
- 后缀 
'ACTION_SUFFIX'         =>  'Action'
public function listAction(){}
后缀只影响类的定义不影响Url

- 控制器实例化
$user = new \Home\Controller\UserController;
or
$user = A("User");

### 前置后置操作
_before_funcname()
_after_funcname()

### 参数绑定
默认开启  'URL_PARAMS_BIND'       =>  true
按照变量顺序绑定 'URL_PARAMS_BIND_TYPE'  =>  1, 

### 伪静态
'URL_HTML_SUFFIX' => 'html|shtml|xml' 
？ pathinfo问题

### URL生成
U()方法
U('Index/say',array('id'=>1),'','localhost:8000');

### ajax返回
$this->ajaxReturn($data,['jsonp']);

### 跳转&重定向
- 跳转
$this->success('成功','/Admin/Index/index',5);
$this->error('error','/Admin/Index/index',5);
模板
TMPL_ACTION_ERROR
TMPL_ACTION_SUCCESS
使用项目内部模板文件：
'TMPL_ACTION_SUCCESS' => 'Public:success' 
内部view须有public/success.html

-重定向
$this->redirect()
redirect()

### 输入变量
I()方法
I('变量类型.变量名/修饰符',['默认值'],['过滤方法'],['额外数据源'])
- 变量过滤
'DEFAULT_FILTER'        => 'htmlspecialchars'
I('get.name') == htmlspecialchars($_GET['name'])
第三个参数
I('post.email','',FILTER_VALIDATE_EMAIL);
调用php 内置的filter_val（）

- 变量修饰符
I('get.id/d');
修饰符有： s d b a f

### 请求类型
内置常量：
IS_GET IS_POST IS_AJAX

### 空操作
找不到请求方法会定位到  
_empty()

### 空控制器
找不到制定控制器会定位到
EmptyController

### 插件控制器
VAR_ADDON => 'addon' 


## 模型
### 定义
约定对应关系：
- UserModel -> think_user
- UserTypeModle -> think_user_type

tp中，关于数据表名称的属性：
- tablePrefix：前缀，未定义获取配置中的DB_PREFIX
- tableName :不包含前缀的表名
- trueTableName: 包含前缀的表明
- dbName : 当前模型对应的数据库和配置文件不一致时定义

模型必须有对应的数据表。
### 实例化
- 直接实例化：
$New  = new \Home\Model\NewModel('blog','think_',$connection);
$connection 数据库连接信息：
    - 字符串定义
    - 数组定义
    - 配置定义：
    默认配置参数：
    'DB_TYPE'      =>  '',     // 数据库类型
    'DB_HOST'      =>  '',     // 服务器地址
    'DB_NAME'      =>  '',     // 数据库名
    ...

- D方法实例化
类不存在，则实例化公共模块下类，若不存在，则实例化\Think\Model基类

- M方法实例化
D是实例化具体的模型类，如果仅仅是对表做CURD操作，则使用M实例化
M实例化不能调用模型类的具体方法。 只能做数据操作

- 实例化空模型类
$Model = M();
仅仅是用原生SQL查询可使用空模型类

###字段定义
缓存位置：Runtime/Data/_fields/
DB_FIELDS_CACHE => false可以关闭字段自动缓存，debug模式下默认为关闭
部署模式下修改了表结构需要清空缓存

### 连接数据库
- 配置文件定意思
- 模型类定义
如果模型内定义了connection属性，则实例化时会使用定义的信息。

### 切换数据库
Model->db("数据库编号","数据库配置");

### 分布式数据库支持
'DB_DEPLOY_TYPE'=> 1, //分布式支持
'DB_RW_SEPARATE'=>true, //读写分离

### 连贯操作
除了select方法放到最后（select并不是连贯操作方法），其他的连贯方法没有先后顺序。

### 命名范围
类定义_scope属性，使用scope连贯操作方法调用。


### CURD操作
#### 数据创建
$User = M('User');
$User->create(); //根据表单提交$_POST数据自动创建对象

create的第二个参数可以制定操作状态。指定后，就可以执行令牌验证，自动验证，自动完成等功能。
没有调用add,save方法之前数据都在内存可以改变对象。
Data()简单创建一个数据对象不进行其他操作。
$User->data();
add(),save()操作时会自动过滤不存在的字段以及非法数据类型的数据，因此不用担心非法数据导致的SQL错误问题

#### 数据写入
add()方法写入，replace参数true表示覆盖，false为默认
如果已经使用create,data创建了对象，则add就不需要传入数据了。。
不执行SQL而是返回：
$User->fetchSql(true)->add($data);

#### 数据读取
- 读取一行数据 find()方法
可以调用data()方法获取查询后的结果
- 读取多行数据 select()方法
- 获取某个列的数据 getField方法

#### 数据更新
save()方法：
$user->where('id=1')->save($data);
如果没有where且数据本身不包含主键字段，则save不会更新任何记录。

setField 更新字段。
setInc seDec 更新统计字段。

延迟更新？？

#### 数据删除
delete()方法
delete(5)删除主键为5的数据
delete(1,2,5) //del主键 1,2,5数据
没有传入任何条件则不会删除。除非
$User->where('1')->delete();


### 字段映射
模型中定义_map属性，表单即可使用映射的字段作为表单名提交：
  protected $_map = array(
 'name' =>'username', // 把表单中name映射到数据表的username字段
 'mail'  =>'email', // 把表单中的mail映射到数据表的email字段
     );
获取数据时并不会自动映射为新字段名，如果要使用自动处理：
'READ_DATA_MAP'=>true


### 查询语言
- 查询方式：
1 字符串作为查询条件：
    $User->where('type=1 AND name="123"')->select();

2 使用数组作为查询条件
3 使用对象来查询：
可以使用内置的stdClass()对象

使用数组或对象查询时，传入不存在的对象会被自动过滤
    
- 表达式查询
$data['字段'] = array('表达式','查询条件')
$data['id'] = array('eq',100);
$data['id'] = array('elt',100); //小于等于
$data['id'] = array('notlike',100);
'DB_LIKE_FIELDS'=>'title|content' 设置字段自动模糊查询
$map['title'] = 'thinkphp'; //查询条件  title like '%thinkphp%'
$map['id']  = array('between','1,8');
$map['id']  = array('not in','1,5,8');
EXP表达式：
$data['score'] = array('exp','score+1');// 用户的积分加1
$User->where('id=5')->save($data); // 根据条件保存修改的数据


- 统计查询
$User = M("User")
$User->count()
$User->max("score")
$User->min("score")
$User->avg("score")
$Uesr->sum("Score')
所有的统计查询均支持连贯操作的使用


- SQL查询
1 query方法，用于执行查询操作：
$Model = new \Think\Model();//可以只实例化Model，没有对应任何表
$Model->query(...);


2 execute方法，执行写入操作的SQL

- 动态查询
getBy 如果表包含Id,name,email 则可以
getByName getByEmail 查询

getFieldBy 根据字段查询
getFieldByName("lala","id")  根据name或者用户的Id

- 子查询
分两步：
1 子查询不执行SQL，只是构建
$subQuery = $model->where()->select(false);
or
$subQuery = $model->where()->buildSql();

2 构建最后的SQL
$model->table($subQuery.' a')->where()->order()->select() 




























































# collection
## preg_replace_callback
php7中替代了preg_replace('/。。。/e','function',str)
的形式，e在7中被废弃

其中第二个参数可以为一个匿名函数，当需要使用外部参数时可以使用 preg_replace_callback('//',function($matches)use($val){},str);
注意，如果需要修改$val的值，则可以加上&符号。。





