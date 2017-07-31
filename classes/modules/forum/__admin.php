<?php

abstract class __forum {


	public function config() {
		$params = Array();
		$regedit = regedit::getInstance();

		$this->sheets_reset();
		$this->load_forms();

		$arr1 = Array();
		$arr2 = Array();

		$sql = "SELECT id, group_name FROM cms_groups";
		$result = mysql_query($sql);
		while(list($gid, $gname) = mysql_fetch_row($result)) {
			if($gid == 1)
				continue;

			$arr1[] = $gid;
			$arr2[] = $gname;
		}

		$params['def_group'] = $regedit->getVal("//modules/forum/def_group");
		$params['need_moder'] = $regedit->getVal("//modules/forum/need_moder");
		$params['antimat'] = $regedit->getVal("//modules/forum/antimat");
		$params['antidouble'] = $regedit->getVal("//modules/forum/antidouble");
		$params['autounion'] = $regedit->getVal("//modules/forum/autounion");
		$params['allow_guest'] = $regedit->getVal("//modules/forum/allow_guest");
		$params['per_page'] = $regedit->getVal("//modules/forum/per_page");

		$params['groups_list'] = putSelectBox($arr2, $arr1, $params['def_group']);

		return $this->parse_form("config", $params);
	}

	public function config_do() {
		$res = "updating config...";
		$regedit = regedit::getInstance();

		$def_group = (int) $_REQUEST['def_group'];
		$need_moder = (int) $_REQUEST['need_moder'];
		$antimat = (int) $_REQUEST['antimat'];
		$antidouble = (int) $_REQUEST['antidouble'];
		$autounion = (int) $_REQUEST['autounion'];
		$allow_guest = (int) $_REQUEST['allow_guest'];
		$per_page = (int) $_REQUEST['per_page'];

		$regedit->setVar("//modules/forum/def_group", $def_group);
		$regedit->setVar("//modules/forum/need_moder", $need_moder);
		$regedit->setVar("//modules/forum/antimat", $antimat);
		$regedit->setVar("//modules/forum/antidouble", $antidouble);
		$regedit->setVar("//modules/forum/autounion", $autounion);
		$regedit->setVar("//modules/forum/allow_guest", $allow_guest);
		$regedit->setVar("//modules/forum/per_page", $per_page);


		$this->redirect($this->pre_lang . "/admin/forum/config/");
		return $res;
	}
/*
	public function confs_list() {
		$res = "";
		$params = Array();

		$this->load_forms();

		$rows = "";

		$sql = <<<SELECT

SELECT id, name FROM cms_forum_confs

SELECT;

		$result = mysql_query($sql);
		while(list($cid, $cname) = mysql_fetch_row($result)) {

			$rows .= <<<ROWS

<row>
	<col><a href="%pre_lang%/admin/forum/topics_list/$cid/" class="glink">$cname</a></col>
	<col style="text-align: center"><a href="%pre_lang%/admin/forum/conf_edit/$cid/" class="glink">Изменить</a></col>
	<col style="text-align: center"><a href="%pre_lang%/admin/forum/conf_del/$cid/" class="glink" commit="Вы уверены?">Удалить</a></col>
</row>

ROWS;
		}

		$params['rows'] = $rows;

		$params['unpublished_messages'] = $this->returnUnpublishedMessages();
		$params['last_messages'] = $this->returnLastMessages();

		$res = $this->parse_form("confs", $params);
		return $res;
	}

	public function conf_add() {
		$res = "";
		$params = Array();

		$this->load_forms();

		$params['method'] = "conf_add_do";
		$params['save_n_save'] = '<submit title="Добавить конференцию" onclick="javascript: return save_with_exit();" />';

		$res = $this->parse_form("conf_add", $params);
		return $res;
	}

	public function conf_add_do() {
		$res = "";

		$name = utf8_1251($_REQUEST['name']);
		$h1 = utf8_1251($_REQUEST['h1']);
		$alt_name = utf8_1251($_REQUEST['alt_name']);
		$descr = utf8_1251($_REQUEST['descr']);

		$sql = <<<INSERT

INSERT INTO cms_forum_confs
	(name, title, alt_name, descr)
		VALUES('$name', '$h1', '$alt_name', '$descr')

INSERT;
		mysql_query($sql);


		$sql = <<<MAX

SELECT MAX(id) FROM cms_forum_confs

MAX;

		list($new_id) = mysql_fetch_row(mysql_query($sql));

		$this->redirect($this->pre_lang . "/admin/forum/");

		return $res;
	}

	public function conf_edit() {
		$res = "";
		$cid = (int) $_REQUEST['param0'];
		$params = Array();

		$this->load_forms();

		$sql = <<<SELECT

SELECT * FROM cms_forum_confs
				WHERE id = '$cid'

SELECT;
		$result = mysql_query($sql);
		if($row = mysql_fetch_assoc($result)) {
			$params = (Array) $params + (Array) $row;
		}

		$params['method'] = "conf_edit_do";

		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';

		$res = $this->parse_form("conf_add", $params);
		return $res;
	}

	public function conf_edit_do() {
		$res = "";

		$cid = (int) $_REQUEST['param0'];

		$name = utf8_1251($_REQUEST['name']);
		$h1 = utf8_1251($_REQUEST['h1']);
		$alt_name = utf8_1251($_REQUEST['alt_name']);
		$descr = utf8_1251($_REQUEST['descr']);

		$sql = <<<INSERT

UPDATE cms_forum_confs
	SET
		name = '$name',
		title = '$h1',
		alt_name = '$alt_name',
		descr = '$descr'
			WHERE id='$cid'

INSERT;
		mysql_query($sql);

		$exit_after_save = $_REQUEST['exit_after_save'];
		if($exit_after_save) {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/forum/");
		} else {
			$this->redirect($_REQUEST['pre_lang'] . "/admin/forum/conf_edit/" . $cid . "/");
		}

		return $res;
	}

	public function conf_del() {
		$cid = (int) $_REQUEST['param0'];

		$sql = <<<DELETE

DELETE FROM cms_forum_confs
				WHERE id='$cid'

DELETE;

		mysql_query($sql);

		$this->redirect($this->pre_lang . "/admin/forum/");
	}
                                   
	public function topics_list() {
		$res = "topics list...";
		$cid = (int) $_REQUEST['param0'];
		$this->load_forms();
		$params = Array();

		$sql = <<<CONF

SELECT name FROM cms_forum_confs
				WHERE id='$cid'

CONF;
		list($cname) = mysql_fetch_row(mysql_query($sql));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");


		$sql = <<<SELECT

SELECT * FROM cms_forum_topics
				WHERE conf_id = '$cid'

SELECT;

		$result = mysql_query($sql);

		$rows = "";
		while($row = mysql_fetch_assoc($result)) {
			$tname = $row['name'];
			$tid = $row['id'];

			$tauthor_id = $row['author_id'];
			$tauthor_name = $row['author_name'];

			$tposttime = $row['posttime'];
			$tposttime = date("<b>d-m-Y</b> | H:i", $tposttime);

			$at = "";

			if($tauthor_id) {
				$tauthor_name = cmsController::getInstance()->getModule('users')->get_user_info($tauthor_id, "%fname% %lname% (%login%)");
				$at = "<a href='%pre_lang%/admin/users/user_edit/$tauthor_id/' class='glink'>" . $tauthor_name . "</a>";
			}
			else
				$at = $tauthor_name;

			if($row['is_active'])
				$todo = "Скрыть";
			else
				$todo = "<b>Открыть</b>";

			$tname = str_replace("&", "&amp;", $tname);	//TODO: finaly, fix!
			$tname = str_replace(">", "", $tname);
			$tname = str_replace("<", "", $tname);
			
			$rows .= <<<ROWS

<row>
	<col>$tposttime</col>
	<col><a href="%pre_lang%/admin/forum/mess_list/$cid/$tid/" class="glink">$tname</a></col>
	<col>$at</col>
	<col style="text-align: center"><a href="%pre_lang%/admin/forum/topic_publish/$cid/$tid/" class="glink">$todo</a></col>
	<col style="text-align: center"><a href="%pre_lang%/admin/forum/topic_edit/$cid/$tid/" class="glink">Изменить</a></col>
	<col style="text-align: center"><a href="%pre_lang%/admin/forum/topic_del/$cid/$tid/" commit="Вы уверены?" class="glink">Удалить</a></col>
</row>

ROWS;
		}


		$params['rows'] = $rows;
		$params['cid'] = $cid;


		$params['unpublished_messages'] = $this->returnUnpublishedMessages();
		$params['last_messages'] = $this->returnLastMessages();


		$res = $this->parse_form("topics", $params);
		return $res;
	}

	public function topic_del() {
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];

		$sql = "DELETE FROM cms_forum_topics WHERE id = '$tid'";
		mysql_query($sql);

		$sql = "DELETE FROM cms_forum_messages WHERE topic_id = '$tid'";
		mysql_query($sql);

		$sql = "DELETE FROM cms_forum_views WHERE topic_id = '$tid'";
		mysql_query($sql);

		$this->redirect($this->pre_lang . "/admin/forum/topics_list/" . $cid . "/");
	}

	public function topic_add() {
		$res = "";
		$cid = (int) $_REQUEST['param0'];

		$sql = <<<CONF

SELECT name FROM cms_forum_confs
				WHERE id='$cid'

CONF;
		list($cname) = mysql_fetch_row(mysql_query($sql));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");
		cmsController::getInstance()->nav_arr[] = Array("Добавление темы", "", "");


		$params = Array();
		$params['method'] = "topic_add_do";
		$params['cid'] = $cid;
		$params['save_n_save'] = '<submit title="Добавить тему" onclick="javascript: return save_with_exit();" />';
		$params['posttime'] = date("Y-m-d H:i");

		$params['is_active'] = 1;


		$this->load_forms();


		$res = $this->parse_form("topic_add", $params);
		return $res;
	}

	public function topic_add_do() {
		$res = "";

		$cid = (int) $_REQUEST['param0'];
		$name = utf8_1251($_REQUEST['name']);
		$alt_name = utf8_1251($_REQUEST['alt_name']);
		$posttime = utf8_1251($_REQUEST['posttime']);
		$posttime = toTimeStamp($posttime);

		$author_id = $this->CMS_ENV['user_id'];
		$author_ip = $_SERVER['REMOTE_ADDR'];

		$is_active = (int) $_REQUEST['is_active'];
		$is_closed = (int) $_REQUEST['is_closed'];


$sql = <<<INSERT

INSERT INTO cms_forum_topics
	(conf_id, name,  alt_name, posttime,
	author_id, author_ip, is_active, is_closed)
		VALUES('$cid', '$name', '$alt_name', '$posttime',
		'$author_id', '$author_ip', '$is_active', '$is_closed')

INSERT;


		mysql_query($sql);


		$sql = "SELECT MAX(id) FROM cms_forum_topics";
		list($new_id) = mysql_fetch_row(mysql_query($sql));

		$sql = <<<INSERT_VIEWS

INSERT INTO cms_forum_views (topic_id, cnt) VALUES ('$new_id', 0)


INSERT_VIEWS;
		mysql_query($sql);


		$this->redirect($this->pre_lang . "/admin/forum/topics_list/" . $cid . "/" . regedit::getInstance()->getVal("//modules/forum/def_group") . "/");

		return $res;
	}

	public function topic_edit() {
		$res = "";
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];
		$params = Array();
		$params['method'] = "topic_edit_do";
		$params['cid'] = $cid;

		$sql = <<<CONF

SELECT name FROM cms_forum_confs
				WHERE id='$cid'

CONF;
		list($cname) = mysql_fetch_row(mysql_query($sql));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");
		cmsController::getInstance()->nav_arr[] = Array("Редактирование темы", "", "");


		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		$sql = <<<SELECT

SELECT * FROM cms_forum_topics
				WHERE conf_id=$cid AND id='$tid'

SELECT;
		$result = mysql_query($sql);
		if($row = mysql_fetch_assoc($result)) {
			$row['posttime'] = date("Y-m-d H:i", $row['posttime']);
			$params = (Array) $params + (Array) $row;
		}

		$this->load_forms();


		$res = $this->parse_form("topic_add", $params);
		return $res;

	}

	public function topic_edit_do() {
		$res = "";

		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];

		$name = utf8_1251($_REQUEST['name']);
		$alt_name = utf8_1251($_REQUEST['alt_name']);
		$posttime = utf8_1251($_REQUEST['posttime']);
		$posttime = toTimeStamp($posttime);

		$is_active = (int) $_REQUEST['is_active'];
		$is_closed = (int) $_REQUEST['is_closed'];


		$sql = <<<UPDATE

UPDATE cms_forum_topics
	SET
		alt_name = '$alt_name',
		name = '$name',
		posttime = '$posttime',
		is_active = '$is_active',
		is_closed = '$is_closed'

			WHERE id='$tid'

UPDATE;

		mysql_query($sql);


		$exit_after_save = $_REQUEST['exit_after_save'];
		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/forum/topics_list/" . $cid . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/forum/topic_edit/" . $cid . "/" . $tid . "/");
		}

		return $res;
	}

	public function topic_publish($direct = true) {
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];

		list($is_published) = mysql_fetch_row(mysql_query("SELECT is_active FROM cms_forum_topics WHERE id = '$tid'"));
		$is_published = (int) !(bool) $is_published;

		$sql = "UPDATE cms_forum_topics SET is_active = '$is_published' WHERE id='$tid'";
		mysql_query($sql);

		if($direct)
			$this->redirect($this->pre_lang . "/admin/forum/topics_list/" . $cid . "/");
	}

	public function mess_list() {
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];

		$this->load_forms();


		$params = Array();

		$params['cid'] = $cid;
		$params['tid'] = $tid;

		list($cname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_confs WHERE id='$cid'"));
		list($tname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_topics WHERE id='$tid'"));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");
		cmsController::getInstance()->nav_arr[] = Array($tname, "/admin/forum/mess_list/$cid/$tid/", "");

		$rows = "";

		$sql = "SELECT * FROM cms_forum_messages WHERE topic_id = '$tid' ORDER BY posttime DESC";
		$result = mysql_query($sql);

		while($row = mysql_fetch_assoc($result)) {
			$mid = $row['id'];
			$posttime = $row['posttime'];
			$author_id = $row['author_id'];
			$title = $row['title'];
			$posttime = date("<b>d-m-Y</b> | H:i", $posttime);
			$ip = $row['author_ip'];

			$content = nl2br(strip_tags($row['content']));


			$at = "";

			if($author_id) {
				$author_name = cmsController::getInstance()->getModule('users')->get_user_info($author_id, "%fname% %lname% (%login%)");
				$at = "<a href='%pre_lang%/admin/users/user_edit/$author_id/' class='glink'>" . $author_name . "</a>";
			} else  {
				$at = $row['author_name'];
				if($row['author_email'])
					$at .= "<br />" . $row['author_email'];
			}

			if($row['is_active'])
				$todo = "<img src=\"/images/cms/admin/%skin_path%/ico_block.%ico_ext%\" title=\"Скрыть\" alt=\"Скрыть\" border=\"0\" />";
			else
				$todo = "<b><img src=\"/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%\" title=\"Открыть\" alt=\"Открыть\" border=\"0\" /></b>";


			$rows .= <<<ROWS

<row>
	<col style="vertical-align: top;">
		<a href="%pre_lang%/admin/forum/message_edit/$cid/$tid/$mid/" class="glink"><b>$title</b></a><br />
		$posttime
	</col>

	<col style="vertical-align: top;">
		$content
	</col>

	<col style="vertical-align: top;">$at<br />$ip</col>

	<col style="text-align: center; vertical-align: top;">
		<a href="%pre_lang%/admin/forum/message_publish/$cid/$tid/$mid/" class="glink">$todo</a>
	</col>
	<col style="text-align: center; vertical-align: top;">
		<a href="%pre_lang%/admin/forum/message_del/$cid/$tid/$mid/" class="glink" commit="Вы уверены?"><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" title="Удалить" alt="Удалить" border="0" /></a>
	</col>
</row>

ROWS;
		}

		$params['unpublished_messages'] = $this->returnUnpublishedMessages();
		$params['last_messages'] = $this->returnLastMessages();


		$params['rows'] = $rows;
		$res = $this->parse_form("messages", $params);

		return $res;
	}

	public function message_publish() {
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];
		$mid = (int) $_REQUEST['param2'];

		list($is_active) = mysql_fetch_row(mysql_query("SELECT is_active FROM cms_forum_messages WHERE id='$mid'"));
		$is_active = (int) !(bool) $is_active;
		mysql_query("UPDATE cms_forum_messages SET is_active='$is_active' WHERE id='$mid'");

		$ref = $_SERVER['HTTP_REFERER'];
		if($ref)
			$this->redirect($ref);
		else
			$this->redirect($this->pre_lang . "/admin/forum/mess_list/" . $cid . "/" . $tid);
	}

	public function message_add() {
		$res = "";
		$params = Array();

		$params['cid'] = $cid = (int) $_REQUEST['param0'];
		$params['tid'] = $tid = (int) $_REQUEST['param1'];
		$params['save_n_save'] = '<submit title="Добавить сообщение" onclick="javascript: return save_with_exit();" />';
		$params['method'] = "message_add_do";
		$params['posttime'] = date("Y-m-d H:i");
		$params['is_active'] = 1;


		list($cname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_confs WHERE id='$cid'"));
		list($tname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_topics WHERE id='$tid'"));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");
		cmsController::getInstance()->nav_arr[] = Array($tname, "/admin/forum/mess_list/$cid/$tid/", "");
		cmsController::getInstance()->nav_arr[] = Array("Добавление сообщения", "/admin/forum/message_add/$cid/$tid/", "");


		$this->load_forms();



		$res = $this->parse_form("message_add", $params);
		return $res;
	}

	public function message_add_do() {
		$res = "";

		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];

		$title = utf8_1251($_REQUEST['name']);
		$content = utf8_1251($_REQUEST['content']);
		$posttime = toTimeStamp($_REQUEST['posttime']);
		$author_id = (int) $this->CMS_ENV['user_id'];
		$author_ip = $_SERVER['REMOTE_ADDR'];

		$is_active = (int) $_REQUEST['is_active'];

		$sql = <<<INSERT

INSERT INTO cms_forum_messages
	(topic_id, title, author_id, author_ip, posttime, content, is_active)
		VALUES('$tid', '$title', '$author_id', '$author_ip', '$posttime', '$content', '$is_active')

INSERT;

		mysql_query($sql);
		if($err = mysql_error())
			return $err;

		$this->redirect($this->pre_lang . "/admin/forum/mess_list/" . $cid . "/" . $tid);

		return $res;
	}

	public function message_edit() {
		$res = "";
		$params = Array();

		$params['cid'] = $cid = (int) $_REQUEST['param0'];
		$params['tid'] = $tid = (int) $_REQUEST['param1'];
		$params['mid'] = $mid = (int) $_REQUEST['param2'];

		$params['save_n_save'] = '<submit title="Сохранить и выйти" onclick="javascript: return save_with_exit();" />&#160;&#160;<submit title="Сохранить" onclick="javascript: return save_without_exit(); " />';
		$params['method'] = "message_edit_do";


		list($cname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_confs WHERE id='$cid'"));
		list($tname) = mysql_fetch_row(mysql_query("SELECT name FROM cms_forum_topics WHERE id='$tid'"));

		//setting navibar...
		cmsController::getInstance()->nav_arr[] = Array("BACK", "", "");//title, alt
		cmsController::getInstance()->nav_arr[] = Array($cname, "/admin/forum/topics_list/$cid/", "");
		cmsController::getInstance()->nav_arr[] = Array($tname, "/admin/forum/mess_list/$cid/$tid/", "");
		cmsController::getInstance()->nav_arr[] = Array("Редактирование сообщения", "/admin/forum/message_add/$cid/$tid/", "");

		$this->load_forms();

		$sql = "SELECT * FROM cms_forum_messages WHERE id='$mid'";
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		$params['posttime'] = date("Y-m-d H:i", $row['posttime']);
		$params = (Array) $params + (Array) $row;

		$params['content'] = system_filter_str($params['content']);

		$res = $this->parse_form("message_add", $params);
		return $res;

	}

	public function message_edit_do() {
		$res = "";

		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];
		$mid = (int) $_REQUEST['param2'];

		$title = utf8_1251($_REQUEST['name']);
		$content = utf8_1251($_REQUEST['content']);
		$posttime = toTimeStamp(utf8_1251($_REQUEST['posttime']));

		$is_active = (int) $_REQUEST['is_active'];

		$sql = <<<UPDATE

UPDATE cms_forum_messages
	SET
		title = '$title',
		content = '$content',
		posttime = '$posttime',
		is_active = '$is_active'

		WHERE id = '$mid'

UPDATE;

		mysql_query($sql);
		if($err = mysql_error())
			return $err;

		$exit_after_save = $_REQUEST['exit_after_save'];
		if($exit_after_save) {
			$this->redirect($this->pre_lang . "/admin/forum/mess_list/" . $cid . "/" . $tid . "/");
		} else {
			$this->redirect($this->pre_lang . "/admin/forum/message_edit/" . $cid . "/" . $tid . "/" . $mid . "/");
		}

		

		return $res;
	}

	public function message_del() {
		$cid = (int) $_REQUEST['param0'];
		$tid = (int) $_REQUEST['param1'];
		$mid = (int) $_REQUEST['param2'];

		$sql = "DELETE FROM cms_forum_messages WHERE id='$mid'";
		mysql_query($sql);

		$ref = $_SERVER['HTTP_REFERER'];
		if($ref)
			$this->redirect($ref);
		else
			$this->redirect($this->pre_lang . "/admin/forum/mess_list/" . $cid . "/" . $tid . "/");
	}


	public function last_messages() {
		$res = "";
		$this->load_forms();
		$params = Array();

		$rows = "";

		$user_id = $this->CMS_ENV['user_id'];
		$sql = "SELECT last_login FROM cms_users WHERE id='$user_id'";
		$result = mysql_query($sql);
		list($last_login) = mysql_fetch_row($result);

//return $last_login;
		
		$sql = "SELECT fm.*, ft.name as topic_title, fc.name as conf_title, fc.id as conf_id, fm.id as mess_id, fm.is_active as is_active, fm.author_ip as author_ip FROM cms_forum_messages fm, cms_forum_topics ft, cms_forum_confs fc WHERE fm.posttime >= $last_login AND ft.id = fm.topic_id AND fc.id = ft.conf_id ORDER BY fm.posttime DESC";
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
			$title = $row['title'];

			$posttime = $row['posttime'];
			$posttime = date("<b>H:i</b> | Y-m-d", $posttime);


			$author_id = $row['author_id'];
			if($author_id) {
				$author_name = cmsController::getInstance()->getModule('users')->get_user_info($author_id, "%fname% %lname% (%login%)");
				system_pstr(&$author_name);
				$at = "<a href='%pre_lang%/admin/users/user_edit/$author_id/' class='glink'>" . $author_name . "</a>";
			} else  {
				$author_name = $row['author_name'];
				system_pstr(&$author_name);
				$at = $author_name;
			}


			$conf_title = $row['conf_title'];
			$topic_title = $row['topic_title'];

			$conf_id = $row['conf_id'];
			$topic_id = $row['topic_id'];
			$mess_id = $row['mess_id'];


			$content = $row['content'];
			system_pstr(&$content);
			$content = nl2br(strip_tags($content));

			system_pstr(&$title);
			system_pstr(&$conf_title);
			system_pstr(&$topic_title);

			$path = <<<PATH

<a href="%pre_lang%/admin/forum/message_edit/$conf_id/$topic_id/$mess_id/" class="glink"><b>$title</b></a>

<span  class='MainPage' style='color: #7E7E7E'><br />
	<a href='%pre_lang%/admin/forum/topics_list/$conf_id/' class='MainPage' style='color: #7E7E7E'>$conf_title</a> / 
	<a href='%pre_lang%/admin/forum/mess_list/$conf_id/$topic_id/' class='MainPage' style='color: #7E7E7E'>$topic_title</a>
</span>

<br /><br />

$content

PATH;

			if($row['is_active'])
				$todo = "<img src=\"/images/cms/admin/%skin_path%/ico_block.%ico_ext%\" title=\"Скрыть\" alt=\"Скрыть\" border=\"0\" />";
			else
				$todo = "<b><img src=\"/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%\" title=\"Открыть\" alt=\"Открыть\" border=\"0\" /></b>";


			$at .= "<br />" . $row['author_ip'];
			if($row['author_email'])
				$at .= "<br />" . $row['author_email'];


			$rows .= <<<ROWS

<row>
	<col style="vertical-align: top;">$path</col>
	<col style="vertical-align: top;">$posttime</col>
	<col style="text-align: center; vertical-align: top;">$at</col>
	<col style="text-align: center; vertical-align: top;"><a href="%pre_lang%/admin/forum/message_publish/$conf_id/$topic_id/$mess_id/" class="glink">$todo</a></col>
	<col style="text-align: center; vertical-align: top;"><a href='%pre_lang%/admin/forum/message_del/$conf_id/$topic_id/$mess_id/' class='glink'><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" title="Удалить" alt="Удалить" border="0" /></a></col>
</row>

ROWS;
		}


		$params['rows'] = $rows;
		$params['curr_time'] = time();
		$res = $this->parse_form("lu_messages", $params);
		return $res;
	}

	public function unpublished_messages() {
		$res = "";
		$this->load_forms();
		$params = Array();

		$rows = "";

		$user_id = $this->CMS_ENV['user_id'];
		$sql = "SELECT last_login FROM cms_users WHERE id='$user_id'";
		$result = mysql_query($sql);
		list($last_login) = mysql_fetch_row($result);
		
		$sql = "SELECT fm.*, ft.name as topic_title, fc.name as conf_title, fc.id as conf_id, fm.id as mess_id, fm.is_active as is_active, fm.author_ip as author_ip FROM cms_forum_messages fm, cms_forum_topics ft, cms_forum_confs fc WHERE fm.is_active = 0 AND ft.id = fm.topic_id AND fc.id = ft.conf_id ORDER BY fm.posttime DESC";
		$result = mysql_query($sql);
		while($row = mysql_fetch_assoc($result)) {
			$title = $row['title'];

			$posttime = $row['posttime'];
			$posttime = date("<b>H:i</b> | Y-m-d", $posttime);


			$author_id = $row['author_id'];
			if($author_id) {
				$author_name = cmsController::getInstance()->getModule('users')->get_user_info($author_id, "%fname% %lname% (%login%)");
				$at = "<a href='%pre_lang%/admin/users/user_edit/$author_id/' class='glink'>" . $author_name . "</a>";
			} else 
				$at = $row['author_name'];

			$conf_title = $row['conf_title'];
			$topic_title = $row['topic_title'];

			$conf_id = $row['conf_id'];
			$topic_id = $row['topic_id'];
			$mess_id = $row['mess_id'];

			$content = $row['content'];
			system_pstr(&$content);
			$content = nl2br(strip_tags($content));

			$path = <<<PATH

<a href="%pre_lang%/admin/forum/message_edit/$conf_id/$topic_id/$mess_id/" class="glink"><b>$title</b></a>

<span  class='MainPage' style='color: #7E7E7E'><br />
	<a href='%pre_lang%/admin/forum/topics_list/$conf_id/' class='MainPage' style='color: #7E7E7E'>$conf_title</a> / 
	<a href='%pre_lang%/admin/forum/mess_list/$conf_id/$topic_id/' class='MainPage' style='color: #7E7E7E'>$topic_title</a>
</span>

<br /><br />
$content
PATH;


			if($row['is_active'])
				$todo = "<img src=\"/images/cms/admin/%skin_path%/ico_block.%ico_ext%\" title=\"Скрыть\" alt=\"Скрыть\" border=\"0\" />";
			else
				$todo = "<b><img src=\"/images/cms/admin/%skin_path%/ico_unblock.%ico_ext%\" title=\"Открыть\" alt=\"Открыть\" border=\"0\" /></b>";


			$at .= "<br />" . $row['author_ip'];
			if($row['author_email'])
				$at .= "<br />" . $row['author_email'];

			$rows .= <<<ROWS

<row>
	<col style="vertical-align: top;">$path</col>
	<col style="vertical-align: top;">$posttime</col>
	<col style="text-align: center; vertical-align: top;">$at</col>
	<col style="text-align: center; vertical-align: top;"><a href="%pre_lang%/admin/forum/message_publish/$conf_id/$topic_id/$mess_id/" class="glink">$todo</a></col>
	<col style="text-align: center; vertical-align: top;"><a href='%pre_lang%/admin/forum/message_del/$conf_id/$topic_id/$mess_id/' class='glink'><img src="/images/cms/admin/%skin_path%/ico_del.%ico_ext%" title="Удалить" alt="Удалить" border="0" /></a></col>
</row>

ROWS;
		}


		$params['rows'] = $rows;
		$params['curr_time'] = time();
		$res = $this->parse_form("lu_messages", $params);
		return $res;

	}
*/
};

?>