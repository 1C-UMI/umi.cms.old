<?php
	abstract class __banners_edit_banners {

		public function banner_blocking() {
			$iBannerId = (int) $_REQUEST['param0'];
			$isActive = (int) $_REQUEST['param1'];

			$oBanner = umiObjectsCollection::getInstance()->getObject($iBannerId);
			if ($oBanner instanceof umiObject) {
				$oBanner->setValue("is_active", $isActive);
				$oBanner->commit();
			}
			$this->redirect($this->pre_lang . "/admin/banners/");
		}

		public function banner_edit() {
			// set tab
			$this->sheets_set_active("banners_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// current banner
			$iBannerId = (int) $_REQUEST['param0'];
			$oBanner = umiObjectsCollection::getInstance()->getObject($iBannerId);
			if ($oBanner instanceof umiObject) {
				$iBanTypeId = $oBanner->getTypeId();
				// get banner types and current type
				$oBannerType = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "banner");
				$arrBannerTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($oBannerType->getId());
				$params['banner_types'] = putSelectBox_assoc($arrBannerTypes, $iBanTypeId, false);
				$params['banner_name'] = $oBanner->getName();
				$params['banner_desc'] = $oBanner->getValue('descr');
				$params['banner_active'] = $oBanner->getValue('is_active');
				$params['banner_url'] = $oBanner->getValue('url');
				$params['is_new_window'] = $oBanner->getValue('open_in_new_window');
				$params['banner_tags'] = implode(", ", $oBanner->getValue('tags'));
				$params['banner_user_tags'] = implode(", ", $oBanner->getValue('user_tags'));

				$params['banner_views'] = (int) $oBanner->getValue('views_count');
				$params['banner_clicks'] = (int) $oBanner->getValue('clicks_count');
				$params['banner_maxviews'] = (int) $oBanner->getValue('max_views');
				// show till date
				$sShowTillDate = "";
				$oShowTillDate = $oBanner->getValue('show_till_date');
				if ($oShowTillDate instanceof umiDate && $oShowTillDate->timestamp) {
						$sShowTillDate = $oShowTillDate->getFormattedDate("d.m.Y H:i");
				}
				$params['banner_show_till'] = $sShowTillDate;
				// show start date
				$sShowStartDate = "";
				$oShowStartDate = $oBanner->getValue('show_start_date');
				if ($oShowStartDate instanceof umiDate && $oShowStartDate->timestamp) {
						$sShowStartDate = $oShowStartDate->getFormattedDate("d.m.Y H:i");
				}
				$params['banner_show_start'] = $sShowStartDate;
				//
				$params['url'] = $oBanner->getValue('url');
				$params['is_new_window'] = $oBanner->getValue('open_in_new_window');
				// control bar
				$params['save_n_save'] = $params['control_bar'] = '<button title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Сохранить" onclick="javascript: return edtWithEdit();" />';
				// groups
				if(cmsController::getInstance()->getModule('data')) {
					$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($oBanner->getTypeId(), $iBannerId, true);
				}

				if($backup_inst = cmsController::getInstance()->getModule("backup")) {
					$params['backup_panel'] = $backup_inst->backup_panel("banners", "banner_edit_do", $iBannerId);
				}

				//method
			}
			$params['method'] = 'banner_edit_do/'.$iBannerId;
			return $this->parse_form("banner_edit", $params);

		}

		public function banner_edit_do() {
			// input:

			$iObjectId = $_REQUEST['param0'];
			$sBanName = $_REQUEST['banner_name'];
			$sBanTags = $_REQUEST['banner_tags'];
			$arrBanTags = preg_split("/\s*,\s*/is", $sBanTags);
			for ($iJ=0; $iJ<count($arrBanTags); $iJ++) {
				$arrBanTags[$iJ] = trim($arrBanTags[$iJ]);
			}
			$iBanTypeId = (int) $_REQUEST['banner_type'];
			$sBanDesc = $_REQUEST['banner_desc'];
			$iBanActive = (bool) $_REQUEST['banner_active'];
			$sBanUrl = $_REQUEST['banner_url'];
			$iBanViews = (int) $_REQUEST['banner_views'];
			$iBanClicks = (int) $_REQUEST['banner_clicks'];
			$iBanMaxViews = (int) $_REQUEST['banner_maxviews'];
			$sBanShowStart = $_REQUEST['banner_show_start'];
			$sBanShowTill = $_REQUEST['banner_show_till'];
			$bNeedNewWindow = (bool) $_REQUEST['is_new_window'];
			$sUserTags = (string) $_REQUEST['banner_user_tags'];
			$arrUserTags = preg_split("/\s*,\s*/is", $sUserTags);


			$oObject = umiObjectsCollection::getInstance()->getObject($iObjectId);
			// set props
			if ($oObject instanceof umiObject) {
				$oObject->setName($sBanName);
				$oObject->setValue('descr', $sBanDesc);
				$oObject->setValue('is_active', $iBanActive);
				$oObject->setValue('url', $sBanUrl);
				
				$oObject->setValue('open_in_new_window', $bNeedNewWindow);
				$oObject->setValue('views_count', $iBanViews);
				$oObject->setValue('clicks_count', $iBanClicks);
				$oObject->setValue('max_views', $iBanMaxViews);
				$oObject->setValue('user_tags', $arrUserTags);

				// set show start date
				$oShowStartDate = new umiDate();
				$oShowStartDate->setDateByTimeStamp(time());
				if (strlen($sBanShowStart)) {
					$oShowStartDate->setDateByString($sBanShowStart);
				}
				$oObject->setValue('show_start_date', $oShowStartDate);

				// set show till date
				$oShowTillDate = NULL;
				if (strlen($sBanShowTill)) {
					$oShowTillDate = new umiDate();
					$oShowTillDate->setDateByString($sBanShowTill);
				}
				$oObject->setValue('show_till_date', $oShowTillDate);
				
				$oObject->setValue('tags', $arrBanTags);
				// set view pages
				

				// set groups
				if(cmsController::getInstance()->getModule('data')) {
					cmsController::getInstance()->getModule('data')->saveEditedGroups($iObjectId, true);
				}

				// set new type
				if ($iBanTypeId) {
					$oObject->setTypeId($iBanTypeId);
				}
			}
			
			$sBannerType = "";
			if ($oObject->getValue('swf') !== false) $sBannerType="swf";
			if ($oObject->getValue('image') !== false) $sBannerType="image";
			if ($oObject->getValue('html_content') !== false) $sBannerType="html";
			// set img sizes
			if ($sBannerType=="swf" || $sBannerType="image") {
				$oImgFile = $oObject->getValue($sBannerType);
				if ($oImgFile instanceof umiImageFile) {
					$iWidth =  (int) $oObject->getValue('width');
					$iHeight = (int) $oObject->getValue('height');
					if ($iWidth<=0) $iWidth = $oImgFile->getWidth();
					if ($iHeight<=0) $iHeight = $oImgFile->getHeight();
					$oObject->setValue('width', $iWidth);
					$oObject->setValue('height', $iHeight);
				}
			}

			$oObject->commit();

			if($backup_inst = cmsController::getInstance()->getModule("backup")) {
				$backup_inst->backup_save("banners", "banner_edit_do", $iObjectId);
			}


			// redirect
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/banners/banners_list/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/banners/banner_edit/".$iObjectId."/"); break;
				default: //do nothing
			}
		}

	};
?>