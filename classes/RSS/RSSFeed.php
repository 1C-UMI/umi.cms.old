<?php
	class RSSFeed implements iRSSFeed {
		private $url,
			$xml,
			$items;

		public function __construct($url) {
			$this->url = $url;
		}

		public function loadContent() {
			$cont = file_get_contents($this->url);
			if(!$cont) {
				trigger_error("Can't load \"{$url}\" RSS.", E_USER_WARNING);
				return false;
			}

			$this->xml = simplexml_load_string($cont);
		}

		public function loadRSS() {
			foreach($this->xml->channel->item as $xml_item) {
				$item = new RSSItem();
				$item->setTitle($xml_item->title);
				$item->setContent($xml_item->description);
				$item->setDate($xml_item->pubDate);
				$item->setUrl($xml_item->link);

				$this->items[] = $item;
			}
		}

		public function loadAtom() {
			foreach($this->xml as $tag => $xml_item) {
				if($tag != "entry") continue;

				$item = new RSSItem();
				$item->setTitle($xml_item->title);
				$item->setContent($xml_item->content);
				$item->setDate($xml_item->published);
				$item->setUrl($xml_item->link['href']);

				$this->items[] = $item;
			}

		}

		public function returnItems() {
			return $this->items;
		}
	}
?>