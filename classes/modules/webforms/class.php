<?php

class webforms extends def_module {

	public function __construct() {
                parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__webforms");
		} else {
			$this->__loadLib("__custom.php");
			$this->__implement("__custom_webforms");
		}
	}

	public function insert($who = "", $template = "default") {
		$who = trim($who);

		if(!$template) $template = "default";
		list($template_block, $template_to_block, $template_to_line) = def_module::loadTemplates("tpls/webforms/{$template}.tpl", "webforms_block", "webforms_to_block", "webforms_to_line");

		$sql = "SELECT * FROM cms_webforms";
		$result = mysql_query($sql);

		$lines = "";
		while($row = mysql_fetch_assoc($result)) {
			$from = Array("%text%", "%id%");
			$to =   Array($row['descr'], $row['id']);
			

			$lines .= str_replace($from, $to, $template_to_line);
		}

		$res_to = str_replace("%lines%", $lines, $template_to_block);

		if($who) {
			if(is_numeric($who)) {
				$res_to = "<input type='hidden' name='email_to' value='" . $who . "' />";
			} else {
				$sql = "SELECT id FROM cms_webforms WHERE email='$who'";
				$result = mysql_query($sql);
				if($row = mysql_fetch_assoc($result)) {
					$res_to = "<input type='hidden' name='email_to' value='" . $row['id'] . "' />";
				} else {
					$res_to = "<input type='hidden' name='email_to' value='" . $who . "' />";
				}
			}
		}


		$block_arr = Array();
		$block_arr['to_block'] = $res_to;
		$block_arr['template'] = $template;

		return self::parseTemplate($template_block, $block_arr);
	}

	public function post() {
		global $_FILES;

		$res = "";

		$email_to = $_REQUEST['email_to'];
		$message = $_REQUEST['message'];
		$data = $_REQUEST['data'];

		$domain = $_REQUEST['domain'];

		$subject = cmsController::getInstance()->getCurrentDomain()->getHost();


		if(is_numeric($email_to)) {
			$sql = "SELECT email FROM cms_webforms WHERE id=$email_to";
			$result = mysql_query($sql);
			list($to) = mysql_fetch_row($result);
		} else {
			$to = $email_to;
		}

		if(!$data['email_from'] && $data['email']) {
			$data['email_from'] = $data['email'];
		}


		$someMail = new umiMail();
		$someMail->addRecipient($to);
		$someMail->setFrom($data['email_from'], $from);



		$mess = "";

		if(is_array($data)) {
			if($data['subject'])
				$subject = $data['subject'];

			if($data['fio'])
				$from = $data['fio'];

			if($data['fname'] || $data['lname'] || $data['mname'])
				$from = $data['lname'] . " " . $data['fname'] . " " . $data['mname'];
				
			if($email_from = $data['email_from']) {
				$email_from = $data['email_from'];
			}
			

			$mess = <<<END

<table border="0" width="100%">

END;

			if(is_array($_FILES['data']['name'])) {
				$data = array_merge($data, $_FILES['data']['name']);
			}
			
			foreach($data as $field => $cont) {
				if($filename = $_FILES['data']['name'][$field]) {
					$old_path = $_FILES['data']['tmp_name'][$field];
					$new_path = dirname($old_path) . "/" . $filename;
					move_uploaded_file($old_path, $new_path);

					$file = new umiFile($new_path);

					$someMail->attachFile($file);
				}

				if(!$cont) $cont = "&mdash;";
				
				$label = ($_REQUEST['labels'][$field]) ? $_REQUEST['labels'][$field] : ("%" . $field . "%");

				$mess .= <<<END

	<tr>
		<td width="30%">
			{$label}:
		</td>

		<td>
			{$cont}
		</td>
	</tr>

END;
			}

			$mess .= <<<END

</table>
<hr />

END;

		}

		$mess .= nl2br($message);

		if(!$from) {
			$from = regedit::getInstance()->getVal("//settings/fio_from");
		}
		
		if(!$from_email) {
		        $from_email = regedit::getInstance()->getVal("//settings/email_from");
		}
		
		$from = $from . "<" . $from_email . ">";

		$someMail->setSubject($subject);
		$someMail->setContent($mess);
		$someMail->commit();
		$someMail->send();


		if($template = (string) $_REQUEST['template']) {	//Sending auto-reply
			list($template_mail, $template_mail_subject) = def_module::loadTemplates("tpls/webforms/{$template}.tpl", "webforms_reply_mail", "webforms_reply_mail_subject");

			$email_from = regedit::getInstance()->getVal("//settings/email_from");
			$fio_from = regedit::getInstance()->getVal("//settings/fio_from");

			$replyMail = new umiMail();
			$replyMail->addRecipient($data['email_from'], $from);
			$replyMail->setFrom($email_from, $fio_from);
			$replyMail->setSubject($template_mail_subject);
			$replyMail->setContent($template_mail);
			$replyMail->commit();
			$replyMail->send();
		}

		$this->redirect($this->pre_lang . "/webforms/posted/");
	}

	public function posted() {
		$res = "";
		$res = "%webforms_thank_you%";
		return $res;
	}

};
?>
