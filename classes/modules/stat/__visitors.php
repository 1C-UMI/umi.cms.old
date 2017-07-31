<?php
	abstract class __visitors_stat {

		public function visitors() {

			$curr_page = (int) $_REQUEST['p'];

			$this->load_forms();

			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('visitersCommon');
			$report = $factory->get('visitersCommon');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);

			$result = $report->get();

			$rows = "";

			$c = $curr_page*$this->items_per_page;
			$iTotal = 0;
			foreach($result['detail']['all'] as $info) {
				$user_id = $info['user_id'];

				//$factory->isValid('userStat');
				//report = $factory->get('userStat');

				//$report->setParams($info);
				//$user_info = $report->get();

				//$first_visit = ($user_info['first_visit']) ? date("Y-m-d | H:i", $user_info['first_visit']) : "-";
				//$last_visit = ($user_info['last_visit']) ? date("Y-m-d | H:i", $user_info['last_visit']) : "-";



				++$c;

				$sDate = date("Y-m-d", $info['ts']);
				$iTotal += $info['cnt'];
				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="%pre_lang%/admin/stat/visitors_by_date/{$info['ts']}/"><![CDATA[{$sDate}]]></a>
	</col>

	<col>
		<![CDATA[{$info['cnt']}]]>
	</col>
</row>


ROW;
			}
			if ($iTotal>0) {
				$rows .= <<<ROW
<row>
	<col>&nbsp;</col>
	<col>
		<b>Всего</b>
	</col>

	<col>
		<b><![CDATA[{$iTotal}]]></b>
	</col>
</row>
ROW;
			}

			$params['routine'] = $result['avg']['routine'];
			$params['weekend'] = $result['avg']['weekend'];
			$params['rows'] = $rows;
			$params['time_range'] = $this->returnRangePanel();
			$params['pages'] = $this->generateNumPage($result['detail']['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("visitors_common", $params);
		}

		public function visitors_by_date() {

			$this->sheets_set_active("visitors");

			$curr_page = (int) $_REQUEST['p'];

			$ts = (int) $_REQUEST['param0'];

			$this->load_forms();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('visitsByDate');
			$report = $factory->get('visitsByDate');

			$report->setFinish(strtotime('+1 day', $ts));
			$report->setLimit($this->items_per_page);
			$report->setOffset($curr_page*$this->items_per_page);

			$result = $report->get();
			
			$rows = "";

			$c = $curr_page*$this->items_per_page;

			foreach($result['all'] as $info) {
				$user_id = $info['user_id'];

				$factory->isValid('userStat');
				$report = $factory->get('userStat');

				$report->setParams($info);
				$user_info = $report->get();

				$first_visit = ($user_info['first_visit']) ? date("Y-m-d | H:i", $user_info['first_visit']) : "-";
				$last_visit = ($user_info['last_visit']) ? date("Y-m-d | H:i", $user_info['last_visit']) : "-";



				++$c;

				$rows .= <<<ROW

<row>
	<col>{$c}</col>

	<col>
		<a href="%pre_lang%/admin/stat/visitor/{$user_id}/"><![CDATA[{$user_info['browser']} ({$user_info['os']})]]></a>
	</col>

	<col>
		<![CDATA[{$first_visit}]]>
	</col>

	<col>
		<![CDATA[{$last_visit}]]>
	</col>
</row>


ROW;
			}


			$params['rows'] = $rows;
			$params['pages'] = $this->generateNumPage($result['total'], $this->items_per_page, $curr_page);

			return $this->parse_form("visitors_by_date", $params);
		}

		public function visitor() {
			$params = Array();
			$this->load_forms();

			$this->sheets_set_active("visitors");

			$user_id = $_REQUEST['param0'];

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('userStat');
			$report = $factory->get('userStat');
			$fromTS = $this->ts;
			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
//			$report->setFinish($fromTS);
			$report->setLimit($this->per_page);
			$report->setParams(Array("user_id" => $user_id));
			$user_info = $report->get();


			$params['first_visit'] = ($user_info['first_visit']) ? date("Y-m-d | H:i", $user_info['first_visit']) : "-";
			$params['last_visit'] = ($user_info['last_visit']) ? date("Y-m-d | H:i", $user_info['last_visit']) : "-";
			$params['visit_count'] = $user_info['visit_count'];
			$params['os'] = $user_info['os'];
			$params['browser'] = $user_info['browser'];
			$params['js_version'] = $user_info['js_version'];

			$params['source_link'] = $user_info['source']['name'];
			$params['last_source_link'] = $user_info['last_source']['name'];
			
			$login_id = $user_info['login'];
			$params['user_info'] = ($login_id) ? "%users get_user_info('{$login_id}', '%login% - %last_name% %first_name% %father_name%')%" : "Посетитель не зарегистрировался на сайте";

			$tags = Array();
			foreach($user_info['labels']['top'] as $label) {
				$tags[] = $label['name'] . " (" . $label['cnt'] . ")";
			}

			$rows = "";
			$c = 0;
			foreach($user_info['last_path'] as $uri) {
				++$c;
				$page_uri = $uri['uri'];

				if($element_id = umiHierarchy::getInstance()->getIdByPath($page_uri)) {
				} else if($page_uri == "/") {
					$element_id = umiHierarchy::getInstance()->getDefaultElementId();
				}

				if($element = umiHierarchy::getInstance()->getElement($element_id)) {
					$page_title = $element->getName();
				}


				$rows .= <<<END
	<row>
		<col>
			$c.
		</col>

		<col>
			<a href="$page_uri"><![CDATA[{$page_title}]]></a>
		</col>


		<col>
			<![CDATA[$page_uri]]>
		</col>
	</row>

END;
			}


			$params['tags'] = implode(", ", $tags);
			$params['rows'] = $rows;

			return $this->parse_form("visitor", $params);
		}
	};
?>