<?php
	interface iData {
		public function getProperty($elementId, $propertyId, $template = "default", $is_random = false);
		public function getPropertyGroup($elementId, $propertyGroupId, $template = "default");
		public function getAllGroups($elementId, $template = "default");

		public function getPropertyOfObject($objectId, $propertyId, $template = "default", $is_random = false);
		public function getPropertyGroupOfObject($objectId, $propertyGroupId, $template = "default");
		public function getAllGroupsOfObject($objectId, $template = "default");

	}
?>