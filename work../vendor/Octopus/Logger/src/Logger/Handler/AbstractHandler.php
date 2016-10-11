<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�AbstractHandler.php
 * ժ    Ҫ��������������
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus\Logger\Handler;

use Octopus\Logger\Formatter\FormatterInterface;
use Octopus\Logger\Formatter\LineFormatter;
use Psr\Log\LogLevel;

abstract class AbstractHandler implements HandlerInterface
{

    protected $level = LogLevel::DEBUG;
    protected $bubble = true;
    protected $formatter;
    protected $processors = array();
    protected $weights = array(
        LogLevel::EMERGENCY => 800,
        LogLevel::ALERT => 700,
        LogLevel::CRITICAL => 600,
        LogLevel::ERROR => 500,
        LogLevel::WARNING => 400,
        LogLevel::NOTICE => 300,
        LogLevel::INFO => 200,
        LogLevel::DEBUG => 100,
    );

    /**
     * ���캯��������Ĭ�ϴ������Ƿ�ð��
     * @param type $level
     * @param type $bubble
     */
    public function __construct($level = LogLevel::DEBUG, $bubble = true)
    {
        $this->setLevel($level);
        $this->bubble = $bubble;
    }

    /**
     * �ж��Ƿ�ɲ���
     * @param array $record
     * @return type
     */
    public function isHandling(array $record)
    {
        return $this->weights[$record['level']] >= $this->weights[$this->level];
    }

    /**
     * ��������
     * @param array $records
     */
    public function handleBatch(array $records)
    {
        foreach ($records as $record)
        {
            $this->handle($record);
        }
    }

    /**
     * �ر�
     */
    public function close()
    {
        
    }

    /**
     * ����Ԥ����ʵ��
     * @param type $callback
     * @return AbstractHandler
     * @throws \InvalidArgumentException
     */
    public function pushProcessor($callback)
    {
        if (!is_callable($callback))
        {
            throw new \InvalidArgumentException('Processors must be valid callables (callback or object with an __invoke method), ' . var_export($callback, true) . ' given');
        }
        array_unshift($this->processors, $callback);
        return $this;
    }

    /**
     * �Ƴ���λԤ����ʵ��
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
     * ���ø�ʽ����ʵ��
     * @param FormatterInterface $formatter
     * @return AbstractHandler
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * ��ȡ��ʽ����ʵ��
     * @return type
     */
    public function getFormatter()
    {
        if (!$this->formatter)
        {
            $this->formatter = $this->getDefaultFormatter();
        }
        return $this->formatter;
    }

    /**
     * ���ÿɲ�������
     * @param type $level
     * @return AbstractHandler
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * ��ȡ�ɲ�������
     * @return type
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * �����Ƿ�ð��
     * @param type $bubble
     * @return AbstractHandler
     */
    public function setBubble($bubble)
    {
        $this->bubble = $bubble;
        return $this;
    }

    /**
     * ��ȡ�Ƿ�ð��
     * @return type
     */
    public function getBubble()
    {
        return $this->bubble;
    }

    /**
     * ��������
     */
    public function __destruct()
    {
        try
        {
            $this->close();
        }
        catch (\Exception $e)
        {
            // do nothing
        }
    }

    /**
     * ��ȡĬ�ϸ�ʽ����
     * @return LineFormatter
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }

}
