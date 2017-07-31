<?php
	abstract class __subscribers_edit_subscribers {
		public function subscriber_edit() {
			// input
			$iSbsId = (int) $_REQUEST['param0'];
			// set tab
			$this->sheets_set_active("subscribers_list");
			// load forms
			$this->load_forms();
			// sbs type
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("dispatches", "subscriber")->getId();
			$iSbsTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oSbsType = umiObjectTypesCollection::getInstance()->getType($iSbsTypeId);
			// params
			$params = array();
			$params['method'] = "subscriber_edit_do/".$iSbsId;
			$params['control_bar'] = '<submit title="Отменить" onclick="javascript: return edtCancel();" />&#160;&#160;&#160;&#160;<submit title="Сохранить и выйти" onclick="javascript: return edtWithExit();" /> <submit title="Сохранить" onclick="javascript: return edtWithEdit();" />';
			$oSbsObj = umiObjectsCollection::getInstance()->getObject($iSbsId);
			if ($oSbsObj instanceof umiObject) {
				$params['sbs_mail'] = $oSbsObj->getName();
				$params['sbs_lname'] = $oSbsObj->getValue('lname');
				$params['sbs_fname'] = $oSbsObj->getValue('fname');
				$params['sbs_father_name'] = $oSbsObj->getValue('father_name');
				// groups
				if(cmsController::getInstance()->getModule('data')) {
					$iSbsGenderFldId = $oSbsType->getFieldId('gender');
					$oSbsGenderFld = umiFieldsCollection::getInstance()->getField($iSbsGenderFldId);
					$params['sbs_gender'] = cmsController::getInstance()->getModule('data')->renderEditableField($oSbsGenderFld, $oSbsObj);
					$params['data_field_groups'] =	cmsController::getInstance()->getModule('data')->renderEditableGroups($iSbsTypeId, $oSbsObj->getId(), true);
				}
			}
			return $this->parse_form("subscriber_edit", $params);
		}

		public function subscriber_edit_do() {
			//input
			$iSbsId = (int) $_REQUEST['param0'];
			$sSbsMail = $_REQUEST['sbs_mail'];
			$sSbsLName = $_REQUEST['sbs_lname'];
			$sSbsFName = $_REQUEST['sbs_fname'];
			$sSbsFatherName = $_REQUEST['sbs_father_name'];
			$sAfterSaveAct = $_REQUEST['after_save_act'];
			$iSbsTypeId =  umiObjectTypesCollection::getInstance()->getBaseType("dispatches", "subscriber");
			$oSbsType = umiObjectTypesCollection::getInstance()->getType($iSbsTypeId);
			// try get object
			$oSbsObj = umiObjectsCollection::getInstance()->getObject($iSbsId);
			if ($oSbsObj instanceof umiObject) {
				// set object props
				$oSbsObj->setName($sSbsMail);
				$oSbsObj->setValue('lname', $sSbsLName);
				$oSbsObj->setValue('fname', $sSbsFName);
				$oSbsObj->setValue('father_name', $sSbsFatherName);
				$iSbsGenderFldId = $oSbsType->getFieldId('gender');
				if (isset($_REQUEST['data_values'][$iSbsGenderFldId])) {
					$oSbsObj->setValue('gender', $_REQUEST['data_values'][$iSbsGenderFldId]);
				}
				// set groups
				if(cmsController::getInstance()->getModule('data')) {
					cmsController::getInstance()->getModule('data')->saveEditedGroups($oSbsObj->getId(), true);
				}
				// commit
				$oSbsObj->commit();
			}
			// redirect
			switch ($sAfterSaveAct) {
				case "exit": $this->redirect($this->pre_lang . "/admin/dispatches/subscribers_list/"); break;
				case "edit": $this->redirect($this->pre_lang . "/admin/dispatches/subscriber_edit/".$iSbsId."/"); break;
				default: //do nothing
			}
		}

	};
?>