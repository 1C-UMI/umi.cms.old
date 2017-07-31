<?php

abstract class __eshop_stores {

	public function stores() {
		$params = Array();
		$this->load_forms();

		$sql = "SELECT * FROM cms_eshop_stores_list ORDER BY id";
		$result = mysql_query($sql);
		$rows = "";
		while($row = mysql_fetch_assoc($result)) {
			$rows .= <<<ROW

	<row>
		<col>
			<b>{$row['id']}.</b>
		</col>

		<col>
			<input type="text" quant="no" name="store_name[{$row['id']}]" style="width: 97%;">{$row['name']}</input>
		</col>

		<col style="text-align: center;">
			<checkbox name="del[{$row['id']}]" value="1" />
		</col>
	</row>

ROW;
		}

		$params['rows'] = $rows;
		return $this->parse_form("stores", $params);
	}

	public function stores_do() {
		$store_name = $_REQUEST['store_name'];
		$del = $_REQUEST['del'];

		foreach($store_name as $id => $name) {
			$id = (int) $id;
			$name = utf8_1251($name);

			$sql = "UPDATE cms_eshop_stores_list SET name = '$name' WHERE id='$id'";
			mysql_query($sql);
		}

		foreach($del as $id => $nl) {
			$id = (int) $id;

			$sql = "DELETE FROM cms_eshop_stores_list WHERE id = '$id'";
			mysql_query($sql);

			$sql = "DELETE FROM cms_eshop_stores WHERE store_id = '$id'";
			mysql_query($sql);
		}

		$new_id = (int) $_REQUEST['new_id'];
		$new_name = utf8_1251($_REQUEST['new_name']);

		if($new_name) {
			if($new_id)
				$sql = "INSERT INTO cms_eshop_stores_list (id, name) VALUES('$new_id', '$new_name')";
			else
				$sql = "INSERT INTO cms_eshop_stores_list (name) VALUES('$new_name')";
			mysql_query($sql);
		}

		$this->redirect($this->pre_lang . "/admin/eshop/stores/");
	}
}

?>