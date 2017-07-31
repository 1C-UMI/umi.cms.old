<?php
	interface iUmiFieldsCollection {
		public function addField($name, $title, $fieldTypeId, $isVisible = true, $isLocked = false, $isInheritable = false);
		public function delField($field_id);
		public function getField($fieldId);
	}
?>