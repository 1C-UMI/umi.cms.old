<?php
	abstract class __phrases_stat {

		public function phrases() {

			$curr_page = (int) $_REQUEST['p'];

			$this->load_forms();

			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesSEOKeywords');
			$report = $factory->get('sourcesSEOKeywords');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);

			$result = $report->get();

			$rows = "";

			$c = $curr_page*$this->items_per_page;
			foreach($result['all'] as $info) {
				++$c;
				
				$info['text'] = iconv("CP1251", "CP1251", $info['text']);
				if(!stat::isStringCP1251($info['text'])) {
					$info['text'] = "";
				}

				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="%pre_lang%/admin/stat/phrase/{$info['query_id']}/"><![CDATA[{$info['text']}]]></a>
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

			return $this->parse_form("phrases", $params);
		}


		public function phrase() {

			$curr_page = (int) $_REQUEST['p'];

			$params = Array();

			$this->load_forms();

			$this->parseTimeRange();

			$this->sheets_set_active("phrases");

			$query_id = $_REQUEST['param0'];

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesSEOKeywordsConcrete');
			$report = $factory->get('sourcesSEOKeywordsConcrete');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);
			$report->setParams(Array("query_id" => $query_id));

			$result = $report->get();
			
			$rows = "";
			$c = $curr_page*$this->items_per_page;
			foreach($result['all'] as $info) {
				++$c;

				$rows .= <<<END
	<row>
		<col>
			$c.
		</col>

		<col>
			<![CDATA[{$info['name']}]]>
		</col>


		<col>
			<![CDATA[{$info['cnt']}]]>
		</col>
	</row>

END;
			}

			$params['rows'] = $rows;
			$params['time_range'] = $this->returnRangePanel();
			$params['pages'] = $this->generateNumPage($result['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("engines", $params);
		}
	};
?>