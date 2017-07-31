<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY middot "&#183;">
	<!ENTITY reg "&#174;">
	<!ENTITY copy "&#169;">
	<!ENTITY raquo "&#187;">
	<!ENTITY laquo "&#171;">
	<!ATTLIST table
		height CDATA #IMPLIED
	>
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output encoding="windows-1251" />

<xsl:variable name="help-ticket" select="/umicms/help/ticket" />


<xsl:template match="/">
<html xsl:version="1.0"
      xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
      lang="ru">
	<head>

		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>

		<style type="text/css">
			@import "/styles/xsl_lite/umicms.css";
			@import "/htmlarea/htmlarea.css";

			@import "/styles/xsl_lite/lTree.css";
			@import "/styles/xsl_lite/css/contentTree.css";
			@import "/styles/xsl_lite/css/symlinkInput.css";
			@import "/styles/xsl_lite/css/umiPopup.css";
		</style>

		<script type="text/javascript" src="/styles/xsl_lite/scripts.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/events.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/dataModuleControls.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/contentTree.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/contentTreeDomain.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/contentTreePage.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/symlinkInput.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/umiPopup.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/cifi.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/js/multipleGuideInput.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/llib.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_lite/lTree.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/multiupload.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/client/cookie.js" charset="utf-8"></script>
		
		<script type="text/javascript">
			<![CDATA[
				_editor_url = "/htmlarea/";
				_editor_lang = "en";
				addOnLoadEvent(pre_is_initEditor);
				addOnLoadEvent(autoSize);
				addOnLoadEvent(initAll);
				addOnLoadEvent(acf_onload);
				addOnLoadEvent(f4u);
			]]>
		</script>

		<script type="text/javascript" src="/js/s4html.php" charset="utf-8"></script>
		<script type="text/javascript" src="/htmlarea/htmlarea.js" charset="utf-8"></script>

<script type="text/javascript">
function f4u() {
}
function v_switch(b_id) {

	lik = b_id;

	if(b_id.id)
		b_id = b_id.id

	b_id = b_id.replace("\n", "");

	tname = b_id + "_table";

	t_obj = document.getElementById(tname);
	v_obj = document.getElementById(b_id + "_vtext");

	if(!t_obj)
		return false;

	if(t_obj.style.display != "none")
		disp = "none";
	else
		disp = "";

	t_obj.style.display = disp;

	if(disp == "none") {
		document.images[b_id + "_img"].src = "/images/cms/admin/lite/sg_arrow_up.gif";
		v_obj.innerHTML = "<xsl:value-of select="//umicms/phrases/core_xclose" />";
	}
	else {
		document.images[b_id + "_img"].src = "/images/cms/admin/lite/sg_arrow_down.gif";
		v_obj.innerHTML= "<xsl:value-of select="//umicms/phrases/core_xopen" />";
	}

	addUCookie(tname, disp, 365, "setgroups");
}
</script>

