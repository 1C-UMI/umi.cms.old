<?php
	class autoupdate extends def_module {
		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__autoupdate");
			}

			if(!is_dir("./updates")) {
				mkdir("./updates");
			}
		}


		public function service () {
			$event = strtoupper($_REQUEST['param0']);

			$this->checkIsValidSender();

			switch($event) {
				case "STATUS":
					$result = $this->returnStatus();
					break;

				case "VERSION":
					$result = $this->returnVersions();
					break;

				case "LAST_UPDATED":
					$result = $this->returnLastUpdated();
					break;

				case "PACKAGE":
					$result = $this->acceptPackage();
					break;

				case "COMMIT":
					$result = $this->commitPackage();
					break;

				default:
					$result = "UNHANDLED_EVENT";
					break;
			}


			$this->flush($result, "text/plain");
			//TODO
		}


		protected function returnStatus () {
			return (string) regedit::getInstance()->getVal("//modules/autoupdate/status");
		}


		protected function returnVersions() {
			$regedit = regedit::getInstance();
			return (string) $regedit->getVal("//modules/autoupdate/system_version") . "\n" . $regedit->getVal("//modules/autoupdate/system_build");
		}


		public function returnLastUpdated() {
			return (string) $this->getLastUpdated();
		}


		protected function checkIsValidSender () {
			//TODO
		}


		public function getCurrentVersion () {
			return (string) regedit::getInstance()->getVal("//modules/autoupdate/system_version");
		}


		public function setCurrentVersion ($version) {
			regedit::getInstance()->setVal("//modules/autoupdate/system_version", (string) $version);
		}


		public function getLastUpdated () {
			return (string) (int) regedit::getInstance()->getVal("//modules/autoupdate/last_updated");
		}


		public function setLastUpdated ($time) {
			regedit::getInstance()->setVal("//modules/autoupdate/last_updated", (int) $time);
		}


		public function acceptPackage () {
			global $_FILES;

			if(!is_array($_FILES)) {
				return "NO_PACKAGE";
			}

			$package_name = $_FILES['package']['name'];

			if(substr($package_name, -4, 4) != ".ucp") {
				return "WRONG_PACKAGE";
			}

			move_uploaded_file($_FILES['package']['tmp_name'], "./updates/" . $package_name);

			return "OK";
		}


		public function commitPackage () {
			$package_name = (string) $_REQUEST['param1'];

			include "classes/umiDistr/umiDistrReader.php";
			include "classes/umiDistr/umiDistrInstallItem.php";
			include "classes/umiDistr/umiDistrFile.php";
			include "classes/umiDistr/umiDistrFolder.php";

			$distr = new umiDistrReader("./updates/update_{$package_name}.ucp");
			
			if($package_name) {
				regedit::getInstance()->setVar("//modules/autoupdate/system_build", $package_name);
				regedit::getInstance()->setVar("//modules/autoupdate/last_updated", time());
			}

			return "OK";
		}
	};
?>
