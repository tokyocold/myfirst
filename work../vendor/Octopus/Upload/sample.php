<form method="POST" enctype="multipart/form-data">
    <input type="file" name=file value=""/>
    <input type="submit" value="Upload File"/>
</form>

<?php
require_once __DIR__.'/src/Upload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	print_r($_FILES);
	
	$storage = __DIR__.'/upload';
	$base_url = 'http://img.2345.com';
	
	// 
	$file = new \Octopus\Upload($storage, $base_url);
	
	$file->addValidation(array(
			// 校验条件，校验可以单独使用，比如可以只校验后缀名，添加多个条件的校验结果需要同时满足条件才会通过
			"extension" => array("gif", "zip", "apk", 'pdf', 'csv', 'log'),// 校验后缀名
			//"mime" => array("application/json", "text/html", 'application/pdf', 'text/plain'), // 校验MIME
			"max_size" => "1m",
	));
	
	$res = $file->upload($_FILES['file']['tmp_name'], $_FILES['file']['name'], '123.txt');
	if (!$res)
	{
		$errors = $file->getErrors();
		
		print_r($errors);
	}
	
	// 获取文件存储路径
	$filepath = $file->getFilepath('images/123.gif');
	
	var_dump($filepath);
	
	// 获取文件访问URL
	$urlpath = $file->getURLpath('images/123.gif');
	var_dump($urlpath);
	// http://img.2345.com/images/123.gif
}