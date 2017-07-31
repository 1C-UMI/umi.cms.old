<?php

abstract class __eshop_dcards {

	public function dcards() {
		$params = Array();
		$this->load_forms();

		$rows = "";
		$sql = "SELECT id, num, size FROM cms_eshop_dcards ORDER BY num";
		$result = mysql_query($sql);
		while(list($id, $num, $size) = mysql_fetch_row($result)) {
			$rows .= <<<END
	<row>
		<col>
			<input type="text" name="card_num[{$id}]" quant="no" title="">{$num}</input>
		</col>

		<col>
			<input type="text" name="card_size[{$id}]" quant="no" title="">{$size}</input>
		</col>

		<col style="text-align: center;">
			<checkbox name="del[]" value="{$id}" />
		</col>
	</row>
END;
		}


		$params['rows'] = $rows;
		return $this->parse_form("dcards", $params);
	}

	public function dcards_do() {
		$num_new = (int) $_REQUEST['card_num_new'];
		$size_new = (int) $_REQUEST['card_size_new'];

		$nums = $_REQUEST['card_num'];
		$sizes = $_REQUEST['card_size'];

		$del = $_REQUEST['del'];

		foreach($nums as $id => $num) {
			$id = (int) $id;
			$num = (int) $num;
			$size = (int) $sizes[$id];

			$sql = "UPDATE cms_eshop_dcards SET num = '{$num}', size = '{$size}' WHERE id = '{$id}'";
			mysql_query($sql);
		}

		foreach($del as $id) {
			$id = (int) $id;

			$sql = "DELETE FROM cms_eshop_dcards WHERE id = '{$id}'";
			mysql_query($sql);
		}

		if($num_new && $size_new) {
			$sql = "INSERT INTO cms_eshop_dcards (num, size) VALUES('$num_new', '$size_new')";
			mysql_query($sql);
		}

		$this->redirect($this->pre_lang . "/admin/eshop/dcards/");
	}
}

?>