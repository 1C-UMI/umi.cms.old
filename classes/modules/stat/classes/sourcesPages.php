<?php
/**
 * $Id: sourcesPages.php 36 2006-12-21 12:03:39Z zerkms $
 *
 * Класс получения информации о рейтинге страниц
 *
 */

class sourcesPages extends simpleStat
{
	/**
	* Метод получения отчёта
	*
	* @return array
	*/
	public function get()
	{
		return $this->simpleQuery("SELECT COUNT(*) AS `cnt`, `ss`.`uri`, `ssd`.`name`, `ssd`.`id` FROM `cms_stat_sources_sites` `ss`
									INNER JOIN `cms_stat_sources` `s` ON `s`.`concrete_src_id` = `ss`.`id` AND `s`.`src_type` = 1
									INNER JOIN `cms_stat_sources_sites_domains` `ssd` ON `ssd`.`id` = `ss`.`domain`
									INNER JOIN `cms_stat_paths` `p` ON `p`.`source_id` = `s`.`id`
										WHERE `p`.`date` BETWEEN " . $this->getQueryInterval() . " AND `p`.`host_id` = " . $this->host_id . "
										GROUP BY `ss`.`uri`
										ORDER BY `cnt` DESC
										LIMIT " . $this->offset . ", " . $this->limit);
	}
}

?>