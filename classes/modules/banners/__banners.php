<?php
	abstract class __banners_banners {
		public function banners_list() {
			//input:
			$this->load_forms();
			$params = array();

			$curr_page = (int) $_REQUEST['p'];
			$per_page = $this->per_page;

			// gen banners list
			$params['rows'] = "";
			$oBannersSelection = new umiSelection;
			$oBannersSelection->setObjectTypeFilter();
			$iHierarchyTypeId = umiHierarchyTypesCollection::getInstance()->getTypeByName("banners", "banner")->getId();
			$iBannerTypeId =  umiObjectTypesCollection::getInstance()->getTypeByHierarchyTypeId($iHierarchyTypeId);
			$oBannerType = umiObjectTypesCollection::getInstance()->getType($iBannerTypeId);

			// sort by show_start_date
			$oBannersSelection->setOrderFilter();
			$iStartDateFldId = $oBannerType->getFieldId('show_start_date');
//			$oBannersSelection->setOrderByProperty($iStartDateFldId);

			$arrBannerTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($iHierarchyTypeId);
			
			foreach($arrBannerTypes as $type_id => $nop) {
				$oBannersSelection->addObjectType($type_id);
			}

			$oBannersSelection->setOrderByName();

			$oBannersSelection->setLimitFilter();
			$oBannersSelection->addLimit($per_page, $curr_page);


			$arrSelResults = umiSelectionsParser::runSelection($oBannersSelection);
			$total = umiSelectionsParser::runSelectionCounts($oBannersSelection);
			
			$params['pages'] = $this->generateNumPage($total, $per_page, $curr_page);

//			$params['filter'] = $this->generateFilters($iHierarchyTypeId);
			$params['hierarchy_type_id'] = $iHierarchyTypeId;

			for ($iI=0; $iI<count($arrSelResults); $iI++) {
				$params['rows'] .= self::renderBanner($arrSelResults[$iI]);
			}

			return $this->parse_form("banners_list", $params);
		}
		protected function renderBanner($iObjId) {
			$oBanner =  umiObjectsCollection::getInstance()->getObject($iObjId);
			$params = array();
			if ($oBanner) {
				$params['banner_name'] = $oBanner->getName();
				$params['banner_id'] = $oBanner->getId();
				$params['banner_clicks'] = (int) $oBanner->getValue('clicks_count');

				$descr = $oBanner->getValue('descr');
				$params['banner_desc'] = ($descr) ? $descr : "Нет комментария";
				
				if($oBanner->getValue("is_active")) {
					$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/banners/banner_blocking/{$iObjId}/0/"><img src="/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%" alt="Заблокировать" title="Заблокировать" border="0" /></a>
END;
				} else {
					$params['blocking'] = <<<END
					<a href="%pre_lang%/admin/banners/banner_blocking/{$iObjId}/1/"><img src="/images/cms/admin/%skin_path%/ico_block.%ico_ext%" alt="Разблокировать" title="Разблокировать" border="0" /></a>
END;
				}
				$iBanTypeId = $oBanner->getTypeId();
				$oBanType = umiObjectTypesCollection::getInstance()->getType($iBanTypeId);
				if ($oBanType instanceof umiObjectType) {
					$params['banner_type'] = $oBanType->getName();
				}
				$bNeedDeactive = false;
				$iBanViews = (int) $oBanner->getValue('views_count');
				$iMaxViews = (int) $oBanner->getValue('max_views');
				$params['banner_views'] = $iBanViews;
				$bIsActive = (bool) $oBanner->getValue('is_active');

				$sMaxViews = "Не ограничено";
				if ($iMaxViews > 0) {
					$sMaxViews = $iMaxViews;
					if ($iBanViews > $iMaxViews) {
						$bNeedDeactive = true;
						$sMaxViews = "<span style='color:red;'><![CDATA[$iMaxViews]]></span>";
					}
				}
				$params['banner_max_views'] = $sMaxViews;
				//
				$params['banner_time_targeting_status'] = ($oBanner->getValue('time_targeting_is_active')? "Включен": "Выключен");

				$oShowStartDate = $oBanner->GetValue('show_start_date');
				if ($oShowStartDate instanceof umiDate && $oShowStartDate->timestamp) {
					$params['banner_show_start'] = $oShowStartDate->getFormattedDate("d.m.Y H:i");
				}

				$sShowTillDate = "Бессрочно";
				$oShowTillDate = $oBanner->GetValue('show_till_date');
				if ($oShowTillDate instanceof umiDate && $oShowTillDate->timestamp) {
					$sTillDateStr = $oShowTillDate->getFormattedDate("d.m.Y H:i");
					$sShowTillDate = ( ($oShowTillDate->timestamp < $oShowTillDate->getCurrentTimeStamp()) ? "<span style='color: red;'>" : "<span>" ) . $sTillDateStr . "</span>";
					$bNeedDeactive = $oShowTillDate->timestamp < $oShowTillDate->getCurrentTimeStamp();
				}

				$params['banner_show_till'] = $sShowTillDate;

				if ($bNeedDeactive && $bIsActive) {
					$oBanner->setValue('is_active', false);
					$bIsActive = false;

					$oBanner->commit();
				}
				$params['banner_active'] = ($oBanner->getValue('is_active') ? "Да": "<span style='color:red;'>Нет</span>");
				
				$arrViewPages = $oBanner->getValue('view_pages');
				$params['banner_pages'] = (is_array($arrViewPages) && count($arrViewPages)? "указанные страницы": "Все страницы");
				// places
				$sPlacesNames = "";
				$arrPlaces = $oBanner->getValue('place');
				if (is_array($arrPlaces) && count($arrPlaces)) {
					for ($iI=0; $iI<count($arrPlaces); $iI++) {
						$oNextPlace = umiObjectsCollection::getInstance()->getObject($arrPlaces[$iI]);
						$sPlacesNames .= "<a href=\"{$this->pre_lang}/admin/banners/place_edit/{$arrPlaces[$iI]}/\"><![CDATA[".$oNextPlace->getName()."]]></a>";
						if ($iI < count($arrPlaces)-1) $sPlacesNames .= ", ";
					}
				} else {
					$sPlacesNames = "<span style='color:red;'>Не определено</span>";
				}
				$params['banner_places'] = $sPlacesNames;
				$params['object_type_id'] = $iBanTypeId;
			}
			return $this->parse_form("banners_list_row", $params);
			//
		}
		
		public function banner_del() {
			$iBannerId = $_REQUEST['param0'];
			umiObjectsCollection::getInstance()->delObject($iBannerId);
			$this->redirect($this->pre_lang . "/admin/banners/banners_list/");
		}
	};
?>