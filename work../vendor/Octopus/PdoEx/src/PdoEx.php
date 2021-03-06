<?php

/**
 * Copyright (c) 2015,上海二三四五网络科技股份有限公司
 * 文件名称：PdoEx.php
 * 摘    要：PDO封装类
 * 作    者：张小虎
 * 修改日期：2015.07.08
 */

namespace Octopus;

use PDO;
use PDOStatement;
use Octopus\Logger\Registry;

class PdoEx
{

    private static $instances = array();
    private $dbname, $config, $dbW, $dbR, $isTransaction = false, $logger = null, $logErrors = array();

    /**
     * 构造函数，设置配置
     * @param string $dbname
     * @param array $config
     */
    private function __construct($dbname, $config)
    {
        $this->dbname = $dbname;
        $this->config = $config;
    }

    /**
     * 获取单例实例
     * @param string $dbkey 实例标识
     * @param array $config
     * @return PdoEx
     */
    public static function getInstance($dbkey, $config)
    {
        if (isset($config['dbname']))
        {
            $dbname = $config['dbname'];
        }
        else
        {
            $dbname = $dbkey;
        }
        if (!isset(self::$instances[$dbkey]))
        {
            self::$instances[$dbkey] = new static($dbname, $config);
        }
        return self::$instances[$dbkey];
    }

    /**
     * 删除单例实例
     * @param string $dbkey 实例标识
     */
    public static function delInstance($dbkey)
    {
        if (self::$instances[$dbkey])
        {
            self::$instances[$dbkey]->dbW = null;
            self::$instances[$dbkey]->dbR = null;
            self::$instances[$dbkey] = null;
        }
    }

    /**
     * 获取可写db
     * @return PDO
     */
    public function getWritableDB()
    {
        if (!$this->dbW)
        {
            $dsn = 'mysql:host=' . $this->config['master']['host'] . ';port=' . $this->config['master']['port'] . ';dbname=' . $this->dbname . ';charset=' . $this->config['charset'];
            $username = $this->config['username'];
            $password = $this->config['password'];
            $options = array(
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->config['charset'],
            );
            $this->dbW = new PDO($dsn, $username, $password, $options);
            if (isset($this->config['errmode']))
            {
                $this->dbW->setAttribute(PDO::ATTR_ERRMODE, $this->config['errmode']);
            }
        }
        return $this->dbW;
    }

