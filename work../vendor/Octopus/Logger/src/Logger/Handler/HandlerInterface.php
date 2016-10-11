<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�HandlerInterface.php
 * ժ    Ҫ����������ӿ�
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus\Logger\Handler;

use Octopus\Logger\Formatter\FormatterInterface;

interface HandlerInterface
{

    /**
     * �Ƿ�ɲ���
     * @param array $record
     */
    public function isHandling(array $record);

    /**
     * ��������
     * @param array $record
     */
    public function handle(array $record);

    /**
     * ��������
     * @param array $records
     */
    public function handleBatch(array $records);

    /**
     * ����Ԥ����ʵ��
     * @param type $callback
     */
    public function pushProcessor($callback);

    /**
     * �Ƴ���λԤ����ʵ��
     */
    public function popProcessor();

    /**
     * ���ø�ʽ������ʵ��
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter);

    /**
     * ��ȡ��ʽ������ʵ��
     */
    public function getFormatter();
}
