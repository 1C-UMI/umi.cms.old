<?php

abstract class __eshop_discounts {

	public function discounts() {
		$params = Array();
		$this->load_forms();

		$rows = "";
		$sql = "SELECT * FROM cms_eshop_discounts";
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
			$sql1 = "SELECT ct.name, ct.object_id FROM cms_eshop_discounts_rels edr, cms_catalog_tree ct WHERE edr.discount_id = '{$row['id']}' AND ct.id = edr.rel_id";
			$result1 = mysql_query($sql1);
			$items = "";
			while(list($iname, $obj_id) = mysql_fetch_row($result1)) {
				$img = ($obj_id) ? '<img src="/images/cms/admin/full/ico_news_item.gif" />' : '<img src="/images/cms/admin/full/ico_news_lent.gif" />';

				$tmp = $img . "&nbsp;" . $iname . ";";
				$tmp = "<a href='#'>" . $tmp . "</a>";
				$tmp = "<div style='white-space: nowrap; display: inline;'>" . $tmp . "</div> ";
				$items .= $tmp;
			}
//			$items = ($items) ? substr($items, 0, strlen($items) - 6) . "</a>" : "";

			$work_interval = "";
			if($row['start_time']) {
				$work_interval = "Начало: " . date("Y-m-d H:i", $row['start_time']) . "<br />";
			}

			if($row['end_time']) {
				$work_interval .= "Конец: " . date("Y-m-d H:i", $row['end_time']) . "";
			}


			$rows .= <<<ROW

	<row>
		<col>
			<a href="%pre_lang%/admin/eshop/discounts_edit/{$row['id']}">{$row['title']}</a>
		</col>

		<col>
			$items
		</col>

		<col style="text-align: center;">
			{$row['discount_size']}%
		</col>

		<col>
			$work_interval
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/eshop/discounts_del/{$row['id']}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="%delete%" title="%delete%" /></a>
		</col>
	</row>

ROW;
		}


		$params['rows'] = $rows;
		return $this->parse_form("discounts", $params);
	}

	public function discounts_add() {
		$params = Array();
		$this->sheets_set_active("discounts");
		$this->load_forms();

		$params['method'] = "discounts_add_do";
		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		return $this->parse_form("discounts_add", $params);
	}

	public function discounts_add_do() {
		$title = utf8_1251($_REQUEST['title']);
		$discount_size = (int) $_REQUEST['discount_size'];
		$is_active = (int) $_REQUEST['is_active'];
		$start_time = utf8_1251($_REQUEST['start_time']);
		$end_time = utf8_1251($_REQUEST['end_time']);

		$items = $_REQUEST['cat_getter_items'];


		$start_time = ($start_time) ? toTimeStamp($start_time) : "";
		$end_time = ($end_time) ? toTimeStamp($end_time) : "";

		$sql = <<<SQL
INSERT INTO cms_eshop_discounts (title, discount_size, start_time, end_time, is_active)
	VALUES('$title', '$discount_size', '$start_time', '$end_time', '$is_active')
SQL;
		mysql_query($sql);
		$newid = (int) mysql_insert_id();

		$sql = "DELETE FROM cms_eshop_discounts_rels WHERE discount_id = '$newid'";
		mysql_query($sql);

		foreach($items as $iid) {
			$sql = "SELECT object_id FROM cms_catalog_tree WHERE id = '$iid'";
			$result = mysql_query($sql);
			list($object_id) = mysql_fetch_row($result);
			$is_object = (int) (bool) $object_id;

			$sql = <<<SQL
INSERT INTO cms_eshop_discounts_rels (discount_id, rel_id, is_object)
	VALUES('$newid', '$iid', '$is_object')
SQL;
			mysql_query($sql);
		}

		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/eshop/discounts/");
		} else {
			$this->redirect($this->pre_lang . "/admin/eshop/discounts_edit/" . $newid . "/");
		}
	}

	public function discounts_edit() {
		$params = Array();
		$this->sheets_set_active("discounts");
		$this->load_forms();

		$discount_id = (int) $_REQUEST['param0'];

		$sql = "SELECT * FROM cms_eshop_discounts WHERE id = '$discount_id'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		$params = array_merge($params, $row);

		$params['start_time'] = ($row['start_time']) ? date("Y-m-d H:i", $row['start_time']) : "";
		$params['end_time'] = ($row['end_time']) ? date("Y-m-d H:i", $row['end_time']) : "";

		$sql = "SELECT ct.name, edr.rel_id, edr.is_object FROM cms_eshop_discounts_rels edr, cms_catalog_tree ct WHERE edr.discount_id = '$discount_id' AND ct.id = edr.rel_id";
		$result = mysql_query($sql);

		$cat_getter_items = "";
		while(list($iname, $iid, $is_object) = mysql_fetch_row($result)) {
			$cat_getter_items .= ($is_object) ? "<obj value=\"{$iid}\">{$iname}</obj>\r\n" : "<cat value=\"{$iid}\">{$iname}</cat>\r\n";
		}


		$params['cat_getter_items'] = $cat_getter_items;
		$params['method'] = "discounts_edit_do";
		$params['discount_id'] = $discount_id;
		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		return $this->parse_form("discounts_add", $params);
	}

	public function discounts_edit_do() {
		$title = utf8_1251($_REQUEST['title']);
		$discount_size = (int) $_REQUEST['discount_size'];
		$is_active = (int) $_REQUEST['is_active'];
		$start_time = utf8_1251($_REQUEST['start_time']);
		$end_time = utf8_1251($_REQUEST['end_time']);

		$start_time = ($start_time) ? toTimeStamp($start_time) : "";
		$end_time = ($end_time) ? toTimeStamp($end_time) : "";

		$discount_id = (int) $_REQUEST['param0'];

		$items = $_REQUEST['cat_getter_items'];

		$sql = <<<SQL
UPDATE cms_eshop_discounts SET title = '$title', discount_size = '$discount_size', start_time = '$start_time', end_time = '$end_time', is_active = '$is_active' WHERE id = '$discount_id'
SQL;

		mysql_query($sql);


		$sql = "DELETE FROM cms_eshop_discounts_rels WHERE discount_id = '$discount_id'";
		mysql_query($sql);

		foreach($items as $iid) {
			$sql = "SELECT object_id FROM cms_catalog_tree WHERE id = '$iid'";
			$result = mysql_query($sql);
			list($object_id) = mysql_fetch_row($result);
			$is_object = (int) (bool) $object_id;

			$sql = <<<SQL
INSERT INTO cms_eshop_discounts_rels (discount_id, rel_id, is_object)
	VALUES('$discount_id', '$iid', '$is_object')
SQL;
			mysql_query($sql);
		}

		if($_REQUEST['exit_after_save']) {
			$this->redirect($this->pre_lang . "/admin/eshop/discounts/");
		} else {
			$this->redirect($this->pre_lang . "/admin/eshop/discounts_edit/" . $discount_id . "/");
		}
	}

	public function discounts_del() {
		$discount_id = (int) $_REQUEST['param0'];

		$sql = "DELETE FROM cms_eshop_discounts WHERE id = '$discount_id'";
		mysql_query($sql);

		$sql = "DELETE FROM cms_eshop_discounts_rels WHERE discount_id = '$discount_id'";
		mysql_query($sql);

		$this->redirect($this->pre_lang . "/admin/eshop/discounts/");
	}
}

?>