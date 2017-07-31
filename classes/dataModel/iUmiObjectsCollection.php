<?php
	interface iUmiObjectsCollection {
		public function getObject($objectId);
		public function addObject($name, $typeId, $isLocked = false);
		public function delObject($objectId);

		public function getGuidedItems($guideId);
	}
?>