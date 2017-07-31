<?php
	interface iUmiHierarchyElement {
		public function getIsDeleted();
		public function setIsDeleted($isDeleted = false);

		public function getIsActive();
		public function setIsActive($isActive = true);

		public function getIsVisible();
		public function setIsVisible($isVisible = true);

		public function getTypeId();
		public function setTypeId($typeId);

		public function getLangId();
		public function setLangId($langId);

		public function getTplId();
		public function setTplId($tplId);

		public function getDomainId();
		public function setDomainId($domainId);

		public function getUpdateTime();
		public function setUpdateTime($timeStamp = 0);

		public function getOrd();
		public function setOrd($ord);

		public function getRel();
		public function setRel($rel_id);

		public function getObject();
		public function setObject(umiObject $object);

		public function setAltName($altName, $autoConvert = true);
		public function getAltName();

		public function setIsDefault($isDefault = true);
		public function getIsDefault();

		public function getParentId();

		public function getValue($propName);
		public function setValue($propName, $propValue);

		public function getFieldId($FieldName);

		public function getName();
	}
?>