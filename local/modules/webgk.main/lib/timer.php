<?php


namespace Webgk\Main;

/**
 * Клас для работы с замерами временем
 */
class Timer
{

    /**
     * @var object Экземпляры класса
     */

    private static $_instance;

    /**
     * @var array таймеры
     */

    private $timers = [];

    /**
     * @var array интервалы
     */

    private $intervals = [];

    public static function getInstance()
    {
        if (is_null(self::$_instance))
            self::$_instance = new Timer();
        return self::$_instance;
    }

    /**
     * Запускает таймер
     * @param string $timerName код таймера
     */

    public function start($timerName)
    {
        $this->timers[$timerName] = microtime(true);
    }

    /**
     * Запускает таймер
     * @param string $timerName код таймера
     */

    public function end($timerName)
    {
        $this->intervals[$timerName][] = microtime(true) - $this->timers[$timerName];
    }

    /**
     * Возвращает интервал таймера
     *
     * @param $timerName
     * @return mixed
     */

    public function getInterval($timerName)
    {
        return microtime(true) - $this->timers[$timerName];
    }

    /**
     * Возвращает интервалы
     *
     * @return array
     */

    public function getIntervals()
    {
        return $this->intervals;
    }

    /**
     * Возвращает среднее арифметическое интервалов
     *
     * @return array
     */

    public function getIntervalsAvg()
    {
        $avgArray = Array();
        foreach ($this->intervals as $name => $data) {
            $c = count($data);
            $avgArray[$name . ' x ' . $c] = array_sum($data) / $c;
        }
        return $avgArray;
    }

    /**
     * @return array Получение суммы по интервалам
     */

    public function getIntervalsSum()
    {
        $sumArray = Array();
        foreach ($this->intervals as $name => $data) {
            $c = count($data);
            $sumArray[$name . ' x ' . $c] = array_sum($data);
        }
        return $sumArray;
    }

}
