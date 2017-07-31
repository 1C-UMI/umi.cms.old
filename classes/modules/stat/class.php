<?php
	class stat extends def_module {
		private $isStatCollected = false;

		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__stat");

				$this->__loadLib("__popular.php");
				$this->__implement("__popular_stat");

				$this->__loadLib("__visitors.php");
				$this->__implement("__visitors_stat");

				$this->__loadLib("__sources.php");
				$this->__implement("__sources_stat");

				$this->__loadLib("__phrases.php");
				$this->__implement("__phrases_stat");

				$this->__loadLib("__seo.php");
				$this->__implement("__seo_stat");


				$this->sheets_add("Сводка","total");
				$this->sheets_add("Популярность страниц","popular_pages");
				$this->sheets_add("Источники","sources");
				$this->sheets_add("Посетители","visitors");
				$this->sheets_add("SEO","engines");
				$this->sheets_add("Поисковые фразы","phrases");

				$this->items_per_page = regedit::getInstance()->getVal("//modules/stat/items_per_page");
				$this->items_per_page = 25; //($this->items_per_page > 0 ? $this->items_per_page: 25);
				$this->per_page = 25;
			} else {
				$this->__loadLib("__tags_cloud.php");
				$this->__implement("__tags_cloud_stat");

				$this->__loadLib("__json.php");
				$this->__implement("__json_stat");

				session_start();
			}

			$this->ts = time();
			$this->from_time = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
			$this->to_time = strtotime('+1 day', $this->from_time);


			require_once dirname(__FILE__) . '/classes/simpleStat.php';
			require_once dirname(__FILE__) . '/classes/statistic.php';
			require_once dirname(__FILE__) . '/classes/statisticFactory.php';
			require_once dirname(__FILE__) . '/classes/xml/xmlDecorator.php';


			$this->enabled = regedit::getInstance()->getVal("//modules/stat/collect");
			$this->mode = cmsController::getInstance()->getCurrentMode();
		}

		public function __destruct() {
			if($this->mode == "" && !$this->isStatCollected) {
				$this->pushStat();
			}
		}
	

		public function remove_to_temp() {
			$regedit = regedit::getInstance();
			$max_days = $regedit->getVal("//modules/stat/delete_after");

			$max_secs = $max_days * 3600 * 24;
			$time_from = time() - $max_secs;

			$sql = "INSERT INTO cms_stat_old SELECT * FROM cms_stat WHERE entrytime < " . $time_from;
			mysql_query($sql);

			$sql = "DELETE FROM cms_stat WHERE entrytime < " . $time_from;
			mysql_query($sql);
		}


		public function pushStat() {
			$this->isStatCollected = true;

			if(!$this->enabled) {
				return false;
			}

			$element_id = cmsController::getInstance()->getCurrentElementId();
			if($element = umiHierarchy::getInstance()->getElement($element_id)) {
				$tags = $element->getValue("tags");
			} else {
				return false;
			}

			$stat = new statistic();

			$stat->setReferer($_SERVER['HTTP_REFERER']);
			$stat->setUri($_SERVER['REQUEST_URI']);
			$stat->setServerName($_SERVER['SERVER_NAME']);
			$stat->setRemoteAddr($_SERVER['REMOTE_ADDR']);
			
			if($users_inst = cmsController::getInstance()->getModule("users")) {
				if($users_inst->is_auth()) {
					$stat->doLogin();
				}
			}

			foreach($tags as $tag) {
				$stat->event($tag);
			}
			$stat->run();
		}


		public function config() {
			return __stat::config();
		}
		
		
		public function isStringCP1251($str) {
			$sz = strlen($str);

			for($i = 0; $i < $sz; $i++) {
				$o = ord(substr($str, $i, 1));
				if((!($o >= 32 && $o <= 122)) && !($o >= 192 && $o <= 255)) {
					return false;
				}
			}
			return true;
		}


		public function getCurrentUserTags() {
			$stat_user_id = $_SESSION['stat']['user_id'];

			$factory = new statisticFactory(dirname(__FILE__) . '/classes');
			$factory->isValid('userTags');
			$report = $factory->get('userTags');
			$fromTS = $this->ts;
			$report->setStart($this->from_time);
			$report->setFinish($this->to_time);
			$report->setParams(Array("user_id" => $stat_user_id));
			$user_info = $report->get();
			
			return $user_info['labels'];
		}
	};

?>