<?php
/**
 * $Id: pagesHits.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о количестве просмотров страниц за период
 *
 */

class pagesHits extends simpleStat
{

    /**
     * Метод получения отчёта
     *
     * @return array
     */
    public function get()
    {
        //return array('all' => $this->getAll(), 'routine' => $this->getRoutine(), 'holidays' => $this->getHolidays());
        return $this->getAll();
    }

    private function getAll()
    {
        $result = $this->simpleQuery("SELECT COUNT(*) as `total` FROM `cms_stat_hits` `h`
                                     INNER JOIN `cms_stat_paths` `p` ON `p`.`id` = `h`.`path_id`
                                     WHERE `h`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id);
        $i_total = (int) $result[0]['total'];

        return array("all"=>$this->simpleQuery("SELECT COUNT(*) AS `abs`, COUNT(*) / ".$i_total." * 100 AS `rel`, `h`.`page_id`, `p`.`uri` FROM `cms_stat_hits` `h`
                                    INNER JOIN `cms_stat_pages` `p` ON `p`.`id` = `h`.`page_id`
                                     WHERE `date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                                      GROUP BY `page_id`
                                       ORDER BY `abs` DESC
                                        LIMIT " . $this->offset . ", " . $this->limit),
                     "total"=>$i_total);
    }
}

?>