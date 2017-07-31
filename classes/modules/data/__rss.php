<?php
	abstract class __rss_data {
		public $rss_per_page = 10;
		public function rss() {
			$element_id = $_REQUEST['param0'];
			if(!umiHierarchy::getInstance()->isExists($element_id)) {
				return "%data_feed_nofeed%";
			}

			if(!$this->checkIfFeedable($element_id)) {
				return "%data_feed_wrong%";
			}

			$xslPath = "xsl/rss.xsl";

			$this->generateFeed($element_id, $xslPath);
		}


		public function atom() {
			$element_id = $_REQUEST['param0'];
			if(!umiHierarchy::getInstance()->isExists($element_id)) {
				return "%data_feed_nofeed%";
			}

			if(!$this->checkIfFeedable($element_id)) {
				return "%data_feed_wrong%";
			}

			$xslPath = "xsl/atom.xsl";

			$this->generateFeed($element_id, $xslPath);
		}


		public function generateFeed($element_id, $xslPath) {
			$sel = new umiSelection();

			$sel->setLimitFilter();
			$sel->addLimit($this->rss_per_page);


			$sel->setHierarchyFilter();
			$sel->addHierarchyFilter($element_id);

			$result = Array($element_id);
			$result = array_merge($result, umiSelectionsParser::runSelection($sel));

			$t = new umiXmlExporter();
			$t->setElements($result);
			$t->run();
			$src = $t->getResultFile();

			$xmldata = simplexml_load_string($src);

			$xslt = new xsltProcessor;
			$xslt->importStyleSheet(DomDocument::load($xslPath));
			$resultXml = $xslt->transformToXML($xmldata);

			header("Content-type: text/xml");
			$this->flush($resultXml);
		}


		public function getRssMeta($element_id, $title_prefix = "") {
			if(!$element_id) {
				$element_id = cmsController::getInstance()->getCurrentElementId();
			}

			if(!umiHierarchy::getInstance()->isExists($element_id)) {
				return "";
			}

			if(!$this->checkIfFeedable($element_id)) {
				return "";
			}

			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element_title = $title_prefix . $element->getName();

			return "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"/data/rss/{$element_id}/\" title=\"{$element_title}\" />";
		}


		public function getRssMetaByPath($path, $title_prefix) {
			if($element_id = umiHierarchy::getInstance()->getIdByPath($path)) {
				return $this->getRssMeta($element_id, $title_prefix);
			} else {
				return "";
			}
		}


		public function getAtomMeta($element_id, $title_prefix = "") {
			if(!$element_id) {
				$element_id = cmsController::getInstance()->getCurrentElementId();
			}

			if(!umiHierarchy::getInstance()->isExists($element_id)) {
				return "";
			}

			if(!$this->checkIfFeedable($element_id)) {
				return "";
			}
			
			$element = umiHierarchy::getInstance()->getElement($element_id);
			$element_title = $title_prefix . $element->getName();

			return "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"/data/atom/{$element_id}/\" title=\"{$element_title}\" />";
		}

		public function getAtomMetaByPath($path, $title_prefix = "") {
			if($element_id = umiHierarchy::getInstance()->getIdByPath($path)) {
				return $this->getAtomMeta($element_id, $title_prefix);
			} else {
				return "";
			}
		}


		public function checkIfFeedable($element_id) {
			$element = umiHierarchy::getInstance()->getElement($element_id);

			if(!$element) return false;

			$hierarchy_type = umiHierarchyTypesCollection::getInstance()->getType($element->getTypeId());

			$module = $hierarchy_type->getName();
			$method = $hierarchy_type->getExt();

			foreach($this->alowed_source as $allowed) {
				if($module == $allowed[0] && $method == $allowed[1]) {
					return true;
				}
			}
			return false;
		}
	};
?>