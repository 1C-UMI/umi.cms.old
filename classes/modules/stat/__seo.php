<?php
	abstract class __seo_stat {

		public function engines() {

			$curr_page = (int) $_REQUEST['p'];

			$this->load_forms();

			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesSEO');
			$report = $factory->get('sourcesSEO');

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
		<a href="%pre_lang%/admin/stat/engine/{$info['engine_id']}/"><![CDATA[{$info['name']}]]></a>
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

			return $this->parse_form("engines", $params);
		}


		public function engine() {

			$curr_page = (int) $_REQUEST['p'];

			$params = Array();

			$this->load_forms();

			$this->parseTimeRange();

			$this->sheets_set_active("engines");

			$engine_id = $_REQUEST['param0'];

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('sourcesSEOConcrete');
			$report = $factory->get('sourcesSEOConcrete');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);
			$report->setParams(Array("engine_id" => $engine_id));

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
			<![CDATA[{$info['text']}]]>
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

			return $this->parse_form("engine", $params);
		}
	};
?>