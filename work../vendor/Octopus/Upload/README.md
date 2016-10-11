#### 文件上传标准：

- 文件上传到指定文件夹
- 文件类型和扩展名校验


#### 使用样例：

  1. 通过 composer 安装 SDK

  ```
  创建composer.json文件：
  
  {
      "name": "Octopus/Octopus",
      "repositories": [{"type": "composer", "url": "http://packagist.2345.com/repo/private/"}],
      "require": {
         "php": ">=5.3.0",
         "Octopus/Upload":"0.2"
      },
      "config": {
          "process-timeout": 1800,
          "secure-http" :false
      }
  }
  
  执行命令安装SDK：
  
  composer install
  
  安装完成之后会在当前文件夹生成vendor目录
  
  使用 require 'vendor/autoload.php'; 将composer安装的依赖包引入到项目中
  
  ```
  
  2. 程序使用

  ```
  // 实例化，第1个参数为存储路径，第2个参数为文件访问根URL（可选）
  // 如果目录不存在会自动创建，目录不可写会直接抛出异常
  
  $storage = __DIR__.'/upload';
  $base_url = 'http://img.2345.com';
  
  $file = new \Octopus\Upload($storage, $base_url);
  
  // 获取存储路径
  $path = $file->getStorage();
  
  // 校验条件，可以单独使用，比如可以只校验后缀名
  // 后缀名和MIME都添加的话需要同时满足条件才会通过
  $file->addValidation(array(
		// 校验后缀名
		"extension" => array("gif", "zip", "apk", 'pdf', 'csv'),
		// 校验MIME
		"mime" => array("application/json", "text/html", 'application/pdf', 'text/plain'),
        // 校验文件大小
        "max_size" => "1m", // 可直接传入字节数，或者定义为1k, 1m, 1g, 这三种单位格式
   ));
	
	// 上传操作，
	// 第1个参数为上传的临时文件路径，
	// 第2个参数为上传的文化名，
	// 第3个参数为存储在服务器的文件名，可不设置。文件名可以带文件夹前缀，SDK不会自动创建该前缀文件夹，请自行创建
   $res = $file->upload($_FILES['file']['tmp_name'], $_FILES['file']['name'], 'images/123.gif');
	if (!$res)
	{
		$errors = $file->getErrors();
		
		print_r($errors);
	}
	
	// 获取文件存储路径
	$filepath = $file->getFilepath('images/123.gif');
	
	// 获取文件访问URL
    $urlpath = $file->getURLpath('images/123.gif')
	// http://img.2345.com/images/123.gif 
  ```
  
  
  
  
  
  