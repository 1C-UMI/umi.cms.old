<?xml version="1.0"?>

<!DOCTYPE HTML [
<!ENTITY nbsp "&#160;">


<!ATTLIST table
	height CDATA #IMPLIED
>
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="menu">
	<xsl:for-each select="//umicms/menu/item">
		<xsl:if test="@active = 'yes'">
			<tr>
				<td><img height="24" src="/images/cms/admin/lite/menu_leftpad.gif" width="4" /></td>
				<td width="100%" background="/images/cms/admin/lite/menu_bg_sel.gif" style="background-position: center" >
					&nbsp;<a class="MenuSel">
					<xsl:attribute name="href">
						<xsl:value-of select="@link"/>
					</xsl:attribute>
					<!--       <b>umi</b>.-->
					<xsl:value-of select="."/>
					</a>
				</td>
				<td background="/images/cms/admin/lite/menu_bg_sel.gif" style="background-position: center; padding-right: 5px;" valign="middle">
					<xsl:if test="@settings = 'yes'">
						<a href="#" style="color: red;">
							<xsl:attribute name="href">
								<xsl:value-of select="@settings_link"/>
							</xsl:attribute>
							cfg
						</a>
					</xsl:if>
				</td>
			</tr>
			<tr>
				<td bgColor="#e5e5e5" colSpan="4" height="1"></td>
			</tr>
		</xsl:if>
		<xsl:if test="@active = 'no'">
			<tr>
				<td><img height="24" src="/images/cms/admin/lite/menu_leftpad.gif" width="4" /></td>
				<td width="100%" background="/images/cms/admin/lite/menu_bg.gif" style="background-position: center">
					&nbsp;<a class="Menu">
						<xsl:attribute name="href">
							<xsl:value-of select="@link"/>
						</xsl:attribute>
						<!--<b>umi</b>.--><xsl:value-of select="."/>
					</a>
				</td>
				<td width="21" background="/images/cms/admin/lite/menu_bg.gif" valign="middle" style="padding-right: 5px; background-position: center">
					<xsl:if test="@settings = 'yes'">
						<a href="#" style="color: red;">
							<xsl:attribute name="href">
								<xsl:value-of select="@settings_link"/>
							</xsl:attribute>
							cfg
						</a>
					</xsl:if>
				</td>
			</tr>
			<tr>
				<td bgColor="#e5e5e5" colSpan="4" height="1"></td>
			</tr>
		</xsl:if>
	</xsl:for-each>
</xsl:template>


<xsl:template match="navibar">
	<xsl:for-each select="//umicms/navibar/item">
		<xsl:if test="@last = 'no'">
			<a href='#' class="MainPage" style="color: #7E7E7E">
				<xsl:attribute name="href">
					<xsl:value-of select="@link" />
				</xsl:attribute>
				<u><xsl:value-of select="." /></u>
			</a> /
		</xsl:if>
		<xsl:if test="@last = 'yes'">
			<xsl:attribute name="href">
				<xsl:value-of select="@link" />
			</xsl:attribute>
			<xsl:value-of select="." />
		</xsl:if>
	</xsl:for-each>
</xsl:template>


<!-- =================== mainpage =================== -->
<xsl:template match="mainpage">
	<xsl:apply-templates select="para" />
</xsl:template>

<xsl:template match="para">
	<xsl:apply-templates select="module" />
</xsl:template>

<xsl:template match="module">
	<div class="home_mod">
		<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="home_modi" rowspan="2">
					<a>
						<xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute>
						<!--img border="0">
							<xsl:attribute name="src">/images/cms/admin/full/ico_b/<xsl:value-of select="@ico"/></xsl:attribute>
						</img-->
					</a>
				</td>
				<td class="home_modh">
					<a class="home_modh">
						<xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute>
						<xsl:value-of select="@title" />
					</a>
				</td>
			</tr>
			<tr>
				<td>
					<xsl:apply-templates select="description" />
				</td>
			</tr>
		</table>
	</div>
</xsl:template>


<xsl:template match="description">
	<h2><xsl:value-of select="@title" /></h2>
	<xsl:apply-templates />
</xsl:template>
<!-- =================== end mainpage =================== -->


<xsl:template match="imenu">
	<table class="tabgroup" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tabgroup_line" nowrap="nowrap">
				<div class="tab_b"></div>
					<xsl:apply-templates select="item" />
				<div class="spacer"></div>
			</td>
		</tr>
		<tr>
			<td class="tabgroup">
				<xsl:apply-templates select="all" />
			</td>
		</tr>
	</table>
</xsl:template>


<xsl:template match="//umicms/content/imenu/item">
	<xsl:choose>
		<xsl:when test="@status = 'active'">
			<div class="tab_item_s_a"></div>
			<div class="tab_item_a"><b><xsl:value-of select="." /></b></div>
		</xsl:when>
		<xsl:when test="@status = 'sub_active'">
			<div class="tab_item_s_a"></div>
			<div class="tab_item_a"><a class="imenu"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><b><xsl:value-of select="." /></b></a></div>
		</xsl:when>
		<xsl:otherwise>
			<div class="tab_item_s"></div>
			<div class="tab_item"><a class="imenu"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><b><xsl:value-of select="." /></b></a></div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>


<xsl:template match="tablegroup">
	<table class="set_table autowidth" cellspacing="1" cellpadding="0" border="0">
		<xsl:attribute name="style"><xsl:value-of select="@style"/></xsl:attribute>
		<xsl:apply-templates />
	</table>
</xsl:template>


<xsl:template match="header">
	<tr>
		<xsl:apply-templates />
	</tr>
</xsl:template>


<xsl:template match="hcol">
	<td class="set_tdh" nowrap="nowrap">
		<xsl:attribute name="align"><xsl:value-of select="@align"/></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style"/></xsl:attribute>
		<xsl:attribute name="colspan"><xsl:value-of select="@colspan"/></xsl:attribute>
		<xsl:apply-templates />
		<xsl:if test=". = ''">&nbsp;</xsl:if>
	</td>
</xsl:template>


<xsl:template match="col">
	<td class="set_td">
		<xsl:attribute name="align"><xsl:value-of select="@align"/></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style"/></xsl:attribute>
		<xsl:attribute name="colspan"><xsl:value-of select="@colspan" /></xsl:attribute>
		<xsl:attribute name="rowspan"><xsl:value-of select="@rowspan" /></xsl:attribute>
		<xsl:apply-templates />
		<span style="font-size: 0px;">&nbsp;</span>
	</td>
</xsl:template>


<xsl:template match="hrow | row">
	<tr>
		<xsl:apply-templates />
	</tr>
</xsl:template>


<xsl:template match="numgroup">
	<div class="numgroup">
		<xsl:apply-templates />
	</div>
</xsl:template>


<xsl:template match="goto">
	<xsl:if test="not(@active = 'yes')">
		<div class="numi"><a class="num"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a></div>
	</xsl:if>
	<xsl:if test="@active = 'yes'">
		<div class="numi_a"><span class="num_a"><xsl:value-of select="." /></span></div>
	</xsl:if>
</xsl:template>


<xsl:template match="tobegin | toend | prev | next">
	<xsl:if test="not(@active = 'yes')">
		<div class="numi"><span class="num_a"><xsl:value-of select="." /></span></div>
	</xsl:if>
	<xsl:if test="@active = 'yes'">
		<div class="numi"><a class="num"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a></div>
	</xsl:if>
</xsl:template>

<!--xsl:template match="sitetree">

<table width="100%" cellpadding="0" cellspacing="0">
 <tr>
  <td width='16' style="line-height: 7px;"><a href='#'>
<xsl:attribute name="onclick">sitetree_switch('<xsl:value-of select="@domain" />');</xsl:attribute>
<img src='/images/cms/admin/lite/minus.gif' width='16' height='16' alt='' border='0'>
<xsl:attribute name="id">p<xsl:value-of select="@domain" />m</xsl:attribute>
</img>
</a>
</td>
  <td style="line-height: 5px;">

<table width="90%" cellspacing="0" cellpadding="0" style="line-height: 7px; height: 12px; background-color: #F8F8F8;">
<tr><td>

&nbsp;<a style='font-weight: bold;  color: #48B30B;'><xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_to_add" /></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_to_add" /></xsl:attribute><xsl:attribute name='href'><xsl:value-of select="@pre_lang" />/admin/content/add_page/?target_domain=<xsl:value-of select="@domain" /></xsl:attribute><xsl:value-of select="@domain" /></a>
</td>

  <td style="width: 15px; line-height: 7px;"><a class="glink">

	<xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/add_page/?parent=<xsl:value-of select="@id" /></xsl:attribute>
	<xsl:value-of select="//umicms/phrases/core_to_add" />
	</a></td>

  <td style="width: 0px;"></td>

</tr>
</table>



</td>
 </tr>

 <tr>
  <td style="vertical-align: top;"><img src="/images/cms/admin/lite/tree_ugol.gif" alt="" width="16" height="21">
<xsl:attribute name="id">il<xsl:value-of select="@domain" />li</xsl:attribute>
</img></td>
  <td>

 <table width="90%" cellpadding="0" cellspacing="0">
<xsl:attribute name='id'>s<xsl:value-of select="@domain" />s</xsl:attribute>
<xsl:attribute name='name'>s<xsl:value-of select="@domain" />s</xsl:attribute>

  <xsl:apply-templates />

 </table>

<script type="text/javascript">
	if(findUCookie("<xsl:value-of select="@domain" />_page") != 1) {
		sitetree_switch('<xsl:value-of select="@domain" />', "ONLOAD");
	}
</script>
  </td>
 </tr>
</table>

</xsl:template>

<xsl:template match="page">

 <xsl:if test="@place = 'first' or @place = 'first_n_last'">
 <tr>
  <td height="5"></td>
  <td></td>
 </tr>
 </xsl:if>

 <tr>

  <xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>

  <td height="16" width="16">

   <xsl:if test="not(@place = 'last') and not(@place = 'first_n_last')">
    <xsl:attribute name="style">background: url('/images/cms/admin/lite/tree_vline.gif')</xsl:attribute>
   </xsl:if>



   <xsl:if test="@children = 'ya'">
   <a>
    <xsl:attribute name="href">#line<xsl:value-of select="@id" /></xsl:attribute>
    <xsl:attribute name="onclick">javascript: sitetree_switch(<xsl:value-of select="@id" />)</xsl:attribute>
    <img src="/images/cms/admin/lite/plus.gif" width="16" height="16" alt="" border="0">
     <xsl:attribute name="id">p<xsl:value-of select="@id" />m</xsl:attribute>
    </img></a>
<a><xsl:attribute name="name">line<xsl:value-of select="@id" /></xsl:attribute></a>

<script type="text/javascript">
	hide_ugol(<xsl:value-of select="@id" />);
	if(findUCookie(<xsl:value-of select="@id" /> + "_page") == 1) {
		to_open[to_open.length] = <xsl:value-of select="@id" />;
	}
</script>

   </xsl:if>

   <xsl:if test="not(@children = 'ya')">
    <img src="/images/cms/admin/lite/minus_gray.gif" width="16" height="16" alt="" border="0" />
   </xsl:if>
</td>

  <td><table width="100%" cellspacing="0" cellpadding="0">
          <xsl:attribute name="bgcolor">#<xsl:value-of select="@b" /></xsl:attribute>
   <tr><td class="sitetree" valign="middle"><a><xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_to_edit" /></xsl:attribute>
	<xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_to_edit" /></xsl:attribute><xsl:attribute name="href">edit_page/<xsl:value-of select="@id" />/</xsl:attribute><xsl:value-of select="@title" /></a></td>

   <td align="right" valign="center">

<table cellspacing="0" cellpadding="0">
 <tr>
  <td valign="center" style="line-height: 8px; height: 12px; width: 30px; text-align: right;">
<xsl:if test="not(@place = 'first') and not(@place = 'first_n_last')">
<a class="glink">
 <xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/?pid=<xsl:value-of select="@id" />&amp;direction=up</xsl:attribute>
	<xsl:value-of select="//umicms/phrases/core_move_up" />
</a>
</xsl:if>

<xsl:if test="(@place = 'first') or (@place = 'first_n_last')">
<span class="tDisabled"><xsl:value-of select="//umicms/phrases/core_move_up" /></span>
</xsl:if>


</td>
  <td style="line-height: 8px; height: 12px;" width="1"></td>
  <td style="line-height: 8px; height: 12px; width: 30px; text-align: right;">
<xsl:if test="not(@place = 'last') and not(@place = 'first_n_last')">
<a class="glink">
 <xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/?pid=<xsl:value-of select="@id" />&amp;direction=down</xsl:attribute>
	<xsl:value-of select="//umicms/phrases/core_move_down" />
</a>
</xsl:if>


<xsl:if test="(@place = 'last') or (@place = 'first_n_last')">
<span class="tDisabled"><xsl:value-of select="//umicms/phrases/core_move_down" /></span>
</xsl:if>
</td>
  <td width="7" style="line-height: 5px;"></td>
  <td style="line-height: 8px; height: 12px; width: 65px; text-align: right;"><a class="glink">

	<xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/add_page/?parent=<xsl:value-of select="@id" /></xsl:attribute>

	<xsl:value-of select="//umicms/phrases/core_to_add" />

	</a></td>
  <td width="1"></td>
  <td style="line-height: 8px; height: 12px; width: 95px; text-align: right;"><a class="glink">

	<xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/edit_page/<xsl:value-of select="@id" />/</xsl:attribute>

	<xsl:value-of select="//umicms/phrases/core_to_edit" />

    </a></td>
  <td width="1" style="line-height: 8px; height: 12px;"></td>
  <td style="line-height: 8px; height: 12px; width: 60px; text-align: right;"><a class="glink">
	<xsl:attribute name="onclick">javascript: return if_sured('<xsl:value-of select="//umicms/phrases/core_are_sured" />');</xsl:attribute>
	<xsl:attribute name="href"><xsl:value-of select="@pre_lang" />/admin/content/del_page/?pid=<xsl:value-of select="@id" /></xsl:attribute>
	<xsl:value-of select="//umicms/phrases/core_to_delete" />
	</a></td>
 </tr>
</table>




   </td>

   </tr>
  </table></td>
 </tr>

 <tr>


  <td height="10" valign="top" style="line-height: 5px;">
   <xsl:if test="not(@place = 'last') and not(@place = 'first_n_last')">
    <xsl:attribute name="style">background: url('/images/cms/admin/lite/tree_vline.gif')</xsl:attribute>
   </xsl:if>

   <xsl:if test="@children = 'ya' and (not(@place = 'last') and not(@place = 'first_n_last'))">

    <img src="/images/cms/admin/lite/tree_krestic.gif" width="16" height="21" alt="" style="display: none">
     <xsl:attribute name="id">i<xsl:value-of select="@id" />i</xsl:attribute>
    </img>
   </xsl:if>

   <xsl:if test="@children = 'ya' and ((@place = 'last') or (@place = 'first_n_last'))">
    <img src="/images/cms/admin/lite/tree_ugol.gif" border="0" width="16" height="21" style="display: none">
     <xsl:attribute name="id">il<xsl:value-of select="@id" />li</xsl:attribute>
    </img>
   </xsl:if>

  </td>
  <td style="line-height: 5px;">

   <table width="100%" cellspacing="0" cellpadding="0">

  <xsl:if test="@children = 'ya'">
   <xsl:attribute name="style">display: none</xsl:attribute>
  </xsl:if>

   <xsl:attribute name="id">s<xsl:value-of select="@id" />s</xsl:attribute>
    <xsl:apply-templates />
   </table>

  </td>
 </tr>

</xsl:template-->


<!--		SITETREE	-->

<!--xsl:template match="sitetree">
	<div>
		<xsl:attribute name="id">tp_<xsl:value-of select="@domain" /></xsl:attribute>
	</div>
	<script type="text/javascript">
		if(!window.l) {
			var l = new lTree('tp_<xsl:value-of select="@domain" />', '<xsl:value-of select="@domain" />');
			window.l = l;

			window.l.addDomain('<xsl:value-of select='@domain' />');
			window.l.init();
		} else {
			window.l.addDomain('<xsl:value-of select='@domain' />');
		}
	</script>
	<xsl:apply-templates />
</xsl:template-->


<xsl:template match="page">
	<script type="text/javascript">
		window.l.addItem("<xsl:if test="@rel = '0'"><xsl:value-of select="../@domain" /></xsl:if><xsl:if test="not(@rel = '0')"><xsl:value-of select="@rel" /></xsl:if>", "<xsl:value-of select="@title" />", '<xsl:value-of select="@id" />', '', '<xsl:if test="@children = 'ya'">1</xsl:if><xsl:if test="not(@children = 'ya')">0</xsl:if>', '<xsl:value-of select="../@domain" />');
	</script>
	<xsl:apply-templates />
</xsl:template>


<!--		SETGROUP	-->

<xsl:template match="setgroup">
<br />
<table cellspacing="0" cellpadding="0" width="99%" style="border: #C0C0C0 1px solid">
	<tr style="cursor: pointer">
		<xsl:attribute name="onclick">javascript: v_switch(document.getElementById('<xsl:value-of select="@id" />')); return false;</xsl:attribute>
		<td width="88%" height="20" class="sg_title" style="background-image: url('/images/cms/admin/lite/sg_bg.gif')">
			<xsl:value-of select="@name" />
		</td>
		<td style="background-image: url('/images/cms/admin/lite/admin/vgl.gif'); background-position: left top; background-repeat: no-repeat; padding-left: 13px; padding-right: 15px; vertical-align: middle; width: 98px; text-align: right;">
			<table width="90px" cellpadding="0" cellspacing="0">
				<tr>
					<td align="right">
						<nobr>
						<!--    <a href="#" onclick="javscript: v_switch(this); return false;" class="sg_blue">-->
						<a href="#" onclick="javscript: return false;" class="sg_blue">
							<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
							<u><xsl:attribute name="id"><xsl:value-of select="@id" />_vtext</xsl:attribute>
							<xsl:value-of select="//umicms/phrases/core_xopen" /></u>
							<img src="/images/cms/admin/lite/sg_arrow_down.gif" border="0" width="13" height="8">
								<xsl:attribute name="name"><xsl:value-of select="@id" />_img</xsl:attribute>
							</img>
						</a>
						</nobr>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<table cellspacing="0" cellpadding="5" width="100%">
				<xsl:attribute name="id"><xsl:value-of select="@id" />_table</xsl:attribute>
				<tr>
					<td height="1" bgcolor="#C0C0C0" style="padding: 0px;"></td>
				</tr>
				<tr>
					<xsl:if test="@form = 'yes'">
						<td class="ntext">
							<form method="post" enctype="multipart/form-data">
								<xsl:attribute name="name"><xsl:value-of select="@id" /></xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
								<xsl:attribute name="enctype"><xsl:value-of select="@enctype" /></xsl:attribute>

								<xsl:apply-templates />
							</form>
						</td>
					</xsl:if>
					<xsl:if test="@form = 'no'">
						<td class="ntext">
							<xsl:apply-templates />
						</td>
					</xsl:if>
				</tr>
			</table>
		</td>
	</tr>
</table>
<script>
	v_onload('<xsl:value-of select="@id" />');
</script>
<br />
</xsl:template>

<xsl:template match="setgroup" mode="anchors">
	<a href="#{@id}" onclick="checkVisible('{@id}');" onmouseover="document.getElementById('anchors').style.display = 'block'" onmouseout="document.getElementById('anchors').style.display = 'block'"><xsl:value-of select="@name"/></a>
</xsl:template>


<xsl:template match="warning">
	<font color="red"><b>Warning:</b>&nbsp;<xsl:apply-templates /></font>
</xsl:template>


<xsl:template match="notice">
	<span style="color: #AAAA00"><b>Notice:</b>&nbsp;<xsl:apply-templates /></span>
</xsl:template>


<xsl:template match="tinytable">
	<table class="autowidth" style="border: 0px; background-color: #D1D1D1; height: 40px;" cellspacing="1">
		<tr>
			<xsl:apply-templates />
		</tr>
	</table>
</xsl:template>


<xsl:template match="tinytable/col">
	<td style="background-color: #FFF; padding: 10px; font-size: 11px; font-family: Tahoma; ">
		<xsl:attribute name="align"><xsl:value-of select="@align" /></xsl:attribute>
		<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
		<xsl:apply-templates />
	</td>
</xsl:template>


<!--xsl:template match="goto">
 <td style="border-left: #A7A7A7 0.5pt solid; width: 38px; text-align: center;">
  <xsl:if test="not(@active = 'yes')">
   <a href="#" class="glink"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
  </xsl:if>

  <xsl:if test="@active = 'yes'">
   <span style="color: #FC6C14; font-weight: bold; font-size: 11px; font-family: Tahoma;"><xsl:value-of select="." /></span>
  </xsl:if>
 </td>
</xsl:template>

<xsl:template match="tobegin">
 <td style="border-left: #A7A7A7 0.5pt solid; width: 38px; text-align: center;">
 <xsl:if test="@active = ''">
  <span style="color: #999; font-size: 15px; font-family: Tahoma; font-weight: bold;"><xsl:value-of select="." /></span>
 </xsl:if>

 <xsl:if test="not(@active = '')">
  <a href='#'  style="color: #008000; font-size: 15px; font-family: Tahoma; font-weight: bold; text-decoration: none;"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
 </xsl:if>

 </td>
</xsl:template>

<xsl:template match="toend">
 <td style="border-right: #A7A7A7 0.5pt solid; border-left: #A7A7A7 0.5pt solid; width: 38px; text-align: center;">
 <xsl:if test="@active = ''">
  <span style="color: #999; font-size: 15px; font-family: Tahoma; font-weight: bold;"><xsl:value-of select="." /></span>
 </xsl:if>

 <xsl:if test="not(@active = '')">
  <a href='#'  style="color: #008000; font-size: 15px; font-family: Tahoma; font-weight: bold; text-decoration: none;"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
 </xsl:if>

 </td>
</xsl:template>

<xsl:template match="prev">
 <td style="border-left: #A7A7A7 0.5pt solid; width: 38px; text-align: center;">
 <xsl:if test="@active = ''">
  <span style="color: #999; font-size: 9px; font-family: Tahoma; font-weight: bold;"><xsl:value-of select="." /></span>
 </xsl:if>

 <xsl:if test="not(@active = '')">
  <a href='#'  style="color: #008000; font-size: 9px; font-family: Tahoma; font-weight: bold; font-weight: bold; text-decoration: none;"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
 </xsl:if>
 </td>
</xsl:template>

<xsl:template match="next">
 <td style="border-left: #A7A7A7 0.5pt solid; width: 38px; text-align: center;">
 <xsl:if test="@active = ''">
  <span style="color: #999; font-size: 9px; font-family: Tahoma; font-weight: bold;"><xsl:value-of select="." /></span>
 </xsl:if>

 <xsl:if test="not(@active = '')">
  <a href='#'  style="color: #008000; font-size: 9px; font-family: Tahoma; font-weight: bold; font-weight: bold; text-decoration: none;"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
 </xsl:if>
 </td>
</xsl:template-->


<xsl:template match="middeled">
	<table border="0">
		<tr>
			<xsl:apply-templates />
		</tr>
	</table>
</xsl:template>


<xsl:template match="mcol">
	<td>
		<xsl:attribute name="style">vertical-align: middle;</xsl:attribute>
		<xsl:if test="not(@style = '')"><xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute></xsl:if>
		<xsl:if test="not(@width = '')"><xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute></xsl:if>

		<xsl:apply-templates />
	</td>
</xsl:template>


<xsl:template match="imgButton">
	<div class="imgBtn">
		<xsl:if test="onclick">
			<xsl:attribute name="onclick"><xsl:value-of select="onclick"/></xsl:attribute>
		</xsl:if>
		<a href="{link}"><xsl:value-of select="title" /></a>
	</div>
</xsl:template>


<!--xsl:template match="simenu">
   <xsl:apply-templates select="./item" /><br />
</xsl:template>


<xsl:template match="//umicms/content/simenu/item">
   <a class="imenu">
    <xsl:attribute name="href">
     <xsl:value-of select="@link" />
    </xsl:attribute>
    <xsl:value-of select="." />
   </a>
</xsl:template-->

</xsl:stylesheet>
