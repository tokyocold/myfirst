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




研究 mpm prework  php-fmp

php-fmp：

