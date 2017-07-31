<?php
	abstract class __stat {

		public function total() {
			$this->load_forms();

			$this->parseTimeRange();

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			
			$factory->isValid('visitersCommon');
			$report = $factory->get('visitersCommon');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);

			$result = $report->get();

			$params['visits_routine'] = (int) $result['avg']['routine'];
			$params['visits_weekend'] = (int) $result['avg']['weekend'];

			$factory->isValid('hostsCommon');
			$report = $factory->get('hostsCommon');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);

			$result = $report->get();

			$params['hosts_routine'] = (int) $result['avg']['routine'];
			$params['hosts_weekend'] = (int) $result['avg']['weekend'];


			$factory->isValid('visitTime');
			$report = $factory->get('visitTime');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$visit_time = array_pop($result['dynamic']);
			$params['visit_time'] = round($visit_time['minutes_avg'], 2);



			$factory->isValid('visitDeep');
			$report = $factory->get('visitDeep');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$visit_deep = array_pop($result['dynamic']);
			$params['visit_deep'] = round($visit_deep['level_avg'], 2);


			$factory->isValid('sourcesTop');
			$report = $factory->get('sourcesTop');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();
			
			if ($result[0]['cnt']) {
				$params['top_source'] =  ($result[0]['type'] == "direct" ? "Прямой переход" : $result[0]['name']) . " (" . $result[0]['cnt'] . ")";
			}


			$factory->isValid('entryPoints');
			$report = $factory->get('entryPoints');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$page_rel = str_replace("&", "&amp;", $result[0]['uri']);
			if (strlen($page_rel)) {
				$params['top_enter'] =  "<a href='" . $page_rel . "'><![CDATA[" . $result[0]['uri'] . "]]></a> (" . $result[0]['abs'] . ")";
			}

			$factory->isValid('exitPoints');
			$report = $factory->get('exitPoints');

			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$page_rel = str_replace("&", "&amp;", $result[0]['uri']);
			if (strlen($page_rel)) {
				$params['top_exit'] =  "<a href='" . $page_rel . "'><![CDATA[" . $result[0]['uri'] . "]]></a> (" . $result[0]['abs'] . ")";
			}

			$factory->isValid('sourcesSEOKeywords');
			$report = $factory->get('sourcesSEOKeywords');
			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$params['top_keyword'] = (strlen($result['all'][0]['text'])? $result['all'][0]['text']." (" . $result['all'][0]['cnt'] . ")" : "");

			$factory->isValid('sourcesSEO');
			$report = $factory->get('sourcesSEO');
			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();

			$params['top_searcher'] = (strlen($result['all'][0]['name'])? $result['all'][0]['name']." (" . $result['all'][0]['cnt'] . ")" : "");

			$factory->isValid('pagesHits');
			$report = $factory->get('pagesHits');
			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setLimit(1);

			$result = $report->get();
			
			$page_rel = str_replace("&", "&amp;", $result['all'][0]['uri']);
			
			if (strlen($page_rel)) {
				$params['top_page'] = "<a href='" . $page_rel . "'><![CDATA[" . $result['all'][0]['uri'] . "]]></a> (" . $result['all'][0]['abs'] . ")";
			}

			$s_from = date("d.m.Y", $this->from_time);
			$s_to  =date("d.m.Y", $this->to_time);
			if ($s_from != $s_to) {
				$params['stat_period'] = "с ".$s_from.' по '.$s_to;
			} else {
				$params['stat_period'] = "за ".$s_to;
			}
			$params['time_range'] = $this->returnRangePanel();
			
			return $this->parse_form('total', $params);
		}

		public function returnRangePanel($from_time = 0, $to_time = 0) {

			$res = "";
			$params = Array();
	//		$this->load_forms();

			if($this->from_time)
				$from_time = (int) $this->from_time;
			else
				$from_time = time();

			if($this->to_time)
				$to_time = (int) $this->to_time;
			else
				$to_time = time();

			$days_arr = Array();
			for($i = 1; $i <= 31; $i++)
				$days_arr[] = $i;

			$months_arr1 = Array(	"январь",
						"февраль",
						"март",
						"апрель",
						"май",
						"июнь",
						"июль",
						"август",
						"сентябрь",
						"октябрь",
						"ноябрь",
						"декабрь");
			$months_arr2 = Array();
			for($i = 1; $i <= 12; $i++)
				$months_arr2[] = $i;

			$years_arr = Array();

			$Y = (int) date("Y");
			for($i = ($Y-2); $i <= $Y; $i++)
				$years_arr[] = $i;


			$params['from_day'] = putSelectBox($days_arr, $days_arr, date("d", $from_time));
			$params['to_day'] = putSelectBox($days_arr, $days_arr, date("d", $to_time));

			$params['from_month'] = putSelectBox($months_arr1, $months_arr2, date("m", $from_time));
			$params['to_month'] = putSelectBox($months_arr1, $months_arr2, date("m", $to_time));

			$params['from_year'] = putSelectBox($years_arr, $years_arr, date("Y", $from_time));
			$params['to_year'] = putSelectBox($years_arr, $years_arr, date("Y", $to_time));

			$params['curr_page'] = htmlentities($_SERVER['REQUEST_URI']);

			$res = $this->parse_form("time_range", $params);
			return $res;
		}

		public function parseTimeRange() {

			$fd = (int) $_REQUEST['fd'];
			$fm = (int) $_REQUEST['fm'];
			$fy = (int) $_REQUEST['fy'];

			if($fd && $fm && $fy) {
				$this->from_time = (int) strtotime($fy . "-" . $fm . "-" . $fd);
				setcookie("from_time", $this->from_time, 0, "/");
			} else {
					if($_COOKIE['from_time']) $this->from_time = (int) $_COOKIE['from_time'];
			}

			$td = (int) $_REQUEST['td'];
			$tm = (int) $_REQUEST['tm'];
			$ty = (int) $_REQUEST['ty'];

			if($td && $tm && $ty) {
				$this->to_time = (int) strtotime($ty . "-" . $tm . "-" . $td);
				if ($this->to_time <= $this->from_time) {
					$this->to_time = strtotime('+1 day', $this->from_time);
				}

				setcookie("to_time", $this->to_time, 0, "/");
			} else {
				if($_COOKIE['to_time']) $this->to_time = (int) $_COOKIE['to_time'];
			}
		}

	};

?>