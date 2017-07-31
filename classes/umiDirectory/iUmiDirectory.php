<?php

interface iUmiDirectory {
	public function getIsBroken();
	public function getFSObjects($i_obj_type=0, $s_mask="", $b_only_readable=false);
	public function getFiles($s_mask="", $b_only_readable=false);
	public function getDirectories($s_mask="", $b_only_readable=false);
}



?>