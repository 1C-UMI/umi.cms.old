<?php
	include dirname(__FILE__) . "/iUmiMimePart.php";
	include dirname(__FILE__) . "/umiMimePart.php";


	class umiMail implements iUmiMail {
		private $template, $is_commited = false, $is_sended = false,
		$subject = "", $from_email = "", $from_name = false,
		$recipients = Array(),
		$files = Array(),
		$priority,
		$mess_body,	$mail_charset, $content, $boundary, $sTxtBody, $arrHeaders = array(), $arrContentImages,
		$arrAttachmentsImages = array(), $arrAttachments = array();
		private static $arrImagesCache = array(), $arrAttachmentsCache = array();


		public function __construct($template = "default") {
			$this->template = $template;
			$this->boundary= md5(uniqid("myboundary"));
			$this->charset="windows-1251";
			$this->priority="normal";
		}



		public function addRecipient($email, $name = false) {
			if(self::checkEmail($email)) {
				$info = Array($email, $name);
				if(in_array($info, $this->recipients) === false) {
					$this->recipients[] = $info;
				}
				return true;
			} else {
				return false;
			}
		}


		public function setFrom($email, $name = false) {
			if(self::checkEmail($email)) {
				$this->from_email = $email;
				$this->from_name = $name;

				return true;
			} else {
				return false;
			}
		}

		public function setSubject($subject) {
			$this->subject = (string) $subject;
		}


		public function setContent($contentString) {
			$this->content = (string) $contentString;
			$this->content = templater::getInstance()->parseInput($this->content);
		}


		public function setTxtContent($sTxtContent) {
			$this->sTxtBody = (string) $sTxtContent;
		}

		public function setPriorityLevel($level = "normal") {
			switch($level) {
				case "highest": $this->priority='1 (Highest)'; break;
				case "hight":   $this->priority='2 (Hight)';   break;
				case "normal":  $this->priority='3 (Normal)';  break;
				case "low":     $this->priority='4 (Low)';     break;
				case "lowest":  $this->priority='5 (Lowest)';  break;
				default:        $this->priority='3 (Normal)';  break;
			}
		}


		public function setImportanceLevel($level = "normal") {
			//TODO
		}


		public function commit() {
			$this->is_commited = true;
		}

		private function formAttachement() {

		}

		private function addHTMLImage($sImagePath, $sCType = "image/jpeg") {
			$sRealPath = $sImagePath;
			if (strtolower(substr($sImagePath, 0, 7)) !== 'http://') {
				if (!file_exists($sImagePath)) {
					$sRealPath = 'http://' . $_SERVER['SERVER_NAME'] . "/" . ltrim($sImagePath , '/');
				}
			}

			if (isset(self::$arrImagesCache[$sRealPath])) {
				$this->arrAttachmentsImages[$sRealPath] = self::$arrImagesCache[$sRealPath];
				$this->arrContentImages[$sImagePath] = $sRealPath;
				return true;
			}

			if (false !== ($sImageData = @file_get_contents($sRealPath))) {
				$sBaseName = basename($sRealPath);
				$this->arrAttachmentsImages[$sRealPath] = array(
						'name' => $sBaseName,
						'path' => $sImagePath,
						'data' => $sImageData,
						'content-type' => $sCType,
						'sizes' => getimagesize($sImagePath),
						'cid' => md5(uniqid(rand(), true))
						);
				self::$arrImagesCache[$sRealPath] = $this->arrAttachmentsImages[$sRealPath];
				$this->arrContentImages[$sImagePath] = $sRealPath;
				return true;
			} else {
				return false;
			}
		}


		private function addAttachment($sPath, $sCType="application/octet-stream") {
			if (isset(self::$arrAttachmentsCache[$sPath])) {
				$this->arrAttachments[$sPath] = self::$arrAttachmentsCache[$sPath];
				return true;
			}

			$sBaseName = basename($sPath);
			if (false !== ($sFileData = @file_get_contents($sPath))) {
				$this->arrAttachments[$sPath] = array(
						'name' => $sBaseName,
						'path' => $sPath,
						'data' => $sFileData,
						'content-type' => $sCType,
						'disposition' => 'attachment',
						'cid' => md5(uniqid(rand(), true))
						);
				self::$arrAttachmentsCache[$sPath] = $this->arrAttachments[$sPath];
				return true;
			} else {
				return false;
			}
		}

		public static function clearFilesCahce() {
			self::$arrAttachmentsCache = array();
			self::$arrImagesCache = array();
		}

		public function getHeaders($arrXHeaders = array(), $bOverwrite = false) {
			$arrHeaders =  array();
			$arrHeaders['MIME-Version'] = '1.0';
			$arrHeaders = array_merge($arrHeaders, $arrXHeaders);
			$this->arrHeaders = $bOverwrite? array_merge($this->arrHeaders, $arrHeaders): array_merge($arrHeaders, $this->arrHeaders);
			return $this->encodeHeaders($this->arrHeaders);
		}

		private function encodeHeaders($arrHeaders) {
			$arrResult = array();

			foreach ($arrHeaders as $sHdrName => $sHdrVal) {
				$arrHdrVals = preg_split("/(\s)/", $sHdrVal, -1, PREG_SPLIT_DELIM_CAPTURE);
				$sPrevVal = "";
				$sEncHeader = "";
				foreach ($arrHdrVals as $sHdrVal) {
					if (!trim($sHdrVal)) {
						$sPrevVal .= $sHdrVal;
						continue;
					} else {
						$sHdrVal = $sPrevVal . $sHdrVal;
						$sPrevVal = "";
					}
					$sQPref = $sQSuff = '';
					if ($sHdrVal{0} == "\"") {
						$sHdrVal = substr($sHdrVal, 1);
						$sQPref = "\"";
					}
					if ($sHdrVal{strlen($sHdrVal)-1} == "\"") {
						$sHdrVal = substr($sHdrVal, 0, -1);
						$sQSuff = "\"";
					}
					if (preg_match('/[\x80-\xFF]{1}/', $sHdrVal)) {
						$sHdrVal = iconv_mime_encode($sHdrName, $sHdrVal, array('input-charset' => 'WINDOWS-1251', 'output-charset' => 'WINDOWS-1251'));
						$sHdrVal = preg_replace("/^{$sHdrName}\:\ /", "", $sHdrVal);
					}
					$sEncHeader .= $sQPref . $sHdrVal . $sQSuff;
				}
				$arrResult[$sHdrName] = $sEncHeader;
			}

			return $arrResult;
		}

		private function parseContent() {
			$content = $this->content;

			list($template_body) = def_module::loadTemplates("tpls/mail/" . $this->template . ".tpl", "body");
			$block_arr = Array();

			$block_arr['header'] = $this->subject;
			$block_arr['content'] = $this->content;

			$sContent = def_module::parseTemplate($template_body, $block_arr);

			$arrImagesUrls1 = array();
			$arrImagesUrls2 = array();
			if (preg_match_all('#<\w+[^>]+\s((?i)src|background|href(?-i))\s*=\s*(["\']?)?([\w\.\-_:\/]+.(jpeg|jpg|gif|png|bmp))\2#i', $sContent, $arrMatches)) {
				$arrImagesUrls1 = isset($arrMatches[3])? $arrMatches[3]: array();
			}
			if (preg_match_all('#(?i)url(?-i)\(\s*(["\']?)([\w\.\-_:\/]+.(jpeg|jpg|gif|png|bmp))\1\s*\)#', $sContent, $arrMatches)) {
				$arrImagesUrls2 = isset($arrMatches[2])? $arrMatches[2]: array();
			}

			$arrImagesUrls = array_unique(array_merge($arrImagesUrls1,$arrImagesUrls2));
			for ($iI=0; $iI < count($arrImagesUrls); $iI++) {
				$this->addHTMLImage($arrImagesUrls[$iI]);
			}

			return $sContent;
		}

		public function send() {


			if($this->content!="") {

				$this->arrHeaders["From"] = ($this->from_name?"\"".$this->from_name."\" ":"")."<".$this->from_email.">";
				$this->arrHeaders["X-Mailer"] = "UMI.CMS";
				$this->arrHeaders["Reply-To"] = ($this->from_name?"\"".$this->from_name."\" ":"")."<".$this->from_email.">\n";
				$this->arrHeaders["X-Priority"] = $this->priority."\n";
				//$this->headers.="X-Importance: ".$this->importance."\n";

				$content = $this->parseContent();
				foreach ($this->arrContentImages as $sImagePath => $sRealPath) {
					if (!isset($this->arrAttachmentsImages[$sRealPath])) continue;

					$arrImgInfo = $this->arrAttachmentsImages[$sRealPath];
					$arrSearchReg = array(
									'/(\s)((?i)src|background|href(?-i))\s*=\s*(["\']?)' . preg_quote($sImagePath, '/') . '\3/',
									'/(?i)url(?-i)\(\s*(["\']?)' . preg_quote($sImagePath, '/') . '\1\s*\)/'
									);
					$arrReplace = array(
									'\1\2=\3cid:' . $arrImgInfo['cid'] .'\3',
									'url(\1cid:' . $arrImgInfo['cid'] . '\2)'
									);
					$content = preg_replace($arrSearchReg, $arrReplace, $content);
				}

				foreach ($this->files as $oFile) {
					$this->addAttachment($oFile->getFilePath());
				}

				$bNeedAttachments = (bool) count($this->arrAttachments);
				$bNeedHtmlImages = (bool) count($this->arrAttachmentsImages);
				$bNeedHtmlBody = (bool) strlen($content);
				$bNeedTxtBody = (bool) strlen($this->sTxtBody);
				$bOnlyTxtBody = !$bNeedHtmlBody && (bool) strlen($content);

				$oMainPart =  new umiMimePart('', array());
				switch (true) {
					case $bOnlyTxtBody && !$bNeedAttachments:
						$oMainPart = $oMainPart->addTextPart($this->sTxtBody);
						break;

					case !$bNeedHtmlBody && !$bNeedTxtBody && $bNeedAttachments:
						$oMainPart = $oMainPart->addMixedPart();
						foreach ($this->arrAttachments as $arrAtthInfo) {
							$oMainPart->addAttachmentPart($arrAtthInfo);
						}
						break;

					case $bOnlyTxtBody && $bNeedAttachments:
						$oMainPart = $oMainPart->addMixedPart();
						$oMainPart->addTextPart($this->sTxtBody);
						foreach ($this->arrAttachments as $arrAtthInfo) {
							$oMainPart->addAttachmentPart($arrAtthInfo);
						}
						break;

					case $bNeedHtmlBody && !$bNeedHtmlImages && !$bNeedAttachments:
						$oMainPart = $oMainPart->addMixedPart();
						if ($bNeedTxtBody) {
							$oAlternativePart = $oMainPart->addAlternativePart();
							$oAlternativePart->addTextPart($this->sTxtBody);
							$oAlternativePart->addHtmlPart($content);
						} else {
							$oMainPart = $oMainPart->addHtmlPart($content);
						}
						break;

					case $bNeedHtmlBody && $bNeedHtmlImages && !$bNeedAttachments:
						$oMainPart = $oMainPart->addRelatedPart();
						if ($bNeedTxtBody) {
							$oAlternativePart = $oMainPart->addAlternativePart();
							$oAlternativePart->addTextPart($this->sTxtBody);
							$oAlternativePart->addHtmlPart($content);
						} else {
							$oMainPart->addHtmlPart($content);
						}
						foreach ($this->arrAttachmentsImages as $arrImgInfo) {
							$oMainPart->addHtmlImagePart($arrImgInfo);
						}
						break;

					case $bNeedHtmlBody && !$bNeedHtmlImages && $bNeedAttachments:
						$oMainPart = $oMainPart->addMixedPart();
						if ($bNeedTxtBody) {
							$oAlternativePart = $oMainPart->addAlternativePart();
							$oAlternativePart->addTextPart($this->sTxtBody);
							$oAlternativePart->addHtmlPart($content);
						} else {
							$oMainPart->addHtmlPart($content);
						}
						foreach ($this->arrAttachments as $arrAtthInfo) {
							$oMainPart->addAttachmentPart($arrAtthInfo);
						}
						break;

					case $bNeedHtmlBody && $bNeedHtmlImages && $bNeedAttachments:
						$oMainPart = $oMainPart->addMixedPart();
						if ($bNeedTxtBody) {
							$oAlternativePart = $oMainPart->addAlternativePart();
							$oAlternativePart->addTextPart($this->sTxtBody);
							$oRelPart = $oAlternativePart->addRelatedPart();
						} else {
							$oRelPart = $oMainPart->addRelatedPart();
						}
						$oRelPart->addHtmlPart($content);
						foreach ($this->arrAttachmentsImages as $arrImgInfo) {
							$oRelPart->addHtmlImagePart($arrImgInfo);
						}
						foreach ($this->arrAttachments as $arrAtthInfo) {
							$oMainPart->addAttachmentPart($arrAtthInfo);
						}
						break;
				}

				$arrEncodedPart = $oMainPart->encodePart();
				$this->mess_body = $arrEncodedPart['body'];

				$arrHeaders = $this->getHeaders($arrEncodedPart['headers'], true);
				$sHeaders = "";

				foreach ($arrHeaders as $sHdrName => $sHdrVal) {
					$sHeaders .= $sHdrName.": ".$sHdrVal . umiMimePart::UMI_MIMEPART_CRLF;
				}

				foreach($this->recipients as $recnt) {
					$sMailTo = strlen(trim($recnt[1]))? ("=?".$this->charset."?q?".umiMimePart::quotedPrintableEncode($recnt[1])."?="." <".$recnt[0].">") : $recnt[0];
					$sSubject = "=?".$this->charset."?q?".umiMimePart::quotedPrintableEncode($this->subject)."?=";
					$bSucc = mail($sMailTo, $sSubject, $this->mess_body, $sHeaders);
				}

				$this->is_sended = true;
			}
			else return false;
		}


		public function attachFile(umiFile $file) {
			if(in_array($file, $this->files) === false) {
				$this->files[] = $file;
				return true;
			}
		}


		public function __destruct() {
			if($this->is_commited && !$this->is_sended) {
				$this->send();
			}
		}


		public static function checkEmail($email) {
			return (bool) eregi("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$", $email);
		}


		protected function quoted_printable_encode($text, $header_charset = 'Windows-1251') {
			$length = strlen($text);

			for($whitespace = "", $line = 0, $encode = "", $index = 0; $index < $length; $index++) {
				$character=substr($text,$index,1);
				$order=Ord($character);
				$encode=0;
				switch($order) {
					case 9:
					case 32:
						if($header_charset == "") {
							$previous_whitespace=$whitespace;
							$whitespace=$character;
							$character= "";
						} else {
							if($order==32) {
								$character= "_";
							} else {
								$encode=1;
							}
						}
						break;

					case 10:
					case 13:
						if($whitespace!= "") {
							if($header_charset == "" && ($line+3) > 75) {
								$encoded.= "=\n";
								$line=0;
							}
						$encoded .= sprintf("=%02X", ord($whitespace));
						$line += 3;
						$whitespace = "";
					}

					$encoded .= $character;
					$line = 0;
					continue 2;

					default:
						if($order > 127 || $order < 32 || $character == "=" || ($header_charset != "" && ($character == "?" || $character == "_" || $character == "(" || $character == ")"))) {
							$encode=1;
						}
						break;
				}


				if($whitespace != "") {
					if($header_charset == "" && ($line+1) > 75) {
						$encoded .= "=\n";
						$line = 0;
					}

					$encoded .= $whitespace;
					$line++;
					$whitespace = "";
				}

				if($character != "") {
					if($encode) {
						$character = sprintf("=%02X", $order);
						$encoded_length = 3;
					} else {
						$encoded_length = 1;
					}

					if($header_charset == "" && ($line + $encoded_length) > 75) {
						$encoded .= "=\n";
						$line = 0;
					}

					$encoded .= $character;
					$line += $encoded_length;
				}
			}

			if($whitespace != "") {
				if($header_charset == "" && ($line+3) > 75) {
					$encoded .= "=\n";
					$encoded .= sprintf("=%02X", ord($whitespace));
				}

				if($header_charset != "" && $text != $encoded) {
					  return( "=?$header_charset?q?$encoded?=");
				} else {
					return($encoded);
				}
			}
		}
	};

?>