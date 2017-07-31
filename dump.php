#!/usr/local/bin/php

<?php
	`mysqldump --default-character-set=utf8 -ubet4win -pJhjgsd734bKKhjds62 mt_group  > dump.sql`;
	`chmod 0777 ./dump.sql`;

	exit(0);
?>