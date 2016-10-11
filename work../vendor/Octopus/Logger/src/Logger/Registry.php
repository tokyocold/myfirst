<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�Registry.php
 * ժ    Ҫ����־ע�Ṥ����
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus\Logger;

use Psr\Log\LoggerInterface;

class Registry
{

    private static $loggers = array();

    /**
     * �����־ʵ��
     * @param LoggerInterface $logger
     * @param type $name
     * @param type $overwrite
     * @throws \InvalidArgumentException
     */
    public static function addLogger(LoggerInterface $logger, $name = null, $overwrite = false)
    {
        $name = $name ? : $logger->getName();

        if (isset(self::$loggers[$name]) && !$overwrite)
        {
            throw new \InvalidArgumentException('Logger with the given name already exists');
        }

        self::$loggers[$name] = $logger;
    }

    /**
     * �Ƴ���־ʵ��
     * @param LoggerInterface $logger
     */
    public static function removeLogger(LoggerInterface $logger)
    {
        if ($logger instanceof Logger)
        {
            if (false !== ($idx = array_search($logger, self::$loggers, true)))
            {
                unset(self::$loggers[$idx]);
            }
        }
        else
        {
            unset(self::$loggers[$logger]);
        }
    }

    /**
     * �����־ʵ��
     */
    public static function clear()
    {
        self::$loggers = array();
    }

    /**
     * ��ȡĳ����־ʵ��
     * @param type $name
     * @return type
     * @throws \InvalidArgumentException
     */
    public static function getInstance($name)
    {
        if (!isset(self::$loggers[$name]))
        {
            throw new \InvalidArgumentException(sprintf('Requested "%s" logger instance is not in the registry', $name));
        }
        return self::$loggers[$name];
    }

    /**
     * ħ����������ȡĳ����־ʵ��
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public static function __callStatic($name, $arguments)
    {
        return self::getInstance($name);
    }

}
