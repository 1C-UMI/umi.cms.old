<?php
	interface iCmsController {

		public function loadBuildInModule($moduleName);

//		public function loadModule($moduleName);

		public function getModule($moduleName);

		public function installModule($moduleName);

		public function getSkinPath();


		public function getCurrentModule();
		public function getCurrentMethod();
		public function getCurrentElementId();
		public function getCurrentMode();
		public function getCurrentDomain();
		public function getCurrentLang();

		public function getLang();

		public function setCurrentModule($moduleName);
		public function setCurrentMethod($methodName);
	}
?>
