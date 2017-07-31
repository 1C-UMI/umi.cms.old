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
			<div class="menu_item">
				<img width="16" height="16" hspace="0" class="left">
					<xsl:attribute name="src">/images/cms/admin/full/ico_s/<xsl:value-of select="@ico" /></xsl:attribute>
				</img><div class="menu_item_b"><a class="menu_a"><xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute><xsl:value-of select="."/></a></div>
				<xsl:if test="@settings = 'yes'">
					<div style="float: right;">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@settings_link"/></xsl:attribute>
							<img src="/images/cms/admin/full/ico_s/ico_config.gif">
								<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_module_config" /></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_module_config" /></xsl:attribute>
							</img>
						</a>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
		<xsl:if test="@active = 'no'">
			<div class="menu_item">
				<img width="16" height="16" hspace="0" class="left">
					<xsl:attribute name="src">/images/cms/admin/full/ico_s/<xsl:value-of select="@ico" /></xsl:attribute>
				</img><div class="menu_item_b"><a class="menu"><xsl:attribute name="href"><xsl:value-of select="@link"/></xsl:attribute><xsl:value-of select="."/></a></div>
				<xsl:if test="@settings = 'yes'">
					<div style="float: right;">
						<a>
							<xsl:attribute name="href"><xsl:value-of select="@settings_link"/></xsl:attribute>
							<img src="/images/cms/admin/full/ico_s/ico_config.gif">
								<xsl:attribute name="alt"><xsl:value-of select="//umicms/phrases/core_module_config" /></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="//umicms/phrases/core_module_config" /></xsl:attribute>
							</img>
						</a>
					</div>
				</xsl:if>
			</div>
		</xsl:if>
	</xsl:for-each>

</xsl:template>


<xsl:template match="navibar">
	<img src="/images/cms/admin/full/arr.gif" width="3" height="8" alt="" />&nbsp;&nbsp;
	<xsl:for-each select="//umicms/navibar/item">
		<xsl:if test="@last = 'no'">
			<a href="#" class="l">
				<xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute>
				<xsl:value-of select="." />
			</a>&nbsp;&nbsp;|&nbsp;&nbsp;
		</xsl:if>
		<xsl:if test="@last = 'yes'">
			<xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute>
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

						<img border="0">
							<xsl:attribute name="src">/images/cms/admin/full/ico_b/<xsl:value-of select="@ico"/></xsl:attribute>
						</img>
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
	<b><xsl:value-of select="@title" /></b><br />
	<xsl:apply-templates /><br />
</xsl:template>
<!-- =================== end mainpage =================== -->


<xsl:template match="imenu">
	<table class="tabgroup" cellspacing="0" cellpadding="0">
		<tr>
			<td class="tabgroup_line" nowrap="nowrap">
				<div class="tab_b"></div>
					<xsl:apply-templates select="./item" />
				<div class="spacer"></div>
			</td>
		</tr>

		<tr>
			<td class="tabgroup">
<!--				content hidden by rule (/styles/xsl_full/interface.xsl)-->
				<xsl:apply-templates select="./all" />
			</td>
		</tr>
	</table>
</xsl:template>


<xsl:template match="//umicms/content/imenu/item">
	<xsl:if test="@status = 'active'">
		<div class="tab_item_s_a"></div>
		<div class="tab_item_a"><b><xsl:value-of select="." /></b></div>
	</xsl:if>

	<xsl:if test="not(@status = 'active') and not(@status = 'sub_active')">
		<div class="tab_item_s"></div>
		<div class="tab_item"><a><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><b><xsl:value-of select="." /></b></a></div>
	</xsl:if>

	<xsl:if test="@status = 'sub_active'">
		<div class="tab_item_s_a"></div>
		<div class="tab_item_a"><a><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><b><xsl:value-of select="." /></b></a></div>
	</xsl:if>
</xsl:template>


<xsl:template match="tablegroup">
	<table class="tablegroup autowidth" cellspacing="0" cellpadding="0">
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
	<td class="tablegroup_hc" nowrap="nowrap">
		<xsl:attribute name="align"><xsl:value-of select="@align"/></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style"/></xsl:attribute>
		<xsl:attribute name="colspan"><xsl:value-of select="@colspan"/></xsl:attribute>
		<xsl:apply-templates />
		<xsl:if test=". = ''">&nbsp;</xsl:if>
	</td>
</xsl:template>


<xsl:template match="col">
	<td class="tablegroup_c">
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



<xsl:template match="setgroup">
	<br />
	<table cellspacing="0" cellpadding="0" class="autowidth" style="border: #C0C0C0 1px solid">
		<tr style="cursor: pointer">
			<xsl:attribute name="onclick">javascript: v_switch(document.getElementById('<xsl:value-of select="@id" />')); return false;</xsl:attribute>
			<td width="88%" height="20" class="sg_title" style="background-color: #E3E9EF;">
				<nobr>
					<a href="#" onclick="javscript: return false;" class="sg_blue">
						<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
						<u><xsl:attribute name="id"><xsl:value-of select="@id" />_vtext</xsl:attribute></u>
						<img src="/images/cms/admin/full/sg_arrow_down.gif" style="margin-left: 2px; margin-right: 7px;" hspace="0" vspace="0" class="left">
							<xsl:attribute name="name"><xsl:value-of select="@id" />_img</xsl:attribute>
						</img>
					</a>
				</nobr>
				<b><xsl:value-of select="@name" /></b>
			</td>
		</tr>
		<tr>
			<td>
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
							<td style="padding: 7px;">
								<xsl:apply-templates />
							</td>
						</xsl:if>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<script type="text/javascript">
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


</xsl:stylesheet>