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

##debug Sql##
如果要查看 Orm对象实际上执行的SQL 可以使用 $user->where()->toSql();
或者 DB::getQueryLog()；
使用 DB::getQueryLog()前记得要先执行：
DB::connection()->enableQueryLog();




##attach/detach##


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







