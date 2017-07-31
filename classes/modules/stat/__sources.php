<?php
	abstract class __sources_stat {

		public function sources() {

			$curr_page = (int) $_REQUEST['p'];

			$this->load_forms();
			
			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesDomains');
			$report = $factory->get('sourcesDomains');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);

			$result = $report->get();
			
			$rows = "";
			$c = $curr_page*$this->items_per_page;

			foreach($result['all'] as $info) {
				++$c;

				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="%pre_lang%/admin/stat/sources_domain/{$info['domain_id']}/"><![CDATA[{$info['name']}]]></a>
	</col>

	<col>
		<![CDATA[{$info['cnt']}]]>
	</col>
</row>


ROW;
			}

			$params['rows'] = $rows;
			$params['time_range'] = $this->returnRangePanel();
			$params['pages'] = $this->generateNumPage($result['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("sources", $params);
		}


		public function sources_domain() {
			
			$curr_page = (int) $_REQUEST['p'];

			$this->sheets_set_active("sources");

			$this->load_forms();

			$this->parseTimeRange();

			$domain_id = (int) $_REQUEST['param0'];

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesDomainsConcrete');
			$report = $factory->get('sourcesDomainsConcrete');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);
			$report->setParams(Array("domain_id" => $domain_id));

			$result = $report->get();

			$rows = "";
			$c = $curr_page*$this->items_per_page;
			foreach($result['all'] as $info) {
				++$c;
				
				$page_rel = "http://" . $info['name'] . $info['uri'];
				$page_rel = str_replace("&", "&amp;", $page_rel);
				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="{$page_rel}"><![CDATA[http://{$info['name']}{$info['uri']}]]></a>
	</col>

	<col>
		<![CDATA[{$info['cnt']}]]>
	</col>
</row>


ROW;
			}

			$params['rows'] = $rows;
			$params['time_range'] = $this->returnRangePanel();
			$params['pages'] = $this->generateNumPage($result['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("sources_domain", $params);
		}

	};
?>