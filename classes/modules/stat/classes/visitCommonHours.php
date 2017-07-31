<?php
/**
 * $Id: visitCommonHours.php 19 2006-09-25 11:54:23Z zerkms $
 *
 * Класс получения обобщённой информации о посещаемости, срез по часам
 *
 */

require_once 'classes/holidayRoutineCounter.php';

class visitCommonHours extends simpleStat
{
    /**
     * Число выходных дней за период
     *
     * @var integer
     */
    private $weekends_count = 0;

    /**
     * Число будних дней за период
     *
     * @var integer
     */
    private $routine_count = 0;

    /**
     * Метод получения отчёта
     *
     * @return array
     */
    public function get()
    {
        return array('detail' => $this->getDetail(), 'avg' => $this->getAvg());
    }

    /**
     * метод получения почасовой информации о числе посещений в выбранном интервале
     *
     * @return array
     */
    private function getDetail()
    {
        $this->setUpVars();

        return $this->simpleQuery("SELECT COUNT(*) AS `cnt`, `hour`, UNIX_TIMESTAMP(`date`) AS `ts` FROM `cms_stat_hits` `h`
                             INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                              WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                               GROUP BY `hour`");


    }

    /**
     * метод получения почасовой информации за выходные и будни
     *
     * @return array
     */
    private function getAvg()
    {
        $this->setUpVars();

        $qry = "(SELECT 'routine' AS `type`, COUNT(*) / " . $this->routine_count . ".0 AS `avg`, `hour` FROM `cms_stat_hits` `h`
                 LEFT JOIN `cms_stat_holidays` `holidays` ON `h`.`day` = `holidays`.`day` AND `h`.`month` = `holidays`.`month`
                  INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                   WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                    AND `day_of_week` BETWEEN 1 AND 5 AND `holidays`.`id` IS NULL
                     GROUP BY `hour`)
                UNION
                (SELECT 'weekend' AS `type`, COUNT(*) / " . $this->holidays_count . ".0 AS `avg`, `hour` FROM `cms_stat_hits` `h`
                 LEFT JOIN `cms_stat_holidays` `holidays` ON `h`.`day` = `holidays`.`day` AND `h`.`month` = `holidays`.`month`
                  INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                   WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                    AND (`day_of_week` NOT BETWEEN 1 AND 5 OR `holidays`.`id` IS NOT NULL)
                     GROUP BY `hour`)";

        $res = mysql_query($qry);

        $result = array();

        while ($row = mysql_fetch_assoc($res)) {
            $result[$row['type']][$row['hour']] = $row['avg'];

        }

        return $result;
    }


    /**
     * метод установки необходимых для работы класса переменных
     *
     */
    private function setUpVars()
    {
        $res = holidayRoutineCounter::count($this->start, $this->finish);
        $this->holidays_count = $res['holidays'];
        $this->routine_count = $res['routine'];
    }
}

?>