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


#建表#
使用 mysqldump导出的表  第一句都是：
drop table if exists 'tablename'

这样会导致其他人环境数据丢失，这就是使用migration的原因
首先创建一个migration:
php artisan make:migration create_table_users --create=users
接下来在migration中完善字段。







