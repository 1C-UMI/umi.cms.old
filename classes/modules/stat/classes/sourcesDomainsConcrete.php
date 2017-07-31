<?php
/**
 * $Id: sourcesDomainsConcrete.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о страницах ссылающихся доменов
 *
 */

class sourcesDomainsConcrete extends simpleStat
{
    /**
     * массив параметров
     *
     * @var array
     */
    protected $params = array('domain_id' => 0);

    /**
     * Метод получения отчёта
     *
     * @return array
     */
    public function get()
    {
        $all = $this->simpleQuery("SELECT SQL_CALC_FOUND_ROWS COUNT(*) AS `cnt`, `ss`.`uri`, `ssd`.`name`, `ssd`.`id`, UNIX_TIMESTAMP(`p`.`date`) AS `ts` FROM `cms_stat_sources_sites` `ss`
                                     INNER JOIN `cms_stat_sources_sites_domains` `ssd` ON `ssd`.`id` = `ss`.`domain`
                                      INNER JOIN `cms_stat_sources` `s` ON `s`.`concrete_src_id` = `ss`.`id` AND `s`.`src_type` = 1
                                       INNER JOIN `cms_stat_paths` `p` ON `p`.`source_id` = `s`.`id`
                                        WHERE `ssd`.`id` = " . (int)$this->params['domain_id'] . " AND `p`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
                                         GROUP BY `ss`.`uri`
                                          ORDER BY `cnt` DESC
                                           LIMIT " . $this->offset . ", " . $this->limit);
        $res = $this->simpleQuery('SELECT FOUND_ROWS() as `total`');
        $i_total = (int) $res[0]['total'];
        return array("all"=>$all, 'total'=>$i_total);
    }
}

?>