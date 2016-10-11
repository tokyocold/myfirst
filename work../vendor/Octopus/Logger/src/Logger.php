<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�Logger.php
 * ժ    Ҫ����־��
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus;

use Octopus\Logger\Handler\HandlerInterface;
use Octopus\Logger\Handler\StreamHandler;
use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{

    protected static $timezone;
    protected $name;
    protected $handlers;
    protected $processors;

    /**
     * ���캯����������־���������ߣ�Ԥ����
     * @param type $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct($name, array $handlers = array(), array $processors = array())
    {
        $this->name = $name;
        $this->handlers = $handlers;
        $this->processors = $processors;
    }

    /**
     * ��ȡ��־��
     * @return type
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * ���������
     * @param HandlerInterface $handler
     */
    public function pushHandler(HandlerInterface $handler)
    {
        array_unshift($this->handlers, $handler);
    }

    /**
     * �Ƴ���λ������
     * @return type
     * @throws \LogicException
     */
    public function popHandler()
    {
        if (!$this->handlers)
        {
            throw new \LogicException('You tried to pop from an empty handler stack.');
        }

        return array_shift($this->handlers);
    }

    /**
     * ��ȡ���еĲ�����
     * @return type
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * ����Ԥ����
     * @param type $callback
     * @throws InvalidArgumentException
     */
    public function pushProcessor($callback)
    {
        if (!is_callable($callback))
        {
            throw new InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . var_export($callback, true) . ' given');
        }
        array_unshift($this->processors, $callback);
    }

    /**
     * �Ƴ���λԤ����
     * @return type
     * @throws \LogicException
     */
    public function popProcessor()
    {
        if (!$this->processors)
        {
            throw new \LogicException('You tried to pop from an empty processor stack.');
        }
        return array_shift($this->processors);
    }

    /**
     * ��ȡ���е�Ԥ����
     * @return type
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * �ж��Ƿ��ܲ���
     * @param type $level
     * @return boolean
     */
    public function isHandling($level)
    {
        $record = array(
            'level' => $level,
        );
        foreach ($this->handlers as $handler)
        {
            if ($handler->isHandling($record))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * д��־
     * @param type $level
     * @param type $message
     * @param array $context
     * @return boolean
     */
    public function log($level, $message, array $context = array())
    {
        if (!$this->handlers)
        {
            $this->pushHandler(new StreamHandler('php://stderr', LogLevel::DEBUG));
        }
        // check if any handler will handle this message so we can return early and save cycles
        $handlerKey = null;
        foreach ($this->handlers as $key => $handler)
        {
            if ($handler->isHandling(array('level' => $level)))
            {
                $handlerKey = $key;
                break;
            }
        }
        if (null === $handlerKey)
        {
            return false;
        }
        if (!static::$timezone)
        {
            static::$timezone = new \DateTimeZone(date_default_timezone_get() ? : 'UTC');
        }
        $record = array(
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'channel' => $this->name,
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true)), static::$timezone)->setTimezone(static::$timezone),
            'extra' => array(),
        );
        foreach ($this->processors as $processor)
        {
            $record = call_user_func($processor, $record);
        }
        while (isset($this->handlers[$handlerKey]) &&
        false === $this->handlers[$handlerKey]->handle($record))
        {
            $handlerKey++;
        }
        return true;
    }

}
