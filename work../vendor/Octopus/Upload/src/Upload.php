<?php
/**
 * 固定文件保存父级目录版本
 * 只做文件校验和文件转移
 * 
 * @author wangyi <wangyi@2345.com> 2016-04-10
 *
 */
namespace Octopus;

class Upload
{
	protected $validation = array();

	protected $storage;
	protected $base_url;
	protected $errors = array();
	
	/**
	 * 实例化方法，
	 * @param string $project 项目名称
	 * @param string $storage 文件存储相对路径
	 */
	public function __construct($storage='', $base_url='')
	{
		$this->storage = $storage;
		$this->base_url = $base_url;
		
		is_dir($this->storage) or mkdir($this->storage, 0755, true);
		if (!is_writable($this->storage))
		{
			throw new \Exception($this->storage." directory is not writable.");	
		}
	}
	
	/**
	 * 增加校验项
	 * @param array $validation
	 * array(
	 *	"extension" => array("gif", "zip", "apk"),
	 *	"mime" => array("application/json", "text/html"),
	 * )
	 */
	public function addValidation($validation)
	{
		$this->validation = array(
			'extension' => isset($validation['extension']) ? $validation['extension'] : array(),
			'mime' => isset($validation['mime']) ? $validation['mime'] : array(),
			'max_size' => isset($validation['max_size']) ? $this->getSizeUsual($validation['max_size']) : 2*1024*1024, // TODO, get upload_max_filesize from ini ,
		);
	}

	/**
	 * 文件检测
	 * @param string $file
	 * @param string $name
	 * @return boolean
	 */
	protected function isValid($file, $name)
	{
		if(!$this->isUploadedFile($file))
		{
			$this->errors[] = $name." was not uploaded via HTTP POST";
			return false;
		}
		
		$mime = $this->getMime($file);
		$extension = $this->getExtension($name);
		
		if (!empty($this->validation['mime']) && !in_array($mime, $this->validation['mime']))
		{
			$this->errors[] = $name." MIME　{$mime} not allowed";
			return false;
		}
		
		if (!empty($this->validation['extension']) && !in_array($extension, $this->validation['extension']))
		{
			$this->errors[] = $name." extension　{$extension} not allowed";
			return false;
		}
		
		$size = $this->getSize($file);
		if ($size > $this->validation['max_size'])
		{
			$this->errors[] = $file." size　{$size} excceed limit ".$this->validation['max_size'];
			return false;
		}

		return true;
	}
	
	/**
	 * 检测是否是通过HTTP POST上传的文件
	 * @param string $file
	 * @return boolean
	 */
	protected function isUploadedFile($file)
	{
		return is_uploaded_file($file);
	}
	
	/**
	 * 获取Mime
	 * @param string $file
	 * @return mixed
	 */
	protected function getMime($file)
	{
		$finfo = new \finfo(FILEINFO_MIME);
		$mime = $finfo->file($file);
		list($mime_type) = explode(';', $mime);
		return $mime_type;
	}
	
	/**
	 * 获取后缀名
	 * @param string $name
	 * @return mixed
	 */
	protected function getExtension($name)
	{
		return strtolower(pathinfo($name, PATHINFO_EXTENSION));
	}

	/**
	 * 获取文件大小
	 * @param string $name
	 * @return mixed
	 */
	protected function getSize($file)
	{
		return filesize($file);
	}
	
	/**
	 * 获取文件单位的转换格式
	 * @param unknown $file
	 */
	protected function getSizeUsual($size='')
	{
		$size = strtolower($size);
		
		$form = substr($size, -1);
		
		if (is_numeric($form))
		{
			return $size;
		}
		
		$size = substr($size, 0, -1) * pow(1024, stripos('kmg', $form) + 1);
		return $size;
	}
	
	/**
	 * 返回错误信息
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}
	
	/**
	 * 返回存储路径
	 * @return string
	 */
	public function getStorage()
	{
		return $this->storage;
	}

	/**
	 * 返回文件路径
	 * @param string $name
	 */
	public function getFilepath($name='')
	{
		return $this->storage.'/'.ltrim($name, '/');
	}
	
	/**
	 * 返回URL路径
	 * @param string $name
	 */
	public function getURLpath($name='')
	{
		return $this->base_url.'/'.ltrim($name, '/');
	}
	
	/**
	 * 上传操作
	 * @param 文件路径 $file
	 * @param 文件名 $name
	 * @param 新文件名 $new_name
	 * @return boolean
	 */
	public function upload($file, $name, $new_name='')
	{
		$file_name = empty($new_name) ? $name : $new_name;
		
		if ($this->isValid($file, $name))
		{
			$dest = $this->storage.'/'.$file_name;
			if (move_uploaded_file($file, $dest))
			{
				return true;
			}
			$this->errors[] = $dest." move_uploaded_file failed";
			return false;
		}
		return false;
	}
}