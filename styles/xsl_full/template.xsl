<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE HTML [

	<!ENTITY nbsp "&#160;">
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

<xsl:template match="/">
<xsl:variable name="help-ticket" select="/umicms/help/ticket" />
<html xsl:version="1.0"
      xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
      lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>

		<style type="text/css">
			@import "/styles/xsl_full/umicms.css";
			@import "/htmlarea/htmlarea.css";

			@import "/styles/xsl_full/lTree.css";
			@import "/styles/xsl_full/css/contentTree.css";
			@import "/styles/xsl_full/css/symlinkInput.css";
			@import "/styles/xsl_full/css/umiPopup.css";
		</style>

		<script type="text/javascript" src="/styles/xsl_full/scripts.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/events.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/dataModuleControls.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/contentTree.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/contentTreeDomain.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/contentTreePage.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/symlinkInput.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/umiPopup.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/cifi.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/js/multipleGuideInput.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/llib.js" charset="utf-8"></script>
		<script type="text/javascript" src="/styles/xsl_full/lTree.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/multiupload.js" charset="utf-8"></script>
		<script type="text/javascript" src="/js/client/cookie.js" charset="utf-8"></script>

		<script type="text/javascript">
			window.pre_lang = '<xsl:value-of select='//umicms/pre_lang' />';
			<![CDATA[
				_editor_url = "/htmlarea/";
				_editor_lang = "en";
				addOnLoadEvent(pre_is_initEditor);
				addOnLoadEvent(autoSize);
				addOnLoadEvent(initAll);
				addOnLoadEvent(acf_onload);
				addOnLoadEvent(f4u);
				addOnLoadEvent(filterState);
			]]>
		</script>

		<script type="text/javascript" src="/js/s4html.php" charset="utf-8"></script>
		<script type="text/javascript" src="/htmlarea/htmlarea.js" charset="utf-8"></script>
<!--
		<script type="text/javascript" src="/htmlarea/plugins/TableOperations/table-operations.js"></script>
		<script type="text/javascript" src="/htmlarea/plugins/TableOperations/lang/ru.js"></script>
-->

		<title><xsl:value-of select="//title" /></title>
		
	</head>

	<body onload="javascript: runOnLoadEvents(); helpViewerState();">
		
		
		<table cellspacing="0" cellpadding="0" border="0" id="main">
			<tr id="header">
				<td colspan="2" style="height: 63px">
					<img src="/images/cms/admin/full/logo.jpg" class="left" hspace="0">
						<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_umicms" /></xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_umicms" /></xsl:attribute>
					</img>
					<img src="/images/cms/admin/full/logo_sub.jpg" class="left" hspace="0">
						<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_umicms_sub" /></xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_umicms_sub" /></xsl:attribute>
					</img>
					<div style="float: right; margin-top: 10px; margin-right: 5px">
						<a id="support" href="http://www.umi-cms.ru/support/">Техническая поддержка</a>
						<a id="help" href="http://help.umi-cms.ru/">Помощь</a>
						<xsl:if test="/umicms/content and (/umicms/versionLine = 'demo' or /umicms/versionLine = 'free')">
							<a class="lang" href="http://www.umi-cms.ru/purchase/how-to-buy/">купить <b>UMI.CMS</b></a> 
						</xsl:if>
					</div>
					<div style="float: right; clear: right; margin-top: 10px; margin-right: 5px">
						<xsl:if test="/umicms/content">
							<a id="trash">
								<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/admin/data/trash/</xsl:attribute>
								<xsl:value-of select="//umicms/phrases/core_jump_to_trash" />
							</a>
						</xsl:if>
						<a id="to-site">
							<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/</xsl:attribute>
							<xsl:value-of select="//umicms/phrases/core_jump_to_site" />
						</a>		
					</div>
					<xsl:if test="//umicms/content">
						<div id="account"><strong>Вы вошли как: </strong><a href="/admin/users/user_edit/{/umicms/userInfo/id}"><xsl:value-of select="/umicms/userInfo/firstName"/>&nbsp;<xsl:value-of select="/umicms/userInfo/lastName"/></a> 
							<span style="margin-left: 30px">
								<xsl:value-of select="//umicms/phrases/core_langs" />: 
								<xsl:apply-templates select="//umicms/langs" />
							</span>
						</div>
					</xsl:if>
				</td>
			</tr>
			<xsl:if test="not(//umicms/content)">
				<td id="content_d" align="center" valign="center" style="vertical-align: middle;">
					<div id="login_b">
						<div id="login_h1"><xsl:value-of select="//umicms/header" /></div>
						<xsl:apply-templates select="//umicms/login" />
					</div>
				</td>
			</xsl:if>
			<xsl:if test="//umicms/content">
				<tr id="vmenu">
					<td id="mnav" nowrap="nowrap">
						<div id="mnav_b">
							<a class="gray">
								<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/admin/</xsl:attribute>
								<img src="/images/cms/admin/full/ico_home.gif" width="11" height="9">
									<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_exit" /></xsl:attribute>
								</img>&nbsp;&nbsp;
								<xsl:value-of select="//umicms/phrases/core_main" />
							</a>
							<a class="gray of_exit">
								<xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/admin/users/logout/</xsl:attribute>
								<img src="/images/cms/admin/full/ico_exit.gif" width="11" height="9">
									<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_exit" /></xsl:attribute>
								</img>&nbsp;&nbsp;<xsl:value-of select="//umicms/phrases/core_exit" />
							</a>
						</div>				
					</td>
					<td id="navibar">
						<div id="navibar_b">
							<xsl:apply-templates select="//umicms/navibar" />
						</div>
						<!--div id="gotoSite">
							<a><xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/admin/data/trash/</xsl:attribute><xsl:value-of select="//umicms/phrases/core_jump_to_trash" /></a>
							&nbsp;&nbsp;|&nbsp;&nbsp;
							<a><xsl:attribute name="href"><xsl:value-of select="//umicms/pre_lang" />/</xsl:attribute><xsl:value-of select="//umicms/phrases/core_jump_to_site" /></a>
						</div-->
					</td>
				</tr>
				<tr>
					<td id="menu" style="width: 190px">
						<div id="menu_b" style="width: 156px">
							<xsl:apply-templates select="//umicms/menu"/>
						</div>
					</td>
					<td id="content_d" width="100%">
						<div id="content_b">
							
								<!--xsl:if test="not(//umicms/module = '')" >
									<div id="h1_ico">
										<img width="32" height="32">
											<xsl:attribute name="src">/images/cms/admin/full/ico_b/ico_<xsl:value-of select="//umicms/module" />.gif</xsl:attribute>
											<xsl:attribute name="alt"><xsl:value-of select="//umicms/header" /></xsl:attribute>
										</img>
									</div>
								</xsl:if-->
								<h1 style="display: block; margin-bottom: 15px; padding: 10px 0 3px 40px; height: 23px; background: url(/images/cms/admin/full/ico_b/ico_{//umicms/module}.gif) no-repeat; border-bottom: #D3D3D3 1px solid"><xsl:value-of select="//umicms/header" /></h1>

							<xsl:if test="$help-ticket != ''">
								<a onclick="helpViewerSwitch(this); return false;" style="float: right; margin-top: -33px; cursor: pointer" class="helpSwitcher helpSwitcherOn" id="helpViewerStateLink">
									<![CDATA[Справка]]>
								</a>
								<div id="info-block" style="margin: -16px 0 15px; padding: 10px; border: #d3d3d3 1px solid; background: #F0F4F8">
										<xsl:copy-of select="document($help-ticket)//body/*"/>
								</div>
							</xsl:if>

							<xsl:if test="//setgroup">
								<div style="float: right; width: 240px">
									<div id="block-links">
										<xsl:if test="not(//imenu)">
											<xsl:attribute name="style">margin-top: -11px</xsl:attribute>
										</xsl:if>
										<a class="title" onmouseover="document.getElementById('anchors').style.display = 'block'">Переход по блокам на этой странице</a>
										<div id="anchors" onmouseover="document.getElementById('anchors').style.display = 'block'" onmouseout="document.getElementById('anchors').style.display = 'none'">
											<xsl:apply-templates select="//setgroup" mode="anchors" />
										</div>
									</div>
								</div>
							</xsl:if>
							<xsl:apply-templates select="//umicms/content" />
						</div>
					</td>
				</tr>
			</xsl:if>
			<tr id="footer">
				<td colspan="2">
					<div id="copy">&#169; 2003-2007 <a href="http://www.umi-cms.ru/" target="_blank" class="l u">umi-cms.ru</a></div>
					<!--div>
						<xsl:value-of select="//umicms/phrases/core_tech_support" />: 
						<a href="http://www.umi-cms.ru/support/" class="l u">umi-cms.ru/support/</a>
					</div-->
				</td>
			</tr>
		</table>
		<div id="placer"></div>
		<div id="scriptPlacer"></div>
	</body>
</html>
</xsl:template>



</xsl:stylesheet>