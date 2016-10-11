<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�AbstractProcessingHandler.php
 * ժ    Ҫ�������������
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus\Logger\Handler;

abstract class AbstractProcessingHandler extends AbstractHandler
{

    /**
     * ��������
     * @param array $record
     * @return boolean
     */
    public function handle(array $record)
    {
        if (!$this->isHandling($record))
        {
            return false;
        }
        $record = $this->processRecord($record);
        $record['formatted'] = $this->getFormatter()->format($record);
        $this->write($record);
        return false === $this->bubble;
    }

    /**
     * д���¼
     */
    abstract protected function write(array $record);

    /**
     * Ԥ�����¼
     * @param array $record
     * @return type
     */
    protected function processRecord(array $record)
    {
        if ($this->processors)
        {
            foreach ($this->processors as $processor)
            {
                $record = call_user_func($processor, $record);
            }
        }
        return $record;
    }

}
