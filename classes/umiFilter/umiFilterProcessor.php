<?php

	class umiFilterProcessor implements iUmiFilterProcessor {

		public static function renderFilter(umiFilter $oUmiFilter) {
			
			$oHierarchyType = $oUmiFilter->getHierarchyType();
			if (!$oHierarchyType) return "";

			$iHierarchyTypeId = $oHierarchyType->getId();
			$iObjectTypeId = $oUmiFilter->getBaseObjectType()->getId();
			$bIsObjectFilter = $oUmiFilter->getIsObjectFilter();

			// render object types
			$arrObjectTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($iHierarchyTypeId);

			$sObjectTypes = "";
			$arrSelFilters = $oUmiFilter->getObjectTypesFilter();
			if (is_array($arrObjectTypes) && count($arrObjectTypes)) {
				foreach ($arrObjectTypes as $iTypeId => $sTypeName) {
					if ($oUmiFilter->getBaseObjectType()->getId() == $iTypeId) continue;
					$sSelected = isset($arrSelFilters[$iTypeId])? "selected=\"1\"" : "";
					$sObjectTypes .= <<<END

								<umi:item name="{$iTypeId}" $sSelected><![CDATA[{$sTypeName}]]></umi:item>

END;

				}
			}

END;
			// render advanced and additional groups
			$sAdvansedFilterGroups = "";
			$sAdditionalFilterGroup = "";
			
			$arrFieldsGroups = $oUmiFilter->getBaseObjectType()->getFieldsGroupsList();
			$arrSelFieldFilters = $oUmiFilter->getFiltredProperties();

			foreach ($arrFieldsGroups as $oGroup) {
				$arrFields = $oGroup->getFields();
				$sAdvansedFilterGroup = "";
				foreach ($arrFields as $oField) {
					if (!$oField->getIsVisible()) continue;

					$sFieldDataType = $oField->getFieldType()->getDataType();
					//echo $oField->getTitle()." ".$sFieldDataType."\n";
					if (in_array($sFieldDataType, array('file', 'swf_file', 'img_file', 'symlink', 'relation'))) continue;

					$sNextProp = "";
					if ($sFieldDataType == "boolean") {
						$sYesSel = (isset($arrSelFieldFilters[$oField->getName()]) && $arrSelFieldFilters[$oField->getName()] == 1)? "selected=\"1\"": "";
						$sNoSel = (isset($arrSelFieldFilters[$oField->getName()]) && $arrSelFieldFilters[$oField->getName()] == 2)? "selected=\"1\"": "";
						$sAnySel = (!strlen($sYesSel) && !strlen($sNoSel))? "selected=\"1\"": "";
						$sNextProp = <<<END

							<umi:property name="fltr_{$oField->getName()}" type="select">
								<umi:title><![CDATA[{$oField->getTitle()}]]></umi:title>
								<umi:choices>
									<umi:item name="0" $sAnySel><![CDATA[не важно]]></umi:item>
									<umi:item name="1" $sYesSel><![CDATA[да]]></umi:item>
									<umi:item name="2" $sNoSel><![CDATA[нет]]></umi:item>
								</umi:choices>
							</umi:property>
END;
						$sAdditionalFilterGroup .= $sNextProp;
					} else {

						$bFldValue = isset($arrSelFieldFilters[$oField->getName()])? "<umi:value>1</umi:value>": "";

						$sNextProp = <<<END

							<umi:property name="fltr_{$oField->getName()}" type="{$sFieldDataType}">
								<umi:title><![CDATA[{$oField->getTitle()}]]></umi:title>
								{$bFldValue}
							</umi:property>

END;
						$sAdvansedFilterGroup .= $sNextProp;
					}
				}
				if (strlen($sAdvansedFilterGroup)) {
					$sAdvansedFilterGroups .= <<<END

					<umi:group name="fltr_{$oGroup->getName()}">
						<umi:title><![CDATA[{$oGroup->getTitle()}]]></umi:title>
						{$sAdvansedFilterGroup}
					</umi:group>
END;
				}
			}


			$sFilterByActive = "";

			if (!$oUmiFilter->getIsObjectFilter()) {
				$sYesSel = (isset($arrSelFieldFilters['active']) && $arrSelFieldFilters['active'] == 1)? "selected=\"1\"": "";
				$sNoSel = (isset($arrSelFieldFilters['active']) && $arrSelFieldFilters['active'] == 2)? "selected=\"1\"": "";
				$sAnySel = (!strlen($sYesSel) && !strlen($sNoSel))? "selected=\"1\"": "";
				$sFilterByActive = <<<END

					<umi:property name="fltr_active" type="select">
						<umi:title>Активность</umi:title>
						<umi:choices>
							<umi:item name="0" $sAnySel>не важно</umi:item>
							<umi:item name="1" $sYesSel>да</umi:item>
							<umi:item name="2" $sNoSel>нет</umi:item>
						</umi:choices>
					</umi:property>

END;
			}

			$sFilterDesc = self::getFilterDescription($oUmiFilter);
			$oSortedProp = $oUmiFilter->getSortedProperty();
			$sSortedPropName = $oSortedProp instanceof umiField ? "sortby=\"fltr_".$oSortedProp->getName()."\"" : "";

			$sIsDescOrder = $oUmiFilter->getIsDescSorting() ? "desc_order=\"1\"" : "";

			$sAnswer = <<<END

				<umi:filter xmlns:umi="http://www.umi-cms.ru/2007/umi-cms-markup" hierarchy_type_id="{$iHierarchyTypeId}" object_type_id="{$iObjectTypeId}" is_object_filter="{$bIsObjectFilter}" $sSortedPropName $sIsDescOrder>
					<umi:message>{$sFilterDesc}</umi:message>
					<umi:property name="fltr_sstring" type="string">
						<umi:title>Искать</umi:title>
						<umi:value><![CDATA[{$oUmiFilter->getFilterString()}]]></umi:value>
					</umi:property>

					<umi:property name="fltr_types" type="multipleSelect">
						<umi:title>Что ищем?</umi:title>
						<umi:choices>
							{$sObjectTypes}
						</umi:choices>
					</umi:property>
					
					{$sAdvansedFilterGroups}

					<umi:group name="grp_additional">
						<umi:title>Дополнительные параметры</umi:title>
						{$sFilterByActive}
						{$sAdditionalFilterGroup}
					</umi:group>
				</umi:filter>

END;
			return $sAnswer;
		}

		public static function applyFilter(umiFilter $oUmiFilter) {
			$oSelection = new umiSelection();

			$sFilterStr = $oUmiFilter->getFilterString();

			$arrFilters = $oUmiFilter->getFilterArray();

			// permissions
			$oSelection->setPermissionsFilter();
			$oSelection->addPermissions();
			
			$oSelection->setConditionModeOr();
			
			// apply object types filter
			$arrObjectTypes = $oUmiFilter->getObjectTypesFilter();
			if (!count($arrObjectTypes)) {
				$arrObjectTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($oUmiFilter->getHierarchyType()->getId());
			}
			$oSelection->setObjectTypeFilter();
			foreach ($arrObjectTypes as $iTypeId => $sTypeName) {
				$oSelection->addObjectType((int) $iTypeId);
			}
			if (!$oUmiFilter->getIsObjectFilter()) {
				// return elements
				$oSelection->forceHierarchyTable();
				// apply filter by active
				if (is_bool($bActive = $oUmiFilter->getFilterByActive())) {
					$oSelection->setActiveFilter();
					$oSelection->addActiveFilter($bActive);
				}
			}
			
			// apply name filter
			if ($oUmiFilter->getFilterByName()) {
				$oSelection->setNamesFilter();
				for ($iI = 0; $iI < count($arrFilters); $iI++) {
					if ($arrFilters[$iI]['type'] == "like") {
						$oSelection->addNameFilterLike($arrFilters[$iI]['value']);
					} elseif ($arrFilters[$iI]['type'] == "equal") {
						$oSelection->addNameFilterEquals($arrFilters[$iI]['value']);
					}
				}
			}
			// apply ancestor filter
			if (is_numeric($iAncestorId = $oUmiFilter->getFilterByAncestor())) {
				$oSelection->setHierarchyFilter();
				$oSelection->addHierarchyFilter($iAncestorId);
			}
			// apply property filters
			$arrFiltredProperties = $oUmiFilter->getFiltredProperties();
			if (count($arrFiltredProperties)) {
				$oSelection->setPropertyFilter();
				foreach ($arrFiltredProperties as $sPropName => $vValue) {
					$iPropId = $oUmiFilter->getBaseObjectType()->getFieldId($sPropName);
					$oProperty = umiFieldsCollection::getInstance()->getField($iPropId);
					if ($oProperty instanceof umiField) {
						$sFieldType = $oProperty->getFieldType()->getDataType();
						if ($sFieldType == "boolean") {
							// 0 - any
							if ((int) $vValue == 0) continue;
							// 1 - enabled
							// 2 - disabled
							$bValue = (int) $vValue;
							if ($bValue) {
								$oSelection->addPropertyFilterEqual($iPropId, $bValue);
							} else {
								$oSelection->addPropertyFilterIsNull($iPropId);
							}
						} else {
							for ($iI = 0; $iI < count($arrFilters); $iI++) {
								$sType = $arrFilters[$iI]['type'];
								$vVal = $arrFilters[$iI]['value'];
								switch ($sType) {
									case "equal": 
										$oSelection->addPropertyFilterEqual($iPropId, $vVal);
									break;

									case "notequal": 
										$oSelection->addPropertyFilterNotEqual($iPropId, $vVal);
									break;

									case "more":
										$oSelection->addPropertyFilterMore($iPropId, $vVal);
									break;

									case "less":
										$oSelection->addPropertyFilterLess($iPropId, $vVal);
									break;

									case "between":
										$oSelection->addPropertyFilterBetween($iPropId, $vVal['min'], $vVal['max']);
									break;

									case "like":
									default: 
										$oSelection->addPropertyFilterLike($iPropId, $vVal);
								}
							}
						}
					}
				}
			}

			// order
			$oSortedProp = $oUmiFilter->getSortedProperty();
			if ($oSortedProp instanceof umiObjectProperty) {
				$oSelection->setOrderByProperty($oSortedProp->getId(), !$oUmiFilter->getIsDescSorting());
			} else {
				$oSelection->setOrderByName(!$oUmiFilter->getIsDescSorting());
			}

			return umiSelectionsParser::runSelection($oSelection);
		}

		public static function getFilterDescription(umiFilter $oUmiFilter) {
			
			$arrObjectTypesFilter = $oUmiFilter->getObjectTypesFilter();
			$arrObjectTypes = umiObjectTypesCollection::getInstance()->getTypesByHierarchyTypeId($oUmiFilter->getHierarchyType()->getId());
			$arrSearchTypes = array();
			if (count($arrObjectTypesFilter)) {
				foreach ($arrObjectTypesFilter as $iTypeId => $sTmp) {
					if (isset($arrObjectTypes[$iTypeId])) {
						$arrSearchTypes[] = $arrObjectTypes[$iTypeId];
					}
				}
			} else {
				$arrSearchTypes[] = $oUmiFilter->getHierarchyType()->getTitle();
			}

			$sSearchTypes = strtolower(implode(", ", $arrSearchTypes));
			

			$arrAdditionalProps = array();
			$arrSearchProperties = array();
			if ($oUmiFilter->getFilterByName()) {
				$arrSearchProperties[] = "название";
			}

			foreach ($oUmiFilter->getFiltredProperties() as $sPropName => $vValue) {
				$iPropId = $oUmiFilter->getBaseObjectType()->getFieldId($sPropName);
				$oProperty = umiFieldsCollection::getInstance()->getField($iPropId);
				if ($oProperty instanceof umiField) {
					$sFieldType = $oProperty->getFieldType()->getDataType();
					if ($sFieldType == "boolean") {
						if ($vValue == 0) continue;
						$bValue = (int) $vValue;
						$arrAdditionalProps[] = $oProperty->getTitle()." = ".($bValue? "yes": "no");
					} else {
						$arrSearchProperties[] = $oProperty->getTitle();
					}
				}
			}
			$sSearchProperties = strtolower(implode(", ", $arrSearchProperties));
			$sAdditionalProps = count($arrAdditionalProps) ? ", а так же <strong><![CDATA[".strtolower(implode(", ", $arrAdditionalProps))."]]></strong>" : "";
			return <<<END
				Вы искали <strong><![CDATA[{$sSearchTypes}]]></strong>, где фраза <strong><![CDATA["{$oUmiFilter->getFilterString()}"]]></strong> встречается в полях <strong><![CDATA[{$sSearchProperties}]]></strong> {$sAdditionalProps}
END;
		}
	}

?>