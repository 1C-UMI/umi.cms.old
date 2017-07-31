<?php
	abstract class __mails_config {
		public function mails() {
			$params = Array();
			$this->load_forms();

			$regedit = regedit::getInstance();
			$email_from = $regedit->getVal("//settings/email_from");
			$fio_from = $regedit->getVal("//settings/fio_from");

			$params['email_from'] = $email_from;
			$params['fio_from'] = $fio_from;

			return $this->parse_form("mails", $params);
		}

		public function mails_do() {
			$email_from = utf8_1251($_REQUEST['email_from']);
			$fio_from = utf8_1251($_REQUEST['fio_from']);

			$regedit = regedit::getInstance();

			$regedit->setVar("//settings/email_from", $email_from);
			$regedit->setVar("//settings/fio_from", $fio_from);

			$this->redirect($this->pre_lang . "/admin/config/mails");
		}
		
	};
?>