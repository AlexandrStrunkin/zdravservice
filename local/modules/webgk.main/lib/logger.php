<?php

namespace Webgk\Main;

use Webgk\Main\Hlblock\Prototype;
use Webgk\Main\Timer;
use Webgk\Main\Tools;

/**
 * класс для логирования SOAP
 */
class Logger
{
    /**
     * Время начала обработки
     *
     * @var string
     */
    public $date;

    /**
     * использование памяти
     *
     * @var int
     */
    public $memoryStart;

    /**
     * объект таймера
     *
     * @var Timer
     */
    public $timer;

    /**
     * логируемый метод
     *
     * @var string
     */
    public $method;

    /**
     * комментарий к записи лога
     *
     * @var string
     */
    public $comment;

    /**
     * количество обработаных элементов
     *
     * @var int
     */
    public $count;

    /**
     * елементов с ошибкой
     *
     * @var int
     */
    public $count_errors;
    
    /**
    * статус
    * 
    * @var string
    */
    public $status;

    public function __construct($hlBlock_code)
    {
        $this->HLBlock = Prototype::getInstance($hlBlock_code);
        $this->status = "OK";
    }

    /**
     * время старта
     *
     */
    public function GetDateStart()
    {
        $this->date = date("d.m.Y H:i:s");
    }

    /**
     * память на старте
     *
     */
    public function GetMemoryStart()
    {
        $this->memoryStart = memory_get_usage();
    }

    /**
     * начало логирования
     *
     */
    public function StartLog($method)
    {
        $this->GetDateStart();
        $this->GetMemoryStart();
        $this->count = 0;
        $this->count_errors = 0;
        $this->comment = "";
        $this->method = $method;
        $this->timer = new Timer();
        $this->timer->start($this->method);
    }

    /**
     * завершение логирования
     *
     */
    public function EndLog()
    {
        $this->timer->end($this->method);
        $timerIntervals = $this->timer->getIntervals();
        $arLog["UF_EXECUTION_TIME"] = round(array_sum($timerIntervals[$this->method]), 2);

        $arLog["UF_MEMORY_USAGE"] = Tools::getFileSize(memory_get_usage() - $this->memoryStart);
        $arLog["UF_METHOD"] = $this->method;
        $arLog["UF_DATE"] = $this->date;
        $arLog["UF_COMMENT"] = $this->comment;
        $arLog["UF_COUNT"] = $this->count;
        $arLog["UF_COUNT_ERROR"] = $this->count_errors;
        $arLog["UF_STATUS"] = $this->status; 

        $this->HLBlock->addData($arLog);
    }
}
