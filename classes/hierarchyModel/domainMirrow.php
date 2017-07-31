<?php
	class domainMirrow extends umiEntinty implements iUmiEntinty, iDomainMirrow {
		private $host;

		public function setHost($host) {
			$this->host = $host;
			$this->setIsUpdated();
		}
                 
		public function getHost() {
			return $this->host;
		}

		protected function loadInfo() {
			$sql = "SELECT SQL_CACHE host FROM cms3_domain_mirrows WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			}

			if(list($host) = mysql_fetch_row($result)) {
				$this->host = $host;
				return true;
			} else {
				return false;
			}
		}

		protected function save() {
			$host = mysql_real_escape_string($this->host);

			$sql = "UPDATE cms3_domain_mirrows SET host = '{$host}' WHERE id = '{$this->id}'";
			$result = mysql_query($sql);

			if($err = mysql_error()) {
				trigger_error($err, E_USER_WARNING);
				return false;
			} else {
				return true;
			}
		}
	}
?>