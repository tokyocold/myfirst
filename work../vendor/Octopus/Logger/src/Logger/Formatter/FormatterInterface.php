<?php

/**
 * Copyright (c) 2015,�Ϻ�������������Ƽ��ɷ����޹�˾
 * �ļ����ƣ�FormatterInterface.php
 * ժ    Ҫ����ʽ���ӿ�
 * ��    �ߣ���С��
 * �޸����ڣ�2015.04.28
 */

namespace Octopus\Logger\Formatter;

interface FormatterInterface
{

    /**
     * ��ʽ������
     * @param array $record
     */
    public function format(array $record);

    /**
     * ������ʽ������
     * @param array $records
     */
    public function formatBatch(array $records);
}
