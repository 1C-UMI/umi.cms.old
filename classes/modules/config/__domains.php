<?php
	abstract class __domains_config {
		public function domains() {
			$this->load_forms();
			$params = Array();

			$langs_list = langsCollection::getInstance()->getAssocArray();

			$rows = "";
			$domains = domainsCollection::getInstance()->getList();
			foreach($domains as $domain) {
				$domain_id = $domain->getId();
				$domain_host = $domain->getHost();

				$is_default = ($domain->getIsDefault()) ? "1" : "";

				$disabled = ($is_default) ? " disabled=\"disabled\"" : "";

				$langs = putSelectBox_assoc($langs_list, $domain->getDefaultLangId(), false);


				if($is_default) {
					$del_link = "";
				} else {
					$del_link = <<<END
			<a href="%pre_lang%/admin/config/domain_del/{$domain_id}" commit_unrestorable="?">
				<img src="/images/cms/admin/%skin_path%/ico_del.gif" alt="Удалить" title="Удалить" border="0" />
			</a>
END;
				}

				$rows .= <<<END
	<row>
		<col>
			<input quant="no" style="width: 94%" {$disabled}>
				<name><![CDATA[domain_hosts[{$domain_id}]]]></name>
				<value><![CDATA[{$domain_host}]]></value>
			</input>
		</col>

		<col align="center">
			<select quant="no">
				<name><![CDATA[domain_langs[{$domain_id}]]]></name>
				{$langs}
			</select>
		</col>

		<col align="center">
			<a href="%pre_lang%/admin/config/domain_mirrows/{$domain_id}/"><img src="/images/cms/admin/%skin_path%/ico_subitems.%ico_ext%" /></a>
		</col>

		<col align="center">
			{$del_link}
		</col>
	</row>
END;
			}

			$params['rows'] = $rows;
			$params['new_lang'] = putSelectBox_assoc($langs_list, 0, false);
			return $this->parse_form("domains", $params);
		}

		public function domains_do() {
			$domain_hosts = $_REQUEST['domain_hosts'];
			$domain_langs = $_REQUEST['domain_langs'];
			$domain_default = $_REQUEST['domain_default'];

			$domain_hosts_new = $_REQUEST['domain_hosts_new'];
			$domain_langs_new = $_REQUEST['domain_langs_new'];

			foreach($domain_hosts as $domain_id => $domain_host) {
				$domain_lang_id = $domain_langs[$domain_id];

				$domain = domainsCollection::getInstance()->getDomain($domain_id);

				if($domain->getIsDefault()) {
					continue;
				}

				$domain->setHost($domain_host);
				$domain->setDefaultLangId($domain_lang_id);

				if($domain_id == $domain_default) {
					$domain->setIsDefault(true);
				} else {
					$domain->setIsDefault(false);
				}

				$domain->commit();
			}

			if($domain_hosts_new) {
				$domain_id = domainsCollection::getInstance()->addDomain($domain_hosts_new, $domain_langs_new, ($domain_default == 'NEW') ? true : false);

				$domain = domainsCollection::getInstance()->getDomain($domain_id);
			}

			$this->redirect($this->pre_lang . "/admin/config/domains/");
		}


		public function domain_mirrows() {
			$this->sheets_set_active("domains");
			$this->load_forms();
			$params = Array();


			$domain_id = $_REQUEST['param0'];
			$domain = domainsCollection::getInstance()->getDomain($domain_id);


			$this->navibar_push("BACK");
			$this->navibar_push("Настройка доменов", $this->pre_lang . "/admin/config/domains/");
			$this->navibar_push($domain->getHost(), $this->pre_lang . "/admin/config/domain_mirrows/" . $domain_id . "/");


			$mirrows = $domain->getMirrowsList();

			$rows = "";

			foreach($mirrows as $mirrow) {
				$mirrow_id = $mirrow->getId();
				$mirrow_host = $mirrow->getHost();

				$rows .= <<<END
			<row>
				<col>
					<input quant="no" style="width: 94%;">
						<name><![CDATA[mirrow_hosts[{$mirrow_id}]]]></name>
						<value><![CDATA[{$mirrow_host}]]></value>
					</input>
				</col>

				<col style="text-align: center;">
					<a href="%pre_lang%/admin/config/domain_mirrow_del/{$domain_id}/{$mirrow_id}" commit_unrestorable="?">
						<img src="/images/cms/admin/%skin_path%/ico_del.gif" alt="Удалить" title="Удалить" border="0" />
					</a>
				</col>
			</row>

END;
			}

			$params['rows'] = $rows;
			$params['domain_id'] = $domain_id;
			return $this->parse_form("domain_mirrows", $params);
		}


		public function domain_mirrows_do() {
			$domain_id = (int) $_REQUEST['param0'];
			$domain = domainsCollection::getInstance()->getDomain($domain_id);

			$mirrow_hosts = $_REQUEST['mirrow_hosts'];
			$mirrow_hosts_new = $_REQUEST['mirrow_hosts_new'];

			foreach($mirrow_hosts as $mirrow_id => $mirrow_host) {
				$mirrow = $domain->getMirrow($mirrow_id);
				$mirrow->setHost($mirrow_host);
				$mirrow->commit();
			}


			if($mirrow_hosts_new) {
				$domain->addMirrow($mirrow_hosts_new);
			}

			$domain->commit();

			$this->redirect($this->pre_lang . "/admin/config/domain_mirrows/" . $domain_id . "/");
		}


		public function domain_mirrow_del() {
			$domain_id = (int) $_REQUEST['param0'];
			$domain_mirrow_id = (int) $_REQUEST['param1'];

			$domain = domainsCollection::getInstance()->getDomain($domain_id);
			$domain->delMirrow($domain_mirrow_id);
			$domain->commit();

			$this->redirect($this->pre_lang . "/admin/config/domain_mirrows/{$domain_id}/");
		}


		public function domain_del() {
			$domain_id = (int) $_REQUEST['param0'];

			domainsCollection::getInstance()->delDomain($domain_id);

			$this->redirect($this->pre_lang . "/admin/config/domains/");
		}
	};
?>