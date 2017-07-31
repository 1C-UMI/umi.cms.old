<?php
/**
 * $Id: exitPoints.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о точках выхода за период
 *
 */

class exitPoints extends simpleStat
{
    /**
     * Метод получения отчёта
     *
     * @return array
     */
    public function get()
    {
        mysql_query("SET @all = (SELECT COUNT(*) FROM `cms_stat_paths`
                      WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `host_id` = " . $this->host_id . ")");

        mysql_query("DROP TEMPORARY TABLE IF EXISTS `tmp_paths_out`");
        mysql_query("CREATE TEMPORARY TABLE `tmp_paths_out` (`level`INT, `path_id` INT, KEY `path_id_level` (`path_id`, `level`)) ENGINE = MEMORY");

        mysql_query("INSERT INTO `tmp_paths_out` (SELECT MAX(`number_in_path`) AS `level`, `path_id` FROM `cms_stat_hits` `h`
                         INNER JOIN `cms_stat_paths` `p` ON `p`.`id` = `h`.`path_id`
                          WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                           GROUP BY `path_id`)");

        return $this->simpleQuery("SELECT COUNT(*) AS `abs`, COUNT(*) / @all * 100 AS `rel`, `p`.`uri` FROM `cms_stat_hits` `h`
                                     INNER JOIN `tmp_paths_out` `t` ON `h`.`path_id` = `t`.`path_id` AND `h`.`number_in_path` = `t`.`level`
                                      INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                                       WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                                        GROUP BY `h`.`page_id`
                                         ORDER BY `rel` DESC
                                          LIMIT " . $this->offset . ", " . $this->limit);
    }
}

?>