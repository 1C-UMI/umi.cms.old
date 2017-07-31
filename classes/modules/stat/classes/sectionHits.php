<?php
/**
 * $Id: sectionHits.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о количестве просмотров разделов за период
 *
 */

class sectionHits extends simpleStat
{

    /**
     * Метод получения отчёта
     *
     * @return array
     */
    public function get()
    {
        mysql_query("SET @all = (SELECT COUNT(*) FROM `cms_stat_hits` `h`
                                    INNER JOIN `cms_stat_paths` `p` ON `p`.`id` = `h`.`path_id`
                                     WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . ")");

        return $this->simpleQuery("SELECT COUNT(*) AS `abs`, COUNT(*) / @all * 100 AS `rel`, `p`.`section` FROM `cms_stat_pages` `p`
                                     INNER JOIN `cms_stat_hits` `h` ON `h`.`page_id` = `p`.`id`
                                      WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                                       GROUP BY `p`.`section`
                                        ORDER BY `abs` DESC
                                         LIMIT " . $this->offset . ", " . $this->limit);
    }

    public function getIncluded($section)
    {
        mysql_query("SET @all = (SELECT COUNT(*) FROM `cms_stat_pages` `p`
                     INNER JOIN `cms_stat_hits` `h` ON `h`.`page_id` = `p`.`id`
                      WHERE `p`.`section` = '" . mysql_real_escape_string($section) . "' AND `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . ")");

        return $this->simpleQuery("SELECT COUNT(*) AS `abs`, COUNT(*) / @all * 100 AS `rel`, `p`.`uri`, `p`.`section`, UNIX_TIMESTAMP(`h`.`date`) AS `ts` FROM `cms_stat_pages` `p`
                                     INNER JOIN `cms_stat_hits` `h` ON `h`.`page_id` = `p`.`id`
                                      WHERE `p`.`section` = '" . mysql_real_escape_string($section) . "' AND `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                                       GROUP BY `p`.`id`
                                        ORDER BY `abs` DESC
                                         LIMIT " . $this->offset . ", " . $this->limit);
    }
}

?>