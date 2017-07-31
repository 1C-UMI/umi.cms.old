<?php
/**
 * $Id: simpleStat.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Абстрактный класс для всех отчётов
 *
 */

abstract class simpleStat
{
    /**
     * константа, хранящая формат для даты mysql
     *
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * стартовая дата для анализа
     *
     * @var integer
     */
    protected $start;

    /**
     * конечная дата для анализа
     *
     * @var integer
     */
    protected $finish;

    /**
     * Id хоста, для которого производятся выборки
     *
     * @var unknown_type
     */
    protected $host_id;

    /**
     * Интервал по умолчанию
     * задаётся в насоедниках, если нужно
     *
     * @var string
     */
    protected $interval = '-10 days';

    /**
     * абстрактный метод получения отчёта
     * должен быть переопределён в наследниках
     *
     */
    abstract public function get();

    /**
     * массив разрешённых параметров
     *
     * @var array
     */
    protected $params = array();

    protected $limit = 10;
    
    protected $offset = 0;

    /**
     * Конструктор
     *
     * @param integer $finish конечная дата анализа
     * @param string $interval анализируемый интервал
     */
    public function __construct($finish = null, $interval = null)
    {
        if (!empty($finish)) {
            $this->setFinish($finish);
        } else {
            $this->setFinish(time());
        }

        $this->setDomain($_SERVER['SERVER_NAME']);

        if (empty($interval)) {
            $interval = $this->interval;
        } else {
            $this->interval = $interval;
        }

        $this->setInterval($interval);
    }

    /**
     * Метод установки имени домена, для которого производятся выборки
     *
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->host_id = $this->searchHostIdByHostname($domain);
    }

    /**
     * метод установки конечной даты анализа
     *
     * @param integer $finish unix timestamp для конечной даты
     */
    public function setFinish($finish)
    {
        if (!is_integer($finish)) {
            throw new invalidParameterException('Значение свойства finish должно быть целочисленного типа и > 0', $finish);
        }

        $this->finish = $finish;

        //$this->setInterval($this->interval);
    }
    
    /**
    * метод установки начальной даты интервала
    *
    * @param integer $start
    */
    public function setStart($start)
    {
        if (!is_integer($start)) {
            throw new invalidParameterException('Значение свойства start должно быть целочисленного типа и > 0', $start);
        }
        
        $this->start = $start;
    }

    /**
     * метод установки анализируемого интервала
     *
     * @param string $interval интервал. значение должно быть корректным для передачи первым аргументом в функцию strtotime
     */
    public function setInterval($interval)
    {
        $start = strtotime($interval, $this->finish);

        if (!is_integer($start)) {
            throw new invalidParameterException('Интервал должен задаваться в соответствии с требованиями к входным параметрам функции strtotime', $interval);
        }

        $this->start = $start;
    }

    /**
     * Метод для поиска Id домена по его имени
     *
     * @param string $hostname
     * @return integer
     */
    protected function searchHostIdByHostname($hostname)
    {
        $res = mysql_query("SELECT `group_id` AS `id` FROM `cms_stat_sites`
                             WHERE `name` = '" . mysql_real_escape_string($hostname) . "'");
        $row = mysql_fetch_assoc($res);
        return (int)$row['id'];
    }

    /**
     * Метод для форматирования даты из unix timestamp в формат mysql
     *
     * @param integer $date искомый timestamp
     * @return string
     */
    protected function formatDate($date)
    {
        return date(self::DATE_FORMAT, $date);
    }

    protected function getQueryInterval()
    {
        return "'" . $this->formatDate($this->start) . "' AND '" . $this->formatDate($this->finish) . "'";
    }

    /**
     * Общий метод для получения всех данных из запроса
     *
     * @param string $query искомый запрос
     * @return array массив с результатами
     */
    protected function simpleQuery($query)
    {
        $res = mysql_query($query);

        if (!is_resource($res)) {
//            die('<i>' . $query . '</i><br>' . mysql_error());
        }

        $result = array();

        while ($row = mysql_fetch_assoc($res)) {
            $result[] = $row;

        }

        return $result;
    }

    /**
     * Метод установки параметров для выборок
     *
     * @param array $array
     */
    public function setParams($array = array())
    {
        foreach($this->params as $key => $val) {
            if (isset($array[$key])) {
                $this->params[$key] = $array[$key];
            }
        }
    }

    public function setLimit($limit)
    {
        if ((int)$limit > 0) {
            $this->limit = $limit;
        }
    }
    
    public function setOffset($offset)
    {
        if ((int)$offset > 0) {
            $this->offset = $offset;
        }
    }
}

?>