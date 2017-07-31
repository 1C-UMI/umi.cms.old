<?php

abstract class __webforms {

	public function addresses() {
		$this->load_forms();
		$params = Array();

		$sql = "SELECT * FROM cms_webforms";
		$result = mysql_query($sql);

		$rows = "";

		while($row = mysql_fetch_assoc($result)) {
			$id    = $row['id'];
			$email = $row['email'];
			$descr = $row['descr'];


			$rows .= <<<ROWS

	<row>
		<col>
			<input style="width: 96%">
				<name><![CDATA[emails[{$id}]]]></name>
				<value><![CDATA[{$email}]]></value>
			</input>
		</col>

		<col>
			<input style="width: 96%">
				<name><![CDATA[descrs[{$id}]]]></name>
				<value><![CDATA[{$descr}]]></value>
			</input>
		</col>

		<col style="text-align: center">
			<checkbox>
				<name><![CDATA[dels[{$id}]]]></name>
				<value><![CDATA[1]]></value>
			</checkbox>
		</col>
	</row>

ROWS;

		}

		$params['rows'] = $rows;

		return $this->parse_form("addrs", $params);
	}

	public function addr_upd() {
		$email_new = $_REQUEST['email_new'];
		$descr_new = $_REQUEST['descr_new'];

		if($email_new && $descr_new) {
			$email_new = umiObjectProperty::filterInputString($email_new);
			$descr_new = umiObjectProperty::filterInputString($descr_new);

			$sql = <<<NEW

INSERT INTO cms_webforms
	(email, descr)
		VALUES('$email_new', '$descr_new')

NEW;
			mysql_query($sql);
		}


		$emails = $_REQUEST['emails'];
		$descrs = $_REQUEST['descrs'];

		if(is_array($emails)) {
			foreach($emails as $id => $email) {
				$descr = umiObjectProperty::filterInputString($descrs[$id]);
				$email = umiObjectProperty::filterInputString($email);

				$sql = <<<UPDATE

UPDATE cms_webforms SET
			email = '$email',
			descr = '$descr'
				WHERE id='$id'

UPDATE;
				mysql_query($sql);
			}
		}

		$dels = $_REQUEST['dels'];
		if(is_array($dels)) {
			foreach($dels as $id => $nl) {
				$sql = "DELETE FROM cms_webforms WHERE id='$id'";
				mysql_query($sql);
			}
		}

		$this->redirect($this->pre_lang . "/admin/webforms/");
	}

};

?>