<script type="text/javascript">
<![CDATA[


function pre_is_initEditor(editor_id) {
if(!editor_id)
		editor_id = "content";
		
	area_obj = document.getElementById(editor_id);
	if(area_obj)
//initDocument();
		is_initEditor();
}

]]>
</script>





		<link href="/styles/xsl_lite/umicms.css" type="text/css" rel="stylesheet" />
		<link href="/htmlarea/htmlarea.css" type="text/css" rel="stylesheet" />
		<title><xsl:value-of select="//title" /></title>
	</head>
	<body bgcolor="#ffffff" style="background-image: url('/images/cms/admin/lite/body_bg.gif'); background-repeat: repeat-y; background-position: left; MARGIN: 0px" onload="javascript: runOnLoadEvents(); helpViewerState();">
		<div style="position: absolute; width: 100%; text-align: right">
			<div style="margin: 3px 20px; text-align: right">
				<xsl:if test="/umicms/versionLine = 'demo' or 'free'">
					<a class="lang" href="http://www.umi-cms.ru/purchase/how-to-buy/">купить UMI.CMS</a>
				</xsl:if>
			</div>
		</div>
		<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td colSpan="5">
					<table cellspacing="0" cellpadding="0" width="100%" border="0">
						<tr bgcolor="#f7f7f7">
							<td rowspan="2">
								<img src="/images/cms/admin/lite/logo_umicms_lite.jpg" style="margin-top: 3px; margin-left: 15px;" />
								<!--
								<img src="/images/cms/admin/lite/admin_logo_text.gif" alt="UMI.CMS 2.0" style="margin-top: 10px; margin-left: 15px;" />
								-->
							</td>
							<td rowspan="2"></td>
							<td valign="center" align="right" width="100%" bgcolor="#f7f7f7" style="padding-top: 10px">
								<a class="glink" href="http://help.umi-cms.ru/"><b>Помощь</b></a><xsl:text> | </xsl:text>
								<a href="/admin/" class="glink"><b>Главная</b></a>&nbsp;|&nbsp;
								<a href="/users/logout/" class="glink"><b>Выход</b></a>
							</td>
							<td bgcolor="#f7f7f7" style="height: 81px; width: 20px;" rowspan="2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						</tr>
						<tr>
							<td style="text-align: right" height="10" bgcolor="#f7f7f7">
								<xsl:value-of select="//phrases/core_langs"/>: 
								<xsl:apply-templates select="//umicms/langs" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="5" height="3" style="background-image: url('/images/cms/admin/lite/gray_line.gif')"></td>
			</tr>
			<tr>
				<td colspan="5" height="10" style="background-image: url('/images/cms/admin/lite/top_line.gif')"></td>
			</tr>
			<tr>
				<td colspan="5" height="3" style="background-image: url('/images/cms/admin/lite/gray_line.gif')"></td>
			</tr>
			<tr>
				<td colSpan="2" style="background-image: url('/images/cms/admin/lite/gray_line.gif')" height="1"></td>
				<td style="background-image: url('/images/cms/admin/lite/gray_line_crotch.gif')"></td>
				<td colSpan="2" style="background-image: url('/images/cms/admin/lite/gray_line.gif')"></td>
			</tr>
			<tr>
			<xsl:choose>
				<xsl:when test="/umicms/login">
					<td align="center" style="width: 100%; height: 100%">
						<xsl:apply-templates select="//umicms/login"/>
					</td>
				</xsl:when>
				<xsl:when test="/umicms/content">
					<td height="100%" style="background-image: url('/images/cms/admin/lite/left_column.gif')" width="13px">
						<table border="0" cellspacing="0" cellpadding="0" width="13">
							<tr>
								<td></td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<table height="100%" cellspacing="0" cellpadding="0" width="170" border="0">
							<xsl:apply-templates select="//umicms/menu"/>
							<tr>
								<td colspan="4" height="100%"></td>
							</tr>
						</table>
					</td>
					<td bgColor="#d6d6d6" height="100%"><img height="1" src="/images/cms/admin/lite/spacer.gif" width="1"/></td>
					<td><img height="3" src="/images/cms/admin/lite/spacer.gif" width="13"/></td>
					<td width="100%" height="100%" valign="top">
						<table height="100%" cellspacing="0" cellpadding="10" width="100%" border="0">
							<tr>
								<td>
									<table height="100%" cellspacing="0" cellpadding="0" width="100%" align="left" border="0">
										<tr>
											<td style="width: 5px; height: 18px;background-color: #FF6500;">&nbsp;</td>
											<td width="100%" style="color: #7E7E7E">
												<div id="navibar_b">
													<xsl:apply-templates select="//umicms/navibar" />
												</div>
												<div id="gotoSite">
													<a>
														<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/admin/data/trash/</xsl:attribute>
														<xsl:value-of select="//umicms/phrases/core_jump_to_trash" />
													</a>
													&nbsp;&nbsp;|&nbsp;&nbsp;
													<a>
														<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/</xsl:attribute>
														<xsl:value-of select="//umicms/phrases/core_jump_to_site" />
													</a>
												</div>
											</td>
											<td>&nbsp;</td>
										</tr>
										<tr>
											<td colSpan="3"><h1><xsl:value-of select="//umicms/header" /></h1>
												<xsl:if test="$help-ticket != ''">
													<a onclick="helpViewerSwitch(this); return false;" style="float: right; margin-top: -25px; cursor: pointer" id="helpViewerStateLink">Справка</a>
													<div id="info-block" style="margin: -11px 0 15px; padding: 10px; border: #d3d3d3 1px solid; background: #f7f7f7">
															<xsl:copy-of select="document($help-ticket)//body/*"/>
													</div>
												</xsl:if>
											</td>
										</tr>
										<tr>
											<td vAlign="top" colSpan="3" height="100%" class="content">
												<xsl:if test="//setgroup">
													<div style="float: right; width: 240px">
														<div id="block-links">
															<xsl:if test="not(//imenu)">
																<xsl:attribute name="style">margin-top: -11px</xsl:attribute>
															</xsl:if>
															<a style="margin: 8px 10px 0; position: absolute;" onmouseover="document.getElementById('anchors').style.display = 'block'">Переход по блокам на этой странице</a>
															<div id="anchors" onmouseover="document.getElementById('anchors').style.display = 'block'" onmouseout="document.getElementById('anchors').style.display = 'none'">
																<xsl:apply-templates select="//setgroup" mode="anchors" />
															</div>
														</div>
													</div>
												</xsl:if>
												<xsl:apply-templates select="//umicms/content" />
											</td>
										</tr>
										<tr>
											<td colSpan="3" height="100%"><img height="100%" src="/images/cms/admin/lite/aspacer.gif" /></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</xsl:when>
			</xsl:choose>
			</tr>
			<tr>
				<td colspan="5">
					<div id="footer">
						<div id="copy">&#169; 2003-2007 <a href="http://www.umi-cms.ru/" target="_blank" class="l u">umi-cms.ru</a></div>
						<div id="support">
							<xsl:value-of select="//umicms/phrases/core_tech_support" />: 
							<a href="http://www.umi-cms.ru/support/" class="l u">umi-cms.ru/support/</a>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<div id="sambo" style="position: absolute"></div>
	</body>
</html>
</xsl:template>

<xsl:template match="login">
	<div id="login">
		<h2>Авторизация</h2>
		<xsl:apply-templates/>
	</div>
</xsl:template>

</xsl:stylesheet>
