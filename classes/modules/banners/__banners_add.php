<?php
	abstract class __banners_add_banners {
		public function banner_add() {
			// set tab
			$this->sheets_set_active("banners_list");
			// load forms
			$this->load_forms();
			// params
			$params = array();
			// get banner types
			$oHierarchyType = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "banner");
			$arrBannerTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($oHierarchyType->getId());
			$params['banner_types'] = putSelectBox_assoc($arrBannerTypes, false, false);
			// controlbar
			$params['control_bar'] = '<button title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Добавить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Добавить" onclick="javascript: return edtWithEdit();" />';
			$params['banner_active'] = 1;
			
			$params['banner_show_start'] = date("d.m.Y H:i");
			//
			if(cmsController::getInstance()->getModule('data')) {
				$type_id = umiObjectTypesCollection::getInstance()->getBaseType("banners", "banner");
				$params['data_field_groups'] = cmsController::getInstance()->getModule('data')->renderEditableGroups($type_id);
			}
			$params['method'] = 'banner_add_do';
			return $this->parse_form("banner_edit", $params);
		}

		public function banner_add_do() {
			//
			// input:
			$sBanName = $_REQUEST['banner_name'];
			$iBanTypeId = (int) $_REQUEST['banner_type'];
			$sBanDesc = $_REQUEST['banner_desc'];
			$sRedirUrl = $_REQUEST['banner_url'];
			$bNeedNewWin = (bool) $_REQUEST['is_new_window'];
			$iBanViews = (int) $_REQUEST['banner_views'];
			$iBanClicks = (int) $_REQUEST['banner_clicks'];
			$iBanMaxViews = (int) $_REQUEST['banner_maxviews'];
			$sBanTags = $_REQUEST['banner_tags'];
			$arrBanTags = preg_split("/\s*,\s*/is", $sBanTags);
			$iBanActive = (bool) $_REQUEST['banner_active'];
			$sBanShowStart = $_REQUEST['banner_show_start'];
			$sBanShowTill = $_REQUEST['banner_show_till'];
			$sUserTags = (string) $_REQUEST['banner_user_tags'];
			$arrUserTags = preg_split("/\s*,\s*/is", $sUserTags);

			// add new banner
			$iObjectId = umiObjectsCollection::getInstance()->addObject($sBanName, $iBanTypeId);
			$oObject = umiObjectsCollection::getInstance()->getObject($iObjectId);
			// save original groups
			if(cmsController::getInstance()->getModule('data')) {
				cmsController::getInstance()->getModule('data')->saveEditedGroups($iObjectId, true);
			}

			$oObject->setValue('descr', $sBanDesc);
			$oObject->setValue('url', $sRedirUrl);
			$oObject->setValue('open_in_new_window', $bNeedNewWin);
			$oObject->setValue('views_count', $iBanViews);
			$oObject->setValue('clicks_count', $iBanClicks);
			$oObject->setValue('max_views', $iBanMaxViews);
			$oObject->setValue('tags', $arrBanTags);
			$oObject->setValue('is_active', $iBanActive);
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

			$oObject->commit();

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