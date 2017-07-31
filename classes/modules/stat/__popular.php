<?php
	abstract class __popular_stat {
		public function popular_pages() {
			
			$curr_page = (int) $_REQUEST['p'];

			$this->load_forms();

			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('pagesHits');
			$report = $factory->get('pagesHits');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);

			$result = $report->get();
			
			$rows = "";
			$c = $curr_page*$this->items_per_page;
			foreach($result['all'] as $info) {
				$page_uri = $info['uri'];

				if($element_id = umiHierarchy::getInstance()->getIdByPath($page_uri)) {
				} else if($page_uri == "/") {
					$element_id = umiHierarchy::getInstance()->getDefaultElementId();
				}

				if($element = umiHierarchy::getInstance()->getElement($element_id)) {
					$page_title = $element->getName();
				}


				$proc = round($info['rel'], 2);

				++$c;
				if (!strlen($page_title)) $page_title = $info['uri'];
				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="{$info['uri']}"><![CDATA[$page_title]]></a>
	</col>

	<col>
		<![CDATA[{$info['uri']}]]>
	</col>

	<col align='center'>
		<![CDATA[{$info['abs']}]]>
	</col>

	<col align='center'>&#160;{$proc}%&#160;</col>
</row>


ROW;
			}


			$params['rows'] = $rows;
			$params['time_range'] = $this->returnRangePanel();
			$params['pages'] = $this->generateNumPage($result['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("popular_pages", $params);
		}
	};
?>