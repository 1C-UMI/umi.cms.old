<?php
	abstract class __backup {
		public $root;
    
		public function backup_panel($changed_module = "", $changed_method = "", $cparam = "") {
			if(!regedit::getInstance()->getVal("modules/backup/enabled")) {
				return false;
			}

			$this->load_forms();
			$params = Array();

			if(!$changed_module) {
				$changed_module = "content";
			}

			if(!$changed_method) {
				$changed_method = "edit_page_do";
			}

			$limit = regedit::getInstance()->getVal("//modules/backup/max_save_actions");

			$sql = "SELECT id, ctime, user_id, is_active FROM cms_backup WHERE changed_module='" . $changed_module . "' AND changed_method='" . $changed_method . "' AND param='" . $cparam . "' GROUP BY param0 ORDER BY ctime DESC";
			$result = mysql_query($sql);

			$c = 0;
			$rows = "";
			while($row = mysql_fetch_assoc($result)) {
				$c++;

				$bid = $row['id'];
				$is_active = $row['is_active'];
				$cdata = date("Y-m-d | H:i:s", $row['ctime']);
				$uinfo = cmsController::getInstance()->getModule('users')->get_user_info($row['user_id'], "%lname% %fname% %father_name%");

				if($is_active) {
					$ct = "<b>" . $c . ".</b>";
					$cdata = "<b>" . $cdata . "</b>";
					$uinfo = "<b>" . $uinfo . "</b>";
				} else {
					$ct = $c . ".";
				}

				$rows .= <<<END

    <row>
	<col align="center">$ct</col>
	<col>$cdata</col>
	<col>$uinfo</col>
	<col align="center"><button onclick="javascript: window.location = '%pre_lang%/admin/backup/rollback/$bid/'" title="Откатить"></button></col>
    </row>

END;
			}

			if(!$rows) {
				$rows = <<<END
	<row>
		<col colspan="4" align="center">%backup_nodata%</col>
	</row>

END;
			}


			$params['rows'] = $rows;
			return $this->parse_form("backup_history", $params);
		}

		public function rollback() {
			if(!regedit::getInstance()->getVal("//modules/backup/enabled")) {
				return false;
			}

			$bid = (int) $_REQUEST['param0'];

			$sql = "SELECT * FROM cms_backup WHERE id='$bid' LIMIT 1";
			$result = mysql_unbuffered_query($sql);

			if($row = mysql_fetch_assoc($result)) {
				$changed_module = $row['changed_module'];
				$changed_method = $row['changed_method'];
				$changed_param = $row['param'];
				$ser = $row['param0'];

				$sql = "UPDATE cms_backup SET is_active='0' WHERE changed_module='" . $changed_module . "' AND changed_method='" . $changed_method . "' AND param='" . $changed_param . "'";
				mysql_unbuffered_query($sql);

				$sql = "UPDATE cms_backup SET is_active='1' WHERE id='" . $bid . "'";
				mysql_unbuffered_query($sql);

				$_temp = unserialize($ser);
				$_REQUEST = Array();

				foreach($_temp as $cn => $cv) {
					if(!is_array($cv)) {
						$cv = base64_decode($cv);
					} else {
						foreach($cv as $i => $v) {
							$cv[$i] = $v;
						}
					}
					$_REQUEST[$cn] = $cv;
				}

				$_REQUEST['rollbacked'] = true;
				$_REQUEST['exit_after_save'] = 0;

				if($changed_module_inst = cmsController::getInstance()->getModule($changed_module)) {
					return $changed_module_inst->cms_callMethod($changed_method, Array());
				} else {
					return "You can't rollback this action. No permission to this module.";
				}
			}
		}


		public function backup_save($cmodule = "", $cmethod = "", $cparam = "") {
			if(!regedit::getInstance()->getVal("//modules/backup/enabled")) return false;
			if($_REQUEST['rollbacked']) return false;

			$cuser_id = (cmsController::getInstance()->getModule('users')) ? $cuser_id = cmsController::getInstance()->getModule('users')->user_id : 0;


			$ctime = time();

			if(!$cmodule) {
				$cmodule = $_REQUEST['module'];
			}

			if(!$cmethod) {
				$cmethod = $_REQUEST['method'];
			}

//			if(!$cparam) {
//				$cparam = $_REQUEST['param0'];
//			}

			foreach($_REQUEST as $cn => $cv) {
				$_temp[$cn] = (!is_array($cv)) ? base64_encode($cv) : $cv;
			}

			$req = serialize($_temp);

			$sql = "UPDATE cms_backup SET is_active='0' WHERE changed_module='" . $cmodule . "' AND changed_method='" . $cmethod . "' AND param='" . $cparam . "'";
			mysql_unbuffered_query($sql);

			$sql = <<<SQL
INSERT INTO cms_backup (ctime, changed_module, changed_method, param, param0, user_id, is_active) 
				VALUES('{$ctime}', '{$cmodule}', '{$cmethod}', '{$cparam}', '{$req}', '{$cuser_id}', '1')
SQL;
			mysql_unbuffered_query($sql);

			$limit = regedit::getInstance()->getVal("//modules/backup/max_save_actions");
			$sql = "SELECT COUNT(*) FROM cms_backup WHERE changed_module='" . $cmodule . "' AND changed_method='" . $cmethod . "' AND param='" . $cparam . "' ORDER BY ctime DESC";
			$result = mysql_query($sql);
			list($total_b) = mysql_fetch_row($result);
		
			$time_limit = regedit::getInstance()->getVal("//modules/backup/max_timelimit");
		
			$td = $total_b - $limit;
			if($td < 0) {
				$td = 0;
			}

			$sql = "DELETE FROM cms_backup WHERE changed_module='" . $cmodule . "' AND changed_method='" . $cmethod . "' AND param='" . $cparam . "' ORDER BY ctime ASC LIMIT " . ($td);
			mysql_query($sql);
		
			$end_time=$time_limit*3600*24;
			$sql="DELETE FROM cms_backup WHERE changed_module='" . $cmodule . "' AND changed_method='" . $cmethod . "' AND param='" . $cparam . "' AND (".time()."-ctime)>".$end_time." ORDER BY ctime ASC";
			mysql_query($sql);

			return true;
		}

		public function config(){
			$this->sheets_reset();
			$this->load_forms();
			$params = Array();

			$regedit = regedit::getInstance();

			$params['enabled'] = $regedit->getVal("//modules/backup/enabled");
			$params['max_timelimit'] = $regedit->getVal("//modules/backup/max_timelimit");
			$params['max_save_actions'] = $regedit->getVal("//modules/backup/max_save_actions");
		
			return $this->parse_form("config", $params);
		}


		public function config_do() {
			$enabled = (int) $_REQUEST['enabled'];
			$max_timelimit = (int) $_REQUEST['max_timelimit'];
			$max_save_actions = (int) $_REQUEST['max_save_actions'];


			$regedit = regedit::getInstance();

			$regedit->setVar("//modules/backup/enabled", $enabled);
			$regedit->setVar("//modules/backup/max_timelimit", $max_timelimit);
			$regedit->setVar("//modules/backup/max_save_actions", $max_save_actions);

			$this->redirect($this->pre_lang . "/admin/backup/config/");
		}
	};
?>