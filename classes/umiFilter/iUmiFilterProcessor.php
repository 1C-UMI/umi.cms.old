<?php

	interface iUmiFilterProcessor {
		public static function applyFilter(umiFilter $oUmiFilter);
		public static function renderFilter(umiFilter $oUmiFilter);
		public static function getFilterDescription(umiFilter $oUmiFilter);
	}

?>