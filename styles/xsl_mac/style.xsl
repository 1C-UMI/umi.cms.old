<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
	<!ENTITY copy   "&#169;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:output encoding="utf-8" />

<xsl:include href="/styles/interface.xsl"/>
<xsl:include href="/styles/common.xsl"/>
<xsl:include href="/styles/xsl_mac/interface.xsl"/>
<xsl:include href="/styles/xsl_mac/html.xsl"/>
<xsl:include href="/styles/xsl_mac/forms.xsl"/>
<xsl:include href="/styles/xsl_mac/xml2js.xsl"/>
<xsl:include href="/styles/xsl_mac/wysiwyg.xsl"/>


<xsl:variable name="num-of-menu" select="ceiling(count(umicms/menu/item) div 2)"/>
<xsl:variable name="help-ticket" select="/umicms/help/ticket" />
<xsl:variable name="favorites" select="document(concat(/umicms/pre_lang, '/admin', '/users/getFavourites/', /umicms/userInfo/id))"/>
<xsl:variable name="menu" select="/umicms/menu"/>

<xsl:template match="umicms">
	<html lang="ru">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=windows-1251"/>

			<style type="text/css">
				@import "/htmlarea/htmlarea.css";
				@import "/styles/xsl_mac/umicms.css";
				@import "/styles/xsl_mac/css/contentTree.css";
				@import "/styles/xsl_mac/css/symlinkInput.css";
				@import "/styles/xsl_mac/css/umiPopup.css";
				@import "/styles/xsl_mac/css/data.css";
			</style>
	
			<script type="text/javascript" src="/styles/xsl_mac/scripts.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/events.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/dataModuleControls.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/contentTree.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/contentTreeDomain.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/contentTreePage.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/symlinkInput.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/umiPopup.js" charset="utf-8"></script>
			<script type="text/javascript" src="/js/cifi.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/multipleGuideInput.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/llib.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/lTree.js" charset="utf-8"></script>
			<script type="text/javascript" src="/js/multiupload.js" charset="utf-8"></script>
			<script type="text/javascript" src="/js/client/cookie.js" charset="utf-8"></script>
			<script type="text/javascript" src="/styles/xsl_mac/js/dockitem.js" charset="utf-8"></script>
			<script>
				window.pre_lang = document.pre_lang = '<xsl:value-of select="/umicms/pre_lang"/>';
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
			<script type="text/javascript">
				<![CDATA[
					function hide() {
						var menu = document.getElementById('drop_down_menu');
						menu.style.display = 'none';
						var oDragHelp = arrDockItems['draghelp'];
						if (oDragHelp) {
							oDragHelp.oDockImg.src = "/images/cms/admin/mac/icons/medium/draghelp_empty.gif";
						}
					}
					function start() {
						var menu = document.getElementById('drop_down_menu');
						if (menu.style.display != 'block') {
							menu.style.display = 'block';
							if (oDragHelp) {
								oDragHelp.oDockImg.src = "/images/cms/admin/mac/icons/medium/draghelp.gif";
							}
						} else {
							hide();
						}
						return false;
					}
					function dockState() {
						var dock = document.getElementById('dock');
						var dockState = getCookie('dock');
						if (dockState == 'hide') {
							dock.style.display = 'none';
							/*if (window.navigator.appName != "Microsoft Internet Explorer") {
								document.getElementById('content').style.marginTop = '23px';
							}*/
							document.getElementById('dock_btn').innerHTML = '<img class="png" src="/images/cms/admin/mac/common/doc_open.png"/>';
						}
					}
					function dockSwitcher() {
						var dockState = getCookie('dock');
						var dock = document.getElementById('dock');
						if (dock.style.display == 'none') {
							dock.style.display = 'block';
							setCookie('dock', 'visible');
							/*if (window.navigator.appName != "Microsoft Internet Explorer") {
								document.getElementById('content').style.marginTop = '90px';
							}*/
							document.getElementById('dock_btn').innerHTML = '<img class="png" src="/images/cms/admin/mac/common/doc_close.png"/>';
						} else {
							dock.style.display = 'none';
							setCookie('dock', 'hide');
							/*if (window.navigator.appName != "Microsoft Internet Explorer") {
								document.getElementById('content').style.marginTop = '23px';
							}*/
							document.getElementById('dock_btn').innerHTML = '<img class="png" src="/images/cms/admin/mac/common/doc_open.png"/>';
						}
					}
				]]>
			</script>
	
			<script type="text/javascript" src="/js/s4html.php" charset="utf-8"></script>
			<script type="text/javascript" src="/htmlarea/htmlarea.js" charset="utf-8"></script>
		</head>
		<title><xsl:value-of select="title"/></title>
		<body onload="javascript: runOnLoadEvents(); dockState(); helpViewerState();">
			<xsl:if test="login">
				<xsl:attribute name="style">background: url(/images/cms/admin/mac/common/bg.jpg) center center</xsl:attribute>
				<xsl:apply-templates select="login"/>
			</xsl:if>
			<xsl:if test="content">
				<table id="container" cellspacing="0px" style="width: 100%; height: 100%">
					<tr><td valign="top" style="background: #fafafa">
					<div id="menu-bar">
						<div id="menu">
							<a href="#" onclick="return start();" onmouseout="document.timeoutId = window.setTimeout('hide()', 2000);" onmouseover="clearTimeout(document.timeoutId);">
								<img class="png" src="/images/cms/admin/mac/common/umi.png" style="margin-top: -1px"/>&nbsp;&nbsp; Модули
							</a>
							<a href="/admin/users/user_edit/{/umicms/userInfo/id}">Профиль</a>
							<a href="/">На сайт</a>
							<a href="/admin/data/trash/"><xsl:value-of select="/umicms/phrases/core_jump_to_trash"/></a>
							<a href="http://help.umi-cms.ru/">Помощь</a>
							<xsl:if test="versionLine = 'demo' or versionLine = 'free'">
								<a href="http://www.umi-cms.ru/purchase/how-to-buy/">Купить <b>UMI.CMS</b></a> 
							</xsl:if>
						</div>
						<div id="lang" style="padding-top: 0px">
							<xsl:apply-templates select="langs"/>
							<a href="/admin/users/logout/">Выход</a>
						</div>
					</div>
					<xsl:apply-templates select="menu"/>
					<div id="dock-container">
						<div id="dock">
					<!--div style="position: absolute; width: 100%; height: 100%; left: 0px; top: 0px; background: #fff; opacity: 0.85; z-index: -1"></div-->
						<span id="dock_help">Можно перенести в эту панель часто используемые модули из меню модулей </span>
						<script type="text/javascript">
							arrDockItems = new Array();
							<xsl:for-each select="$favorites//ditem">
								createDItem(&apos;<xsl:value-of select="@id" />&apos;, &apos;<xsl:value-of select="$menu/item[contains(@ico, current()/@id)]" />&apos;);
							</xsl:for-each>

							arrDockItems[&apos;draghelp&apos;] = new dockItem(&apos;draghelp&apos;);
							var oDragHelp = arrDockItems[&apos;draghelp&apos;];
							if (oDragHelp) {
								oDragHelp.sIcoExt = 'gif';
								oDragHelp.create(null, null, 'Можно перенести в эту панель часто используемые модули из меню модулей', '', true);
								oDragHelp.oDockImg.src = "/images/cms/admin/mac/icons/medium/draghelp_empty.gif";
								oDragHelp.show();
							}

							createDItem(&apos;trash&apos;, &apos;Корзина&apos;, '/admin/data/trash', true);
							if (arrDockItems[&apos;trash&apos;]) {
								arrDockItems[&apos;trash&apos;].isTrash = true;
							}
						</script>
						</div>
						<div style="position: absolute; font-size: 0px; text-align: center; width: 100%; border: #f00 0px solid"><a id="dock_btn" onclick="dockSwitcher();"><img class="png" src="/images/cms/admin/mac/common/doc_close.png"/></a></div>
					</div>
					<div id="content">
						<xsl:if test="menu/item[contains(@link, /umicms/module)]/@settings = 'yes'">
							<div id="settings">
								<a href="{menu/item[contains(@link, /umicms/module)]/@settings_link}"><img class="png" src="/images/cms/admin/mac/icons/settings.png" style="vertical-align: -4px"/>&nbsp;&nbsp;Настройка модуля</a>
							</div>
						</xsl:if>
						<xsl:if test="//setgroup">
							<div style="float: right; clear: both; width: 250px">
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
						<xsl:if test="$help-ticket != ''">
							<a onclick="helpViewerSwitch(this); return false;" style="float: right; clear: both; margin-top: 50px; cursor: pointer">
								<img class="png" src="/images/cms/admin/mac/icons/help_block.png"/>
							</a>
						</xsl:if>
						<div id="content-header">
							<img class="png" src="{concat('/images/cms/admin/mac/icons/big/', module, '.png')}" style="position: absolute; left: 20px"/>
							<div style="margin-left: 110px; padding-top: 55px">
								<xsl:apply-templates select="navibar"/>
								<h2><xsl:value-of select="header"/></h2>
							</div>
						</div>
						
						<table>
							<tr><td valign="top">
									<xsl:if test="$help-ticket != ''">
										<xsl:attribute name="style">width: 75%</xsl:attribute>
									</xsl:if>
									<xsl:apply-templates select="content"/>
							</td>
							<td valign="top">
							<xsl:if test="$help-ticket != ''">
								<div id="info-block" class="panel">
									<div class="header full" style="cursor: default">
										<span><span><div style="padding-left: 10px; font-weight: bold" class="helpViewerStateLink">Справка</div></span></span>
									</div>
									<div class="content" style="border-top: none">
										<xsl:choose>
											<xsl:when test="function-available('document')">
												<xsl:copy-of select="document($help-ticket)//body/*"/>
											</xsl:when>
											<xsl:otherwise>
												not for Opera... yet... (
											</xsl:otherwise>
										</xsl:choose>
									</div>
								</div>
							</xsl:if>
							</td>
							</tr>
						</table>
					</div>
					</td></tr>
					<tr><td style="height: 30px; vertical-align: top; background: #fafafa">
						<a id="copy" href="http://www.umi-cms.ru">&copy; 2003-2007 UMICMS.RU</a>
						<a id="support" href="http://www.umi-cms.ru/support">Техническая поддержка UMICMS.RU</a>
					</td></tr>
				</table>
			</xsl:if>
		</body>
	</html>
</xsl:template>

<xsl:template match="login">
	<table id="login" style="height: 100%">
		<tr><td align="center">
			<table style="width: 414px; text-align: center; border-collapse: collapse">
				<tr><td><img class="png" src="/images/cms/admin/mac/common/login_header.png" /></td></tr>
				<tr>
					<td style="background: #fff" align="center">
						<img class="png" src="/images/cms/admin/mac/icons/medium/auth.png" /> <strong>АВТОРИЗАЦИЯ</strong>
						<br/><br/>
					</td>
				</tr>
				<tr>
					<td style="background: #fff" align="left">
						<div style="margin-left: 125px">
							<xsl:apply-templates select="//warning"/>
						</div>
						<form method='post' action='/admin/users/login_do/'>
							<table cellpadding="2px" style="width: 310px; margin-left: 40px">
								<tr><td align="right" style="height: 30px">Логин </td><td align="right"><xsl:apply-templates select=".//input" mode="login"/></td></tr>
								<tr><td align="right" style="height: 30px">Пароль </td><td align="right"><xsl:apply-templates select=".//password" mode="login"/></td></tr>
								<tr><td align="right" style="height: 30px">Скин </td><td align="right"><xsl:apply-templates select=".//select" mode="login"/></td></tr>
								<tr><td colspan="2" align="right" style="height: 40px"><xsl:apply-templates select=".//submit"/></td></tr>
							</table>
							<xsl:apply-templates select=".//passthru"/>
						</form>
					</td>
				</tr>
				<tr><td><img class="png" style="border-top: #fff 1px solid" src="/images/cms/admin/mac/common/login_footer.png" /></td></tr>
			</table>
		</td></tr>
	</table>
</xsl:template>

<xsl:template match="input" mode="login">
	<input type="text" name="{name}" id="{id}" style="width: 220px"/>
</xsl:template>

<xsl:template match="password" mode="login">
	<input type="password" name="{@name}" id="{@id}" style="width: 220px"/>
</xsl:template>

<xsl:template match="select" mode="login">
	<select type="password" name="{name}" id="{id}">
		<xsl:apply-templates select="item"/>
	</select>
</xsl:template>


</xsl:stylesheet>