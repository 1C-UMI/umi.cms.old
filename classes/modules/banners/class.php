<?php
	class banners extends def_module implements iBanners{
		static $arrVisibleBanners = array();
		public function __construct() {
			parent::__construct();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				cmsController::getInstance()->getModule('users');
				$this->__loadLib("__admin.php");
				$this->__implement("__banners");

				$this->__loadLib("__banners.php");
				$this->__implement("__banners_banners");

				$this->__loadLib("__banners_add.php");
				$this->__implement("__banners_add_banners");


				$this->__loadLib("__banners_edit.php");
				$this->__implement("__banners_edit_banners");


				$this->__loadLib("__places.php");
				$this->__implement("__places_banners");

				$this->__loadLib("__places_add.php");
				$this->__implement("__places_add_banners");


				$this->__loadLib("__places_edit.php");
				$this->__implement("__places_edit_banners");


				$this->sheets_reset();
				$this->sheets_add("Баннеры", "banners_list");
				$this->sheets_add("Расположение", "places_list");
			} else {
				$this->__loadLib("__custom.php");
				$this->__implement("__custom_banners");
			}

			$this->per_page = 10;
		}

		public function insert($sPlace = "", $iMacrosID=0) {
			// insert banner in page
			$sResult = "";
			$arrBannersList = array();
			// filter
			$oBannersSelection = new umiSelection;
			// type filter
			$oBannersSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "banner")->getId();
			$iBannerTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oBannerType = umiObjectTypesCollection::getInstance()->getType($iBannerTypeId);

			$arrBannerTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($iHierarchyTypeId);

			foreach($arrBannerTypes as $iTypeId => $nop) {
				$oBannersSelection->addObjectType($iTypeId);
			}
			// sort by show_start_date
			$oBannersSelection->setOrderFilter();
			$iStartDateFldId = $oBannerType->getFieldId('show_start_date');
			$oBannersSelection->setOrderByProperty($iStartDateFldId);
			// property filters =======================================================
			$oBannersSelection->setPropertyFilter();
			// start_date filter
			$oBannersSelection->addPropertyFilterLess($iStartDateFldId, time());
			// active filter
			$iActiveFldId = $oBannerType->getFieldId('is_active');
			$oBannersSelection->addPropertyFilterEqual($iActiveFldId, true, true);
			// place filter
			$oPlacesSelection = new umiSelection;
			$oPlacesSelection->setObjectTypeFilter();
			$iPlHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "place")->getId();
			$arrPlaceTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($iPlHierarchyTypeId);
			foreach($arrPlaceTypes as $type_id => $nop) {
				$oPlacesSelection->addObjectType($type_id);
			}
			$oPlacesSelection->setNamesFilter();
			$oPlacesSelection->addNameFilterEquals($sPlace);
			$arrPlacesSelResults = umiSelectionsParser::runSelection($oPlacesSelection);
			if (!count($arrPlacesSelResults)) return "";
			$iPlaceFldId = $oBannerType->getFieldId('place');
			$bShowRandomBanner = false;
			for ($iI=0; $iI<count($arrPlacesSelResults); $iI++) {
				$iNextPlaceId = $arrPlacesSelResults[$iI];
				$oNextPlace = umiObjectsCollection::getInstance()->getObject($iNextPlaceId);
				$bShowRandomBanner = (bool) $oNextPlace->getValue('is_show_rand_banner');
				$oBannersSelection->addPropertyFilterEqual($iPlaceFldId, $arrPlacesSelResults[$iI], true);
				
			}
			// view pages filter
			$iViewPagesFldId = $oBannerType->getFieldId('view_pages');
			$iCurrPageId = cmsController::getInstance()->getCurrentElementId();
			$arrCurrPageParents = array();
			$arrCurrPageParentsIds = umiHierarchy::getInstance()->getAllParents($iCurrPageId, true);
			$oBannersSelectionWithNull = clone $oBannersSelection;
			$oBannersSelection->addPropertyFilterEqual($iViewPagesFldId, $arrCurrPageParentsIds, true);
			$oBannersSelectionWithNull->addPropertyFilterIsNull($iViewPagesFldId);
			//

			// do selection
			$arrSelResultsWithoutNull = umiSelectionsParser::runSelection($oBannersSelection);
			$arrSelResultsWithNull = umiSelectionsParser::runSelection($oBannersSelectionWithNull);
			$arrSelResults = array_merge($arrSelResultsWithoutNull, $arrSelResultsWithNull);
			$arrSelResults = array_unique($arrSelResults);
			// others filters =========================================================
			for ($iI=0; $iI<count($arrSelResults); $iI++) {
				$iNextBanId = $arrSelResults[$iI];
				$oNextBanner =  umiObjectsCollection::getInstance()->getObject($iNextBanId);
				if ($oNextBanner instanceof umiObject) {
					// max count views filter
					if ($oNextBanner->getValue('max_views') <= 0 || $oNextBanner->getValue('views_count') <= $oNextBanner->getValue('max_views')) {
						$bShowActual = true;
						// tags filter
						$arrBannerTags = $oNextBanner->getValue("tags");
						if (count($arrBannerTags)) {
							$iCurrPageId = cmsController::getInstance()->getCurrentElementId();
							$oCurrPage = umiHierarchy::getInstance()->getElement($iCurrPageId, true);
							if(is_object($oCurrPage)) {
								$arrPageTags = $oCurrPage->getValue("tags");
							} else {
								$arrPageTags = Array();
							}
							$arrCommonTags = array_intersect($arrBannerTags, $arrPageTags);
							if (!count($arrCommonTags)) $bShowActual = false;
						}
						// do show till filter
						$oShowTillDate = $oNextBanner->GetValue('show_till_date');
						if ($oShowTillDate instanceof umiDate && $oShowTillDate->timestamp) {
							if ($oShowTillDate->timestamp < $oShowTillDate->getCurrentTimeStamp()) {
								$bShowActual = false;
							}
						}
						if ($bShowActual) {
							// time-targeting filter =======================
							if ($oNextBanner->getValue('time_targeting_is_active')) {
								$oRanges = new ranges();
								// by month
								$sByMonth = $oNextBanner->getValue('time_targeting_by_month');
								if (strlen($sByMonth)) {
									$iCurrMonth = (int) date("m");
									$arrShowByMonth = $oRanges->get($sByMonth, 1);
									if (array_search($iCurrMonth, $arrShowByMonth)===false) $bShowActual = false;
								}
								// by month days
								$sByMonthDays = $oNextBanner->getValue('time_targeting_by_month_days');
								if (strlen($sByMonthDays) && $bShowActual) {
									$iCurrMonthDay = (int) date("d");
									$arrShowByMonthDays = $oRanges->get($sByMonthDays);
									if (array_search($iCurrMonthDay, $arrShowByMonthDays)===false) $bShowActual = false;
								}
								// by week days
								$sByWeekDays = $oNextBanner->getValue('time_targeting_by_week_days');
								if (strlen($sByWeekDays) && $bShowActual) {
									$iCurrWeekDay = (int) date("w");
									$arrShowByWeekDays = $oRanges->get($sByWeekDays);
									if (array_search($iCurrWeekDay, $arrShowByWeekDays)===false) $bShowActual = false;
								}
								// by hours
								$sByHours = $oNextBanner->getValue('time_targeting_by_hours');
								if (strlen($sByHours) && $bShowActual) {
									$iCurrHour = (int) date("G");
									$arrShowByHours = $oRanges->get($sByHours);
									if (array_search($iCurrHour, $arrShowByHours)===false) $bShowActual = false;
								}
							}
							// user tags filter
							if ($bShowActual) {
								$arrBannerTags = $oNextBanner->getValue("user_tags");
								if (is_array($arrBannerTags) && count($arrBannerTags)) {
									$arrGetTags = array();
									if ($oStat = cmsController::getInstance()->getModule("stat")) {
										$arrUserTags = array();
										$arrGetTags = $oStat->getCurrentUserTags();
										if (isset($arrGetTags["top"]) && is_array($arrGetTags["top"])) {
											foreach ($arrGetTags["top"] as $sTmp => $arrTagInfo) {
												if (isset($arrTagInfo["tag"])) $arrUserTags[] = $arrTagInfo["tag"];
											}
										}
										$iExceptTags = 0;
										$iAllowTags = 0;
										for ($iI=0; $iI < count($arrBannerTags); $iI++) {
											$sNextTag = $arrBannerTags[$iI];
											if (strpos($sNextTag, "!") !== false) {
												$sNextTag = substr($sNextTag ,1);
												if (in_array($sNextTag, $arrUserTags)) {
													$iExceptTags = 1;
													break;
												}
											} else {
												if (in_array($sNextTag, $arrUserTags)) $iAllowTags++;
											}
										}
										if ($iExceptTags || !$iAllowTags) $bShowActual = false;
									}
								}
							}
							if ($bShowActual) {
								$arrBannersList[] = $iNextBanId;
							}
						} else {
							//$oNextBanner->setValue('is_active', false);
						}
					} else {
						//$oNextBanner->setValue('is_active', false);
					}
				}
			}
			if (count($arrBannersList)) {
				$iShowBanId = 0;
				if (count($arrBannersList) > 1) {
					foreach ($this->arrVisibleBanners as $sNextPlace => $arrPlaceBanners) {
						$arrBannersList = array_diff($arrBannersList, $arrPlaceBanners);
					}
				}
				if ($bShowRandomBanner) {
					// random banner
					$iRandBanInd = array_rand($arrBannersList);
					$iShowBanId = $arrBannersList[$iRandBanInd];
				} else {
					reset($arrBannersList);
					$iShowBanId = current($arrBannersList);
				}
				$this->arrVisibleBanners[$sPlace][] = $iShowBanId;
				$sResult = self::renderBanner($iShowBanId);
			}

			return $sResult;
		}
		
		protected function renderBanner($iObjId) {
			//
			$sResult = "";
			$oBanner = umiObjectsCollection::getInstance()->getObject($iObjId);
			if ($oBanner instanceof umiObject) {
				//$iBannerTypeId = $oBanner->getTypeId();
				//$oBannerType = umiObjectTypesCollection::getInstance()->getType($iBannerTypeId);
				$sBannerType = "";
				if ($oBanner->getValue('swf') !== false) $sBannerType="swf";
				if ($oBanner->getValue('image') !== false) $sBannerType="image";
				if ($oBanner->getValue('html_content') !== false) $sBannerType="html";
				$sUrl =  $oBanner->getValue('url');
				$bOpenInNewWindow = $oBanner->getValue('open_in_new_window');
				switch ($sBannerType) {
					case "swf":
								$oImgFile = $oBanner->getValue('swf');
								if ($oImgFile instanceof umiImageFile && !$oImgFile->getIsBroken()) {
									// banner sizes
									$iWidth =  (int) $oBanner->getValue('width');
									$iHeight = (int) $oBanner->getValue('height');
									if ($iWidth<=0) $iWidth = $oImgFile->getWidth();
									if ($iHeight<=0) $iHeight = $oImgFile->getHeight();
									$sSwfSrc = $oImgFile->getFilePath(true);
									$sSwfTarget = ($oBanner->getValue('open_in_new_window')? "_blank": "_self");
									$sResult = <<<END
<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="$iWidth" height="$iHeight" id="$iObjId" align="middle">
	<param name="allowScriptAccess" value="sameDomain" />
	<param name="movie" value="{$sSwfSrc}?target={$sSwfTarget}&amp;link1={$sUrl}&amp;link={$sUrl}" />
	<param name="quality" value="high" /><param name="bgcolor" value="#ffffff" />
	<param name="wmode" value="transparent" />

	<embed src="{$sSwfSrc}?target={$sSwfTarget}&amp;link1={$sUrl}&amp;link={$sUrl}" quality="high" bgcolor="#ffffff" width="$iWidth" height="$iHeight" wmode="transparent" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />

</object>
END;
								}
								break;
					case "image":
								$oImgFile = $oBanner->getValue('image');
								if ($oImgFile instanceof umiImageFile && !$oImgFile->getIsBroken()) {
									// banner sizes
									$iWidth =  (int) $oBanner->getValue('width');
									$iHeight = (int) $oBanner->getValue('height');
									if ($iWidth<=0) $iWidth = $oImgFile->getWidth();
									if ($iHeight<=0) $iHeight = $oImgFile->getHeight();
									// 
									$sBannerImg = "<img src=\"".$oImgFile->getFilePath(true)."\" border=\"0\" alt=\"".$oBanner->getValue('alt')."\" width=\"".$iWidth."\" height=\"".$iHeight."\" />";
									$sUrl = $oBanner->getValue('url');
									$sResult = $sBannerImg;
									if (strlen($sUrl)) {
										$sResult = "<a href=\"".$this->pre_lang."/banners/go_to/".$iObjId."/\" ".(($bOpenInNewWindow)? "target=\"_blank;\"": "").">".$sBannerImg."</a>";
									}
								}
								break;
					case "html":
								$sResult = $oBanner->getValue('html_content');
								// parse result
								$sResult = str_ireplace("%link%", $this->pre_lang."/banners/go_to/".$iObjId, $sResult);
								break;
					default:
						// do nothing
				}
				// set banner
				$iOldViewsCount = $oBanner->getValue('views_count');
				//$oBanner->setValue('views_count', ++$iOldViewsCount);

				//$oBanner->commit();
			}
			
			$block_arr = Array();
			$block_arr['id'] = $iObjId;
			$sResult = def_module::parseTemplate($sResult, $block_arr, false, $iObjId);
			return $sResult;
		}

		public function go_to(){
			$iObjId = $_REQUEST['param0'];
			$oBanner = umiObjectsCollection::getInstance()->getObject($iObjId);
			if ($oBanner instanceof umiObject) {
				$sUrl = $oBanner->getValue('url');
				// write stats
				$iOldClicksCount = $oBanner->getValue('clicks_count');
				$oBanner->setValue('clicks_count', ++$iOldClicksCount);
				$oBanner->commit();
				// try redirect
				$this->redirect($sUrl);
			}
		}

		public function config() {
			if(class_exists("__banners")) {
				return __banners::config();
			}
		}

		public function getEditLink($object_id, $object_type) {
			$object = umiObjectsCollection::getInstance()->getObject($object_id);

			switch($object_type) {
				case "banner": {
					$link_add = $this->pre_lang . "/admin/banners/banner_add/";
					$link_edit = $this->pre_lang . "/admin/banners/banner_edit/{$object_id}/";

					return array($link_add, $link_edit);
					break;
				}

				default: {
					return false;
				}
			}
		}
	};
?>
