<?php

class updatesrv extends def_module {
	public function __construct() {
                parent::__construct();

		if(cmsController::getInstance()->getCurrentMode() == "admin") {
			$this->__loadLib("__admin.php");
			$this->__implement("__updatesrv");

			$this->__loadLib("__licenses.php");
			$this->__implement("__licenses_updatesrv");

			$this->sheets_add("Лицензии", "licenses");
		} else {
			$this->__loadLib("__server.php");
			$this->__implement("__server_updatesrv");

			$this->__loadLib("__updates.php");
			$this->__implement("__updates_updatesrv");
		}
	}


	public static function generateLicense($licenseCodeName, $domainName, $ipAddr = false) {
		if($ipAddr === false) {
			if(!($ipAddr = gethostbyname($domainName)) || $ipAddr == $domainName) {
				trigger_error("Failed to generate license: can't get ip by hostname", E_USER_WARNING);
				return false;
			}
		}

		$cs1 = md5($time = time());
		$cs2 = md5($ipAddr);
		$cs3 = NULL;

		switch($licenseCodeName) {
			case "old_free":
				$cs3 = md5($domainName);
				break;

			case "free":
				$cs3 = md5(md5(md5($domainName)));
				break;
				
			case "old_lite":
				$cs3 = md5(md5(md5(md5($domainName))));
				break;
				
			case "lite":
				$cs3 = md5(md5(md5(md5(md5($domainName)))));
				break;
				
			case "lite_plus":
			case "liteplus":
			case "freelance":
				$cs3 = md5(md5(md5(md5(md5(md5(md5($domainName)))))));
				break;

			case "pro":
				$cs3 = md5(md5(md5(md5(md5(md5(md5(md5(md5(md5($domainName))))))))));
				break;

			default:
				trigger_error("Failed to generate license: don't know license codename \"{$licenseCodeName}\"", E_USER_WARNING);
				return false;
				break;
		}

		$licenseKeyCode = strtoupper(substr($cs1, 0, 11) . "-" . substr($cs2, 0, 11) . "-" . substr($cs3, 0, 11));

		$res = Array	(
					"keycode"	=> $licenseKeyCode,
					"timestamp"	=> $time,
					"ip"		=> $ipAddr,
					"host"		=> $domainName
		);

		return $res;
	}


	public function generatePrimaryKeycode() {
		return strtoupper(substr(md5(uniqid()), 0, 11) . "-" . substr(md5(uniqid()), 0, 11) . "-" . substr(md5(uniqid()), 0, 11));
	}


	public function test() {
//		return $this->sendSiteUpdate(26345);
//		return $this->sendSiteUpdate(26359);
//		return $this->sendAllUpdates();
//		return $this->sendSiteUpdate(26345);
//		return $this->sendSiteUpdate(26363);
//		return $this->sendSiteUpdate(26337);	//bonanzaland.ru
//		return $this->sendSiteUpdate(26345);	//lyxsus.ru
//		return $this->sendAllUpdates();
//		return $this->sendSiteUpdate(30495);	//umi-cms.pl.ru
//		return $this->sendSiteUpdate(31059);	//rsport.ru
//		return $this->sendSiteUpdate(31202);	//umitest.kunstkamera.ru
//		return $this->sendSiteUpdate(31206);	//aquatorygroup.ru
//		return $this->sendSiteUpdate(31233);	//i-kids.ru
//		return $this->sendSiteUpdate(27076);	//test.umi-cms.ru
//		return $this->sendSiteUpdate(27107);	//wwwerh.ru
//		return $this->sendSiteUpdate(31206);	//aquatorygroup.ru
//		return $this->sendSiteUpdate(31919);	//install-pro-commerce.umi-cms.ru
//		return $this->sendSiteUpdate(27080);	//install-pro-corporate.umi-cms.ru
//		return $this->sendSiteUpdate(31214);	//test.webmaster.spb.ru
//		return $this->sendSiteUpdate(27078);	//www.umistudio.com
//		return $this->sendAllUpdates();
//		return $this->sendSiteUpdate(27099);	//www.timetolive.ru
//		return $this->sendSiteUpdate(31805);	//mekong-com.1gb.ru
//		return $this->sendAllUpdates();
//		return $this->sendSiteUpdate(31914);	//sam-site.ru
		return $this->sendAllUpdates();

	}
};
?>