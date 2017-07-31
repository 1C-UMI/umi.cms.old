<?php
	interface iTemplatesCollection {
		public function addTemplate($filename, $title, $domainId = false, $langId = false, $isDefault = false);
		public function delTemplate($templateId);


		public function getDefaultTemplate($domain_id = false, $lang_id = false);
		public function setDefaultTemplate($template_id, $domain_id = false, $lang_id = false);

		public function getTemplatesList($domainId, $langId);

		public function getTemplate($templateId);
	}
?>