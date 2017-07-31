<?php

abstract class __eshop_credits {
	public function credits_add() {
		$params = Array('title'=>'');
		$this->load_forms();
		$this->sheets_set_active("credits");

		$params['method'] = 'credits_add_do';
		$params['save_n_save'] = '<submit title="Добавить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Добавить" onclick="javascript: return save_without_exit(); " />';
		return $this->parse_form("credits_add", $params);
	}

	public function credits_add_do() {
		$title = utf8_1251($_REQUEST['title']);
		$descr = utf8_1251($_REQUEST['descr']);
		$min_price = (int) $_REQUEST['min_price'];
		$months = (int) $_REQUEST['months'];
		$first_pay = (int) $_REQUEST['first_pay'];
		$month_pay = (int) $_REQUEST['month_pay'];

		$sql = "INSERT INTO cms_eshop_credits (title, descr, min_price, months, first_pay, month_pay)
						VALUES('$title', '$descr', '$min_price', '$months', '$first_pay', '$month_pay')";
		mysql_query($sql);
		$programm_id = mysql_insert_id();

		$exit_after_save = $_REQUEST['exit_after_save'];
		if($exit_after_save) {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/credits/");
		} else {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/credits_edit/" . $programm_id . "/");
		}
	}

	public function credits_edit() {
		$params = Array();
		$this->load_forms();
		$this->sheets_set_active("credits");

		$programm_id = (int) $_REQUEST['param0'];

		$sql = "SELECT * FROM cms_eshop_credits WHERE id = '$programm_id'";
		$result = mysql_query($sql);
		if($row = mysql_fetch_assoc($result)) {
			$params = array_merge($params, $row);
		}


		$params['programm_id'] = $programm_id;
		$params['method'] = 'credits_edit_do';
		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		return $this->parse_form("credits_add", $params);
	}

	public function credits_edit_do() {
		$title = utf8_1251($_REQUEST['title']);
		$descr = utf8_1251($_REQUEST['descr']);
		$min_price = (int) $_REQUEST['min_price'];
		$months = (int) $_REQUEST['months'];
		$first_pay = (int) $_REQUEST['first_pay'];
		$month_pay = (int) $_REQUEST['month_pay'];

		$programm_id = (int) $_REQUEST['param0'];


		$sql = "UPDATE cms_eshop_credits SET title = '$title', descr = '$descr', min_price = '$min_price', months = '$months', first_pay = '$first_pay', month_pay = '$month_pay'
						WHERE id = '$programm_id'";
		mysql_query($sql);

		$exit_after_save = $_REQUEST['exit_after_save'];
		if($exit_after_save) {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/credits/");
		} else {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/eshop/credits_edit/" . $programm_id . "/");
		}
	}

	public function credits() {
		$params = Array();
		$this->load_forms();

		$sql = "SELECT * FROM cms_eshop_credits";
		$result = mysql_query($sql);
		$rows = "";
		while($row = mysql_fetch_assoc($result)) {
			$rows .= <<<ROW

	<row>
		<col>
			{$row['title']}
		</col>

		<col style="text-align: center;">
			{$row['min_price']}
		</col>

		<col style="text-align: center;">
			{$row['months']}
		</col>

		<col style="text-align: center;">
			{$row['first_pay']}%
		</col>

		<col style="text-align: center;">
			{$row['month_pay']}%
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/eshop/credits_edit/{$row['id']}/"><img src="/images/cms/admin/%skin_path%/ico_edit.%ico_ext%" alt="Редактировать" title="Редактировать" border="0" /></a>
		</col>

		<col style="text-align: center;">
			<a href="%pre_lang%/admin/eshop/credits_del/{$row['id']}/" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" alt="Удалить" title="Удалить" border="0" /></a>
		</col>
	</row>

ROW;
		}

		$params['rows'] = $rows;
		return $this->parse_form("credits", $params);
	}

	public function credits_del() {
		$programm_id = (int) $_REQUEST['param0'];

		$sql = "DELETE FROM cms_eshop_credits WHERE id = '$programm_id'";
		mysql_query($sql);

		$this->redirect($this->pre_lang . "/admin/eshop/credits/");
	}
}

?>