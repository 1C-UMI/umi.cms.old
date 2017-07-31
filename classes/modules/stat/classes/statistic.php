<?php

define('STAT_SITES', 1);
define('STAT_SEARCH', 2);
define('STAT_PR', 3);
define('STAT_TICKET', 4);
define('STAT_COUPON', 5);

define('STAT_RETURN_INTERVAL', 15);

class statistic
{
    private $time;
    private $noCount;

    private $referer;
    private $uri;
    private $serverName;
    private $remoteAddr;

    public function __construct($time = null)
    {
        if (!empty($time)) {
            $this->time = $time;
        } else {
            $this->time = time();
        }
    }

    public function run()
    {
        if ($this->noCount) {
            return null;
        }

        // проверяем, является ли посетитель поисковым ботом
        if (!isset($_SESSION['stat']['isSearchBot'])) {
            $_SESSION['stat']['isSearchBot'] = $this->isSearchBot();
        }

        if ($_SESSION['stat']['isSearchBot'] == true) {
            // если поисковый бот - заканчиваем работу
            return false;
        }

        if (isset($_SESSION['stat']['doLogin'])) {
            $login = $this->getLogin();
            // если уже были залогинены
            if (isset($_SESSION['stat']['loginId'])) {
                // удаляем всю сессионную информацию и считаем заново
                unset($_SESSION['stat']);
                unset($_COOKIE['stat_id']);
            } else {
                // если не были залогинены
                $this->setUpHostId();

                $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_users` WHERE `login` = '" . mysql_real_escape_string($login) . "' AND `host_id` = " . $_SESSION['stat']['site_id'];
                $res = mysql_query($qry);
                $row = mysql_fetch_assoc($res);
                // если такой пользователь уже есть
                if (isset($row['id'])) {
                    $user_id = $row['id'];
                } else {
                    // если такого пользователя не было
                    $user_id = $this->addUser();
                }

                // если пользователь уже походил по сайту - заменяем ид пользователя на текущего
                if (isset($_SESSION['stat']['path_id'])) {
                    $qry = "UPDATE `cms_stat_paths` SET `user_id` = " . $user_id . " WHERE `id` = " . $_SESSION['stat']['path_id'];
                    mysql_query($qry);
                }

                $_SESSION['stat']['loginId'] = $user_id;
                //unset($_SESSION['stat']['doLogin']);
            }
        }

        // если пользователь только зашёл на сайт
        // устанавливаем необходимые для работы переменные и определяем, откуда пришёл пользователь
        if (!isset($_SESSION['stat']['id'])) {
            // если в куках нет id
            if (!isset($_COOKIE['stat_id'])) {
                // устанавливаем куку на 10 лет
                setcookie('stat_id', session_id(), strtotime('+10 years'));
                // пишем этот же id в сессию
                $_SESSION['stat']['id'] = session_id();

                $_SESSION['stat']['user_id'] = $this->addUser();
            } else {
                // если в куках значение есть, проверяем есть ли такое в бд, и если есть берём его оттуда
                $sessid = mysql_real_escape_string($_COOKIE['stat_id']);
                $res = mysql_query("SELECT SQL_CACHE `id` FROM `cms_stat_users` WHERE `session_id` = '" . $sessid . "'");
                $row = mysql_fetch_assoc($res);
                if (isset($row['id'])) {
                    $_SESSION['stat']['id'] = $sessid;

                    $_SESSION['stat']['user_id'] = $row['id'];

                    // проверяем когда пользователь в последний раз был на сайте
                    // если посещение было в течение 15 минут (STAT_RETURN_INTERVAL)
                    // тогда сессия та же
                    $qry = "SELECT SQL_CACHE UNIX_TIMESTAMP(MAX(`h`.`date`)) AS `ts`, `p`.`id` FROM `cms_stat_hits` `h`
                             INNER JOIN `cms_stat_paths` `p` ON `p`.`id` = `h`.`path_id`
                              WHERE `p`.`user_id` = " . $row['id'] . "
                               GROUP BY `p`.`user_id`";

                    //$res = mysql_query("SELECT SQL_CACHE UNIX_TIMESTAMP(`date`) AS `ts`, `id` FROM `cms_stat_paths` WHERE `user_id` = " . $row['id'] . " ORDER BY `id` DESC LIMIT 1");
                    $res = mysql_query($qry);
                    $row = mysql_fetch_assoc($res);
                    if (isset($row['ts']) && ($this->time - $row['ts']) / 60 <= STAT_RETURN_INTERVAL) {
                        // восстанавливаем прежний path_id
                        $_SESSION['stat']['path_id'] = $row['id'];

                        // и число посещённых за сессию страниц
                        $res = mysql_query("SELECT COUNT(*) AS `cnt` FROM `cms_stat_hits` WHERE `path_id` = " . $row['id']);
                        $row = mysql_fetch_assoc($res);
                        $_SESSION['stat']['number_in_path'] = $row['cnt'];
                    }
                } else {
                    // устанавливаем куку на 10 лет
                    setcookie('stat_id', session_id(), strtotime('+10 years'), '/');
                    // пишем этот же id в сессию
                    $_SESSION['stat']['id'] = session_id();

                    $_SESSION['stat']['user_id'] = $this->addUser();
                }
            }

            $this->setUpHostId();

            $source_id = 0;
            $source_type = 0;

            // получаем адрес реферера
            $referer = $this->getReferer();
            $url_array = parse_url($referer);

            if (!isset($url_array['host'])) {
                $url_array['host'] = '';
            }

            if (strpos($url_array['host'], 'www.') === 0) {
                $domain = substr($url_array['host'], 4);
            } else {
                $domain = $url_array['host'];
            }
            // является ли источник - рекламной кампанией
            $qry = "SELECT SQL_CACHE `pr_id` FROM `cms_stat_sources_pr_sites`
                    WHERE '" . mysql_real_escape_string($domain) . "' LIKE `url`";
            $res = mysql_query($qry);
            $row = mysql_fetch_assoc($res);
            if ($row) {
                $source_id = $this->getSourceId($row['pr_id'], STAT_PR);
            }

            // случай когда источник является "входным билетом"
            if (!$source_id && !empty($_SESSION['stat']['ticket_id'])) {
                $source_id = $_SESSION['stat']['ticket_id'];
                $source_type = STAT_TICKET;
            }

            // результаты поиска
            if (!$source_id) {
                $qry = "SELECT SQL_CACHE `id`, `varname` FROM `cms_stat_sources_search_engines`
                           WHERE '" . mysql_real_escape_string($domain) . "' LIKE `url_mask`";
                $res = mysql_query($qry);
                $row = mysql_fetch_assoc($res);
                // если такая поисковая система существует
                if (isset($row['id'])) {
                    $engine_id = $row['id'];
                    // если в адресе содержится REQUEST_URI
                    if (isset($url_array['query'])) {
                        $qry = $url_array['query'];
                        parse_str($qry, $arr);
                        // если в REQUEST_URI есть переменная, в которой находится искомый текст
                        if (isset($arr[$row['varname']])) {
                            $text = $arr[$row['varname']];
                            
                            $text = $this->convertCharset($text);

                            // ищем нужную комбинацию поисковой системы и искомого слова
                            $qry = "SELECT SQL_CACHE `s`.`id` FROM `cms_stat_sources_search_queries` `q`
                                     INNER JOIN `cms_stat_sources_search` `s` ON `s`.`text_id` = `q`.`id`
                                      WHERE `q`.`text` = '" . mysql_real_escape_string($text) . "' AND `engine_id` = " . $engine_id;
                            $res = mysql_query($qry);
                            $row = mysql_fetch_assoc($res);
                            // если таковые есть
                            if (isset($row['id'])) {
                                // то это и есть источник
                                $source_id = $row['id'];
                            } else {
                                // иначе - смотрим, есть ли искомая фраза в БД
                                $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_sources_search_queries` WHERE `text` = '" . mysql_real_escape_string($text) . "'";
                                $res = mysql_query($qry);
                                $row = mysql_fetch_assoc($res);
                                if (isset($row['id'])) {
                                    // если есть - то добавляем комбинацию поисковая система - фраза в таблицу
                                    $qry = "INSERT INTO `cms_stat_sources_search` (`engine_id`, `text_id`) VALUES (" . $engine_id . ", " . $row['id'] . ")";
                                    mysql_query($qry);

                                    $source_id = mysql_insert_id();
                                } else {
                                    // если нет - то добавляем искомую фразу
                                    $qry = "INSERT INTO `cms_stat_sources_search_queries` (`text`) VALUES ('" . mysql_real_escape_string($text) . "')";
                                    mysql_query($qry);

                                    $word_id = mysql_insert_id();

                                    // и сопоставляем её с конкретной поисковой системой
                                    $qry = "INSERT INTO `cms_stat_sources_search` (`engine_id`, `text_id`) VALUES (" . $engine_id . ", " . $word_id . ")";
                                    mysql_query($qry);

                                    $source_id = mysql_insert_id();
                                }
                            }

                            $source_id = $this->getSourceId($source_id, STAT_SEARCH);
                        }
                    }
                }
            }

            // ссылающиеся сайты
            if (!$source_id) {
                if ($domain) {
                    $qry = "SELECT SQL_CACHE `group_id` FROM `cms_stat_sites` WHERE `name` = '" . mysql_real_escape_string($domain) . "'";
                    $res = mysql_query($qry);
                    $row = mysql_fetch_assoc($res);

                    if (!isset($row['group_id']) || $row['group_id'] != $_SESSION['stat']['site_id']) {

                        $source_type = STAT_SITES;

                        $qry = "SELECT SQL_CACHE `s`.`id` FROM `cms_stat_sources_sites_domains` `d`
                             INNER JOIN `cms_stat_sources_sites` `s` ON `s`.`domain` = `d`.`id`
                              WHERE `d`.`name` = '" . mysql_real_escape_string($domain) . "'";
                        $res = mysql_query($qry);
                        $row = mysql_fetch_assoc($res);
                        // если ссылающаяся страница сайта найдена
                        if (isset($row['id'])) {
                            $source_id = $row['id'];
                        } else {
                            // если не найдена, то ищем - есть ли вообще такой ссылающийся домен
                            $qry = "SELECT * FROM `cms_stat_sources_sites_domains` `d`
                                  WHERE `d`.`name` = '" . mysql_real_escape_string($domain) . "'";
                            $res = mysql_query($qry);
                            $row = mysql_fetch_assoc($res);
                            // если есть домен

                            $uri_ref = $url_array['path'] . (isset($url_array['query']) ? "?" . $url_array['query'] : '');

                            if (isset($row['id'])) {
                                // то добавляем сылающуюся с этого домена страницу
                                $qry = "INSERT INTO `cms_stat_sources_sites` (`uri`, `domain`) VALUES ('" . mysql_real_escape_string($uri_ref) . "', " . $row['id'] . ")";
                                mysql_query($qry);

                                $source_id = mysql_insert_id();
                            } else {
                                // если домена нет - то добавляем домен
                                $qry = "INSERT INTO `cms_stat_sources_sites_domains` (`name`) VALUES ('" . mysql_real_escape_string($domain) . "')";
                                mysql_query($qry);

                                $rel_site_id = mysql_insert_id();

                                $qry = "INSERT INTO `cms_stat_sources_sites` (`uri`, `domain`) VALUES ('" . mysql_real_escape_string($uri_ref) . "', " . $rel_site_id . ")";
                                mysql_query($qry);

                                $source_id = mysql_insert_id();
                            }
                        }

                        $source_id = $this->getSourceId($source_id, STAT_SITES);
                    }
                }
            }

            // если не создан path - создаём
            if (!isset($_SESSION['stat']['path_id'])) {
                // установка path_id
                $qry = "INSERT INTO `cms_stat_paths` (`user_id`, `date`, `host_id`" . (isset($source_id) ? ", `source_id`" : "") . ") VALUES (" . $_SESSION['stat']['user_id'] . ", '" . $this->getNow() . "', " . $_SESSION['stat']['site_id'] . "" . (isset($source_id) ? ", " . $source_id : "") . ")";
                mysql_query($qry);

                $_SESSION['stat']['path_id'] = mysql_insert_id();

                $_SESSION['stat']['number_in_path'] = 0;
            }
        }

        // фиксирование хитов
        // поиск необходимой страницы
        $uri = mysql_real_escape_string($this->getUri());
        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_pages` WHERE `uri` = '" . $uri . "' AND `host_id` = " . $_SESSION['stat']['site_id'];
        $res = mysql_query($qry);
        $row = mysql_fetch_assoc($res);
        if (isset($row['id'])) {
            $page_id = $row['id'];
            // проверяем - не нажал ли пользователь f5 (не находится ли на той же странице)
            if (isset($_SESSION['stat']['last_page_id']) && $_SESSION['stat']['last_page_id'] == $page_id) {
                // если да - тогда ничего не делаем и выходим из метода
                return;
            }
        } else {
            if (strrpos($this->getUri(), '/') === 0) {
                $section = 'index';
            } else {
                $section = mysql_real_escape_string(substr($this->getUri(), 1, strpos($this->getUri(), '/', 1) - 1));
            }

            $qry = "INSERT INTO `cms_stat_pages` (`uri`, `host_id`, `section`) VALUES ('" . $uri . "', '" . $_SESSION['stat']['site_id'] . "', '" . $section . "')";
            mysql_query($qry);

            $page_id = mysql_insert_id();
        }

        // запоминаем текущую страницу
        $_SESSION['stat']['last_page_id'] = $page_id;

        $_SESSION['stat']['number_in_path']++;

        $qry = "INSERT INTO `cms_stat_hits` (`page_id`, `date`, `hour`, `day_of_week`, `week`, `day`, `month`, `year`, `path_id`, `number_in_path`" . ((isset($_SESSION['stat']['prev_page_id'])) ? ', `prev_page_id`' : '') . ") VALUES
                 (" . $page_id . ", '" . $this->getNow() . "', HOUR('" . $this->getNow() . "'), DATE_FORMAT('" . $this->getNow() . "', '%w'), WEEK('" . $this->getNow() . "'), DAY('" . $this->getNow() . "'), MONTH('" . $this->getNow() . "'), YEAR('" . $this->getNow() . "'), " . $_SESSION['stat']['path_id'] . ", " . $_SESSION['stat']['number_in_path'] . (isset($_SESSION['stat']['prev_page_id']) ? ", " . $_SESSION['stat']['prev_page_id'] : '') . ")";
        mysql_query($qry);

        $hit_id = mysql_insert_id();

        $_SESSION['stat']['prev_page_id'] = $page_id;

        // срабатывание событий
        $qry = "(SELECT SQL_CACHE `r`.`event_id` FROM `cms_stat_events_urls` `u`
                 INNER JOIN `cms_stat_events_rel` `r` ON `r`.`metaevent_id` = `u`.`event_id`
                  WHERE `u`.`page_id` = " . $page_id . ")

                UNION DISTINCT

                (SELECT SQL_CACHE `event_id` FROM `cms_stat_events_urls`
                  WHERE `page_id` = " . $page_id . ")";

        $res = mysql_query($qry);

        // генерируем запрос для добавления событий
        $q = '';
        while($row = mysql_fetch_assoc($res)) {
            $q .= '(' . $row['event_id'] . ', ' . $hit_id . '), ';
        }

        // в массиве $_SESSION['stat']['entryQry'] сохраняются события, зафиксированные до запуска сбора статистики (например вызов установки события вручную)
        if (isset($_SESSION['stat']['entryQry'])) {
            foreach ($_SESSION['stat']['entryQry'] as $val) {
                $q .= '(' . $val . ', ' . $hit_id . '), ';
            }

            unset($_SESSION['stat']['entryQry']);
        }

        if (isset($_SESSION['stat']['events'])) {
            foreach ($_SESSION['stat']['events'] as $val) {
                $q .= '(' . $val . ', ' . $hit_id . '), ';
            }

            unset($_SESSION['stat']['events']);
        }

        if ($q) {
            $q = substr($q, 0, -2);
            $qry = "INSERT INTO `cms_stat_events_collected` (`event_id`, `hit_id`) VALUES " . $q;
            mysql_query($qry);
        }

    }

    /**
     * Метод получения реферера
     *
     * @return string
     */
    private function getReferer()
    {
        return !empty($this->referer) ? $this->referer : (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
    }

    /**
     * Метод получения запроса
     *
     * @return string
     */
    private function getUri()
    {
        return !empty($this->uri) ? $this->uri : $_SERVER['REQUEST_URI'];
    }

    /**
     * Метод получения имени сервера
     *
     * @return string
     */
    private function getServerName()
    {
        return !empty($this->serverName) ? $this->serverName : $_SERVER['SERVER_NAME'];
    }

    /**
     * Метод получения IP-адреса клиента
     *
     * @return string
     */
    private function getRemoteAddr()
    {
        return !empty($this->remoteAddr) ? $this->remoteAddr : $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Метод установки реферера
     *
     * @param string $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * Метод установки запроса
     *
     * @param string $uri
     */
    public function setUri($uri)
    {
        $this->uri = $uri;
    }

    /**
     * Метод установки имени сервера
     *
     * @param string $serverName
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * Метод установки IP-адреса клиента
     *
     * @param unknown_type $remoteAddr
     */
    public function setRemoteAddr($remoteAddr)
    {
        $this->remoteAddr = $remoteAddr;
    }

    /**
     * Установка в сессионные переменные данных о том, на каком хосте запущен класс для сбора статистики
     *
     */
    private function setUpHostId()
    {
        if (!isset($_SESSION['stat']['site_id'])) {
            $name = mysql_real_escape_string($this->getServerName());
            $qry = "SELECT SQL_CACHE `group_id` FROM `cms_stat_sites` WHERE `name` = '" . $name . "'";
            $res = mysql_query($qry);
            $row = mysql_fetch_assoc($res);
            if (isset($row['group_id'])) {
                $_SESSION['stat']['site_id'] = $row['group_id'];
            } else {
                $qry = "INSERT INTO `cms_stat_sites_groups` (`name`) VALUES ('" . $name . "')";
                mysql_query($qry);
                $id = mysql_insert_id();
                $qry = "INSERT INTO `cms_stat_sites` (`name`, `group_id`) VALUES ('" . $name . "', " . $id . ")";
                mysql_query($qry);

                $_SESSION['stat']['site_id'] = $id;
            }
        }
    }

    /**
     * метод установки источника текущего посещения как "входной билет"
     * вызывается как site/stat/ticket/ticketname
     *
     * @param string $name имя билета
     */
    public function ticket($name)
    {
        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_sources_ticket` WHERE `url` = '" . mysql_real_escape_string($name) . "'";
        $res = mysql_query($qry);
        if ($row = mysql_fetch_assoc($res)) {
            $source_id = $this->getSourceId($row['id'], STAT_TICKET);
            $_SESSION['stat']['ticket_id'] = $source_id;
        }
    }

    /**
     * Метод, вызываемый ЦМС при входе на специальную "точку входа". Пример: «www.mysite.ru/entry/somename».
     *
     * @param string $name имя точки входа
     */
    public function entry($name)
    {
        $this->noCount = true;

        $this->setUpHostId();

        $qry = "SELECT SQL_CACHE `p`.`url`, `e`.`event_id` FROM `cms_stat_entry_points` `p`
                 LEFT JOIN `cms_stat_entry_points_events` `e` ON `e`.`entry_point_id` = `p`.`id`
                  WHERE `name` = '" . mysql_real_escape_string($name) . "' AND `host_id` = " . $_SESSION['stat']['site_id'];

        $res = mysql_query($qry);

        if (isset($_SESSION['stat']['entryQry'])) {
            unset($_SESSION['stat']['entryQry']);
        }

        while ($row = mysql_fetch_assoc($res)) {
            if (!isset($redirect)) {
                $redirect = $row['url'];
            }
            $_SESSION['stat']['entryQry'][] = $row['event_id'];
        }

        if (!isset($redirect)) {
            $redirect = '/';
        }

        header('Location:' . $redirect);

        exit;
    }

    /**
     * Метод, для ручного вызова регистрации события
     *
     * @param string $name имя события
     */
    public function event($name)
    {
        $this->setUpHostId();
        
        $name = mysql_real_escape_string($name);

        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_events`
                 WHERE `name` = '" . $name . "' AND `host_id` = " . $_SESSION['stat']['site_id'];

        $res = mysql_query($qry);

        if ($row = mysql_fetch_assoc($res)) {
            $id = $row['id'];
        } else {
            $qry = "INSERT INTO `cms_stat_events` (`description`, `name`, `type`, `profit`, `host_id`) VALUES ('" . $name . "', '" . $name . "', 2, 0, " . $_SESSION['stat']['site_id'] . ")";
            mysql_query($qry);
            $id = mysql_insert_id();
        }

        $_SESSION['stat']['events'][] = $id;
    }

    /**
     * Метод, возвращающий id источника посещения
     *
     * @param integer $concreteSourceId
     * @param integer $type
     * @return integer
     */
    private function getSourceId($concreteSourceId, $type)
    {
        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_sources` WHERE `concrete_src_id` = " . $concreteSourceId . " AND `src_type` = " .  $type;
        $res = mysql_query($qry);
        $row = mysql_fetch_assoc($res);
        if (isset($row['id'])) {
            $source_id = $row['id'];
        } else {
            $qry = "INSERT INTO `cms_stat_sources` (`src_type`, `concrete_src_id`) VALUES (" . $type . ", " . $concreteSourceId . ")";
            mysql_query($qry);
            $source_id = mysql_insert_id();
        }

        return $source_id;
    }

    /**
     * Метод для добавления нового пользователя в статистику
     *
     * @return integer
     */
    private function addUser()
    {
        $this->setUpHostId();
        $qry = "INSERT INTO `cms_stat_users` (`session_id`, `first_visit`, `login`, `os_id`, `browser_id`, `ip`, `location`, `js_version`, `host_id`) VALUES
                 ('" . session_id() . "', '" . $this->getNow() . "', '" . mysql_real_escape_string($this->getLogin()) . "', '" . (int)$this->getOsId() . "', '" . (int)$this->getBrowserId() . "', '" . mysql_real_escape_string($this->getRemoteAddr()) . "', '" . $this->getLocation($this->getRemoteAddr()) . "', '" . $this->getJsVersion() . "', " . $_SESSION['stat']['site_id'] . ")";
        mysql_query($qry);

        return mysql_insert_id();
    }

    /**
    * Метод получения id текущего пользователя
    *
    * @return integer
    */
    public function getUserId()
    {
        return $_SESSION['stat']['loginId'];
    }

    /**
     * Метод для получения логина текущего пользователя. Должно быть получено из ЦМС с помощью специального API
     *
     * @todo заменить генератор на получение реальных данных
     * @return string
     */
    private function getLogin()
    {
	if($users_inst = cmsController::getInstance()->getModule("users")) {
	    if ($users_inst->is_auth()) {
    		return $users_inst->user_id;
    	    } else {
    		return false;
    	    }
	} else {
	    return false;
	}
    }

    public function doLogin()
    {
        $_SESSION['stat']['doLogin'] = true;
    }

    /**
     * Метод для получения идентификатора браузера текущего клиента
     *
     * @return integer
     */
    private function getBrowserId()
    {
        require_once dirname(__FILE__) . '/libs/detect.php';
        $browser = mysql_real_escape_string(Net_UserAgent_Detect::getBrowserString());
        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_users_browsers` WHERE `name` = '" . $browser . "'";
        $res = mysql_query($qry);
        $row = mysql_fetch_assoc($res);
        if (isset($row['id'])) {
            return $row['id'];
        } else {
            $qry = "INSERT INTO `cms_stat_users_browsers` (`name`) VALUES ('" . $browser . "')";
            mysql_query($qry);
            return mysql_insert_id();
        }
    }

    /**
     * Метод для получения идентификатора Операционной Системы текущего клиента
     *
     * @return integer
     */
    private function getOsId()
    {
        require_once dirname(__FILE__) . '/libs/detect.php';
        $os = mysql_real_escape_string(Net_UserAgent_Detect::getOSString());
        $qry = "SELECT SQL_CACHE `id` FROM `cms_stat_users_os` WHERE `name` = '" . $os . "'";
        $res = mysql_query($qry);
        $row = mysql_fetch_assoc($res);
        if (isset($row['id'])) {
            return $row['id'];
        } else {
            $qry = "INSERT INTO `cms_stat_users_os` (`name`) VALUES ('" . $os . "')";
            mysql_query($qry);
            return mysql_insert_id();
        }
    }

    /**
     * Метод для получения версии JS
     *
     * @return string
     */
    private function getJsVersion()
    {
        require_once dirname(__FILE__) . '/libs/detect.php';
        return mysql_real_escape_string(Net_UserAgent_Detect::getFeature('javascript'));
    }

    /**
     * Метод для получения местонахождения пользователя по IP
     *
     * @todo заменить на получение актуальных данных из специальной БД
     * @param string $ip IP-клиента
     * @return string местонахождение
     */
    private function getLocation($ip)
    {
        return substr(md5(microtime()), 0, mt_rand(3, 15));
    }

    /**
     * Метод получения текущего времени в формате mysql
     *
     * @return string
     */
    private function getNow()
    {
        return date('Y-m-d H:i:s', $this->time);
    }

    /**
     * Метод, проверяющий - является ли поисковым ботом текущий клиент
     *
     * @todo заменить на метод, получающий по имеющимся данным - бот посетитель или нет
     * @return boolean
     */
    private function isSearchBot()
    {
        // тут заменить на взаимодействие с другими модулями
        // которые распознают в посетителе бота
        return false;
    }
    
    /**
     * Метод для конвертирования из utf8 в cp1251
     *
     * @return string
     */
    private function convertCharset($text)
    {
        $tmp = error_reporting(0);
        $textConverted = base64_decode($text);
        error_reporting($tmp);
        if ($textConverted) {
            $text = $textConverted;
        }


        $tmp = error_reporting(0);
        $textConverted = iconv('UTF-8', 'CP1251', $text);
        error_reporting($tmp);
        if ($textConverted) {
            $text = $textConverted;
        }
	
	if(!stat::isStringCP1251($text)) {
		return false;
	}
	
        return $text;
    }
}

?>