    /**
     * 获取可读db
     * @return PDO
     */
    public function getReadableDB()
    {
        if (!isset($this->config['slave']))
        {
            return $this->getWritableDB();
        }
        else
        {
            if (!$this->dbR)
            {
                if (array_keys($this->config['slave']) !== range(0, count($this->config['slave']) - 1))
                {
                    $slave = $this->config['slave'];
                }
                else
                {
                    $slave = $this->config['slave'][array_rand($this->config['slave'])];
                }
                $dsn = 'mysql:host=' . $slave['host'] . ';port=' . $slave['port'] . ';dbname=' . $this->dbname . ';charset=' . $this->config['charset'];
                $username = $this->config['username'];
                $password = $this->config['password'];
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->config['charset'],
                );
                $this->dbR = new PDO($dsn, $username, $password, $options);
                if (isset($this->config['errmode']))
                {
                    $this->dbR->setAttribute(PDO::ATTR_ERRMODE, $this->config['errmode']);
                }
            }
            return $this->dbR;
        }
    }

    /**
     * 插入函数
     * @param string $table 表名
     * @param array $data 插入的键值对数组
     * @param boolean $returnLastInsertId 是否返回最后插入的ID
     * @return boolean|int 是否成功|最后插入的ID|影响行数
     */
    public function insert($table, $data, $returnLastInsertId = true)
    {
        $columns = array_keys($data);
        $sql = "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES (:" . implode(", :", $columns) . ")";
        $params = array();
        foreach ($data as $column => $param)
        {
            $params[":$column"] = $param;
        }
        $result = $this->query($sql, $params, false, true);
        if ($result && $returnLastInsertId)
        {
            return $this->lastInsertId();
        }
        else
        {
            return $result;
        }
    }

    /**
     * 更新函数
     * @param string $table 表名
     * @param array $data 更新的键值对数组
     * @param string|array $condition 条件
     * @return boolean|int 是否成功|影响行数
     */
    public function update($table, $data, $condition)
    {
        $condition = $this->preCondition($condition);
        $columns = array_keys($data);
        foreach ($columns as $key => $column)
        {
            $columns[$key] = "`$column` = :$column";
        }
        $sql = "UPDATE `$table` SET " . implode(',', $columns) . " WHERE {$condition['where']}";
        $params = array();
        foreach ($data as $column => $param)
        {
            $params[":$column"] = $param;
        }
        $params = array_merge($params, $condition['params']);
        return $this->query($sql, $params, false, true);
    }

    /**
     * 删除函数
     * @param string $table 表名
     * @param string|array $condition 条件
     * @return boolean|int 是否成功|影响行数
     */
    public function delete($table, $condition)
    {
        $condition = $this->preCondition($condition);
        $sql = "DELETE FROM `$table` WHERE {$condition['where']}";
        $params = $condition['params'];
        return $this->query($sql, $params, false, true);
    }

    /**
     * 批量插入函数
     * @param string $table 表名
     * @param array $columns 列数组
     * @param array $data 行数组
     * @return boolean|int 是否成功|影响行数
     */
    public function batch($table, $columns, $data)
    {
        $values = array();
        $bindValues = array();
        foreach ($data as $rowKey => $row)
        {
            $value = array();
            foreach ($columns as $colKey => $column)
            {
                $value[] = ":{$column}$rowKey";
                $bindValues[":{$column}$rowKey"] = $row[$colKey];
            }
            $values[] = "(" . implode(", ", $value) . ")";
        }
        $sql = "INSERT INTO `$table` (`" . implode("`, `", $columns) . "`) VALUES " . implode(", ", $values);
        return $this->query($sql, $bindValues, false, true);
    }

    /**
     * 执行函数
     * @param string $sql sql语句
     * @param array $params 参数
     * @param boolean $useReadableDB 是否使用从库
     * @return boolean|int|PDOStatement 是否成功|影响行数|PDOStatement
     */
    public function query($sql, $params = array(), $useReadableDB = false, $returnRowCount = false)
    {
        if ($useReadableDB && !$this->isTransaction)
        {
            $db = $this->getReadableDB();
        }
        else
        {
            $db = $this->getWritableDB();
        }
        $stmt = $this->execute($db, $sql, $params);
        if ($returnRowCount)
        {
            return $this->rowCount($stmt);
        }
        else
        {
            return $stmt;
        }
    }

    /**
     * 获取影响行数
     * @param PDOStatement $stmt
     * @return boolean|int 是否成功|影响行数
     */
    public function rowCount($stmt)
    {
        if ($stmt instanceof PDOStatement)
        {
            return $stmt->rowCount();
        }
        else
        {
            $this->logError(null, "\$stmt is not PDOStatement");
            return false;
        }
    }

    /**
     * 获取一条记录
     * @param PDOStatement $stmt
     * @return boolean|array 是否成功|行数组
     */
    public function fetch($stmt)
    {
        if ($stmt instanceof PDOStatement)
        {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        else
        {
            $this->logError(null, "\$stmt is not PDOStatement");
            return false;
        }
    }

    /**
     * 获取所有记录
     * @param PDOStatement $stmt
     * @return boolean|array 是否成功|多行数组
     */
    public function fetchAll($stmt)
    {
        if ($stmt instanceof PDOStatement)
        {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        else
        {
            $this->logError(null, "\$stmt is not PDOStatement");
            return false;
        }
    }

    /**
     * 查找单条记录
     * @param string $sql sql语句
     * @param array $params 参数
     * @param boolean $useWritableDB 是否使用主库
     * @return array|boolean 是否成功|行数组
     */
    public function find($sql, $params = array(), $useWritableDB = false)
    {
        $stmt = $this->query($sql, $params, !$useWritableDB);
        return $this->fetch($stmt);
    }

    /**
     * 查找所有记录
     * @param string $sql sql语句
     * @param array $params 参数
     * @param boolean $useWritableDB 是否使用主库
     * @return array|boolean 是否成功|多行数组
     */
    public function findAll($sql, $params = array(), $useWritableDB = false)
    {
        $stmt = $this->query($sql, $params, !$useWritableDB);
        return $this->fetchAll($stmt);
    }

    /**
     * 查找限定记录
     * @param string $sql sql语句
     * @param array $params 参数
     * @param int $limit
     * @param int $offset
     * @param boolean $useWritableDB 是否使用主库
     * @return array|boolean 是否成功|多行数组
     */
    public function findList($sql, $params = array(), $limit = 0, $offset = 0, $useWritableDB = false)
    {
        if ($limit > 0)
        {
            if ($offset > 0)
            {
                $sql .= " LIMIT :offset, :limit";
                $params[":offset"] = array($offset, PDO::PARAM_INT);
                $params[":limit"] = array($limit, PDO::PARAM_INT);
            }
            else
            {
                $sql .= " LIMIT :limit";
                $params[":limit"] = array($limit, PDO::PARAM_INT);
            }
        }
        return $this->findAll($sql, $params, $useWritableDB);
    }

    /**
     * 获取最后插入的id
     * @return string 最后插入的ID
     */
    public function lastInsertId()
    {
        $db = $this->getWritableDB();
        return $db->lastInsertId();
    }

    /**
     * 开启事务
     */
    public function beginTransaction()
    {
        $db = $this->getWritableDB();
        $return = $db->beginTransaction();
        $this->isTransaction = true;
        return $return;
    }

    /**
     * 回滚事务
     */
    public function rollBack()
    {
        $db = $this->getWritableDB();
        $return = $db->rollBack();
        $this->isTransaction = false;
        return $return;
    }

    /**
     * 提交事务
     */
    public function commit()
    {
        $db = $this->getWritableDB();
        $return = $db->commit();
        $this->isTransaction = false;
        return $return;
    }

    /**
     * 统一执行SQL语句
     * @param type $db pdo对象
     * @param type $sql sql语句
     * @param type $params 参数
     * @return boolean|PDOStatement 是否成功|PDOStatement
     */
    private function execute($db, $sql, $params)
    {
        if (preg_match("/['\"#]/is", $sql, $matches))
        {
            $this->logError(null, "$sql 含有非法字符[{$matches[0]}]！");
            return false;
        }
        if (preg_match("/[=<>]+\s*(?![:\?`=<>\s]|([\w\d]+\s*\([^\)]*\)))/is", $sql))
        {
            $this->logError(null, "$sql 含有未绑定的参数！");
            return false;
        }
        $stmt = $db->prepare($sql);
        if (defined('RUNMODE') && RUNMODE == 'development')
        {
            $search = array();
            $replace = array();
            foreach ($params as $column => $param)
            {
                $search[] = $column;
                if (is_array($param))
                {
                    if ($param[1] == PDO::PARAM_INT)
                    {
                        $replace[] = intval($param[0]);
                    }
                    else
                    {
                        $replace[] = "'$param[0]'";
                    }
                    $stmt->bindValue($column, $param[0], $param[1]);
                }
                else
                {
                    $replace[] = "'$param'";
                    $stmt->bindValue($column, $param);
                }
            }
            Registry::debug()->info(str_replace($search, $replace, $stmt->queryString));
        }
        else
        {
            foreach ($params as $column => $param)
            {
                if (is_array($param))
                {
                    $stmt->bindValue($column, $param[0], $param[1]);
                }
                else
                {
                    $stmt->bindValue($column, $param);
                }
            }
        }
        $result = $stmt->execute();
        $this->logError($stmt);
        if ($result)
        {
            return $stmt;
        }
        else
        {
            return false;
        }
    }

    /**
     * 预处理查询条件
     * @param string|array $condition
     * @return array
     */
    private function preCondition($condition)
    {
        if (is_string($condition))
        {
            $condition = array("where" => $condition, 'params' => array());
        }
        else if (isset($condition['params']) && $condition['params'])
        {
            $paramSuffix = "_suffix";
            $condition['where'] = preg_replace("/(\:\w*)/is", "$1$paramSuffix", $condition['where']);
            foreach ($condition['params'] as $key => $value)
            {
                unset($condition['params'][$key]);
                $condition['params'][$key . $paramSuffix] = $value;
            }
        }
        else
        {
            $condition['params'] = array();
        }
        return $condition;
    }

    /**
     * 记录错误日志
     */
    private function logError($obj, $msg = "")
    {
        if ($this->logger === null)
        {
            try
            {
                $this->logger = Registry::runtime();
            }
            catch (\Exception $e)
            {
                $this->logger = false;
                return;
            }
        }
        else if ($this->logger === false)
        {
            return;
        }
        if (!$msg)
        {
            if ($obj->errorCode() == "00000")
            {
                return;
            }
            else
            {
                $errorInfo = $obj->errorInfo();
                $msg = "SQLSTATE error code: {$errorInfo[0]} Driver-specific error code: {$errorInfo[1]} Driver-specific error message: {$errorInfo[2]}";
            }
        }
        $trace = debug_backtrace();
        $msgs = array($msg);
        foreach ($trace as $call)
        {
            $msgs[] = "{$call['file']} on line {$call['line']}";
        }
        $msgs[] = "--------------------------------------------------------------------------------";
        $msgKey = md5(implode("", $msgs));
        if (isset($this->logErrors[$msgKey]))
        {
            return;
        }
        else
        {
            $this->logErrors[$msgKey] = 1;
            foreach ($msgs as $msg)
            {
                $this->logger->error($msg);
            }
        }
    }

}
