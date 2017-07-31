<?php
/**
 * $Id: entryPoints.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о точках входа за период
 *
 */

class entryPoints extends simpleStat
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
                      WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `number_in_path` = 1 AND `p`.`host_id` = " . $this->host_id . ")");

        return $this->simpleQuery($qry = "SELECT COUNT(*) AS `abs`, COUNT(*) / @all * 100 AS `rel`, `p`.`uri`, `p`.`id`, UNIX_TIMESTAMP(`h`.`date`) AS `ts` FROM `cms_stat_hits` `h`
                                     INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                                      WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `h`.`number_in_path` = 1 AND `p`.`host_id` = " . $this->host_id . "
                                       GROUP BY `h`.`page_id`
                                        ORDER BY `abs` DESC
                                         LIMIT " . $this->offset . ", " . $this->limit);
    }
}

?>