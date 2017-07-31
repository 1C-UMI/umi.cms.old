<?php
	interface iUmiObject {
		public function getName();
		public function setName($name);

		public function getIsLocked();
		public function setIsLocked($isLocked);

		public function getTypeId();
		public function setTypeId($typeId);

		public function getPropGroupId($groupName);
		public function getPropGroupByName($groupName);
		public function getPropGroupById($groupId);

		public function getPropByName($propName);
		public function getPropById($propId);

		public function isPropertyExists($id);


		public function getValue($propName);
		public function setValue($propName, $propValue);

		public function setOwnerId($ownerId);
		public function getOwnerId();
	}
?>