<?php
	interface iUmiMail {
		public function __construct($template = "default");
		public function __destruct();

		public function addRecipient($recipientEmail, $recipientName = false);
		public function setFrom($fromEmail, $fromName = false);

		public function setContent($contentString);
		public function setTxtContent($sTxtContent);
		public function setSubject($subjectString);
		public function setPriorityLevel($priorityLevel = "normal");
		public function setImportanceLevel($importanceLevel = "normal");

		public function getHeaders($arrXHeaders = array(), $bOverwrite = false);

		public function attachFile(umiFile $file);

		public function commit();
		public function send();

		public static function clearFilesCahce();
		public static function checkEmail($emailString);
	}
?>