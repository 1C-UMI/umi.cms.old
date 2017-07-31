<?xml version="1.0"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="langs">
	<span id="langs"><xsl:value-of select="/umicms/phrases/core_langs"/>:&nbsp;<xsl:apply-templates/></span>
</xsl:template>

<xsl:template match="lang">
	<xsl:choose>
		<xsl:when test="@active = 'yes'">
			<a class="active" title="{.}"><xsl:value-of select="@prefix"/></a>
		</xsl:when>
		<xsl:otherwise>
			<a href="{@link}" title="{.}"><xsl:value-of select="@prefix"/></a>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="menu">
	<div id="drop_down_menu" onmouseout="document.timeoutId = window.setTimeout('hide()', 2000);" onmouseover="clearTimeout(document.timeoutId);">
		<div class="header">
			<div id="version">umicms.pro</div>
			<div id="domain">
				<xsl:choose>
					<xsl:when test="string-length(../domain) &gt; 18">
						<xsl:value-of select="substring(../domain, '1', '18')"/>...
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="../domain"/>
					</xsl:otherwise>
				</xsl:choose>
			</div>
		</div>
		<div id="items">
			<div style="width: 50%; border-right: #4a87bb 1px solid">
				<xsl:for-each select="item[position() &lt;= $num-of-menu]">
					<a class="item" href="{@link}" onclick="hide();" onmousedown="createMovedDItem('{substring-after(substring-before(@ico, '.jpg'), 'ico_')}', '{.}'); return false;"><img class="png" onmousedown="return false;" src="{concat('/images/cms/admin/mac/icons/small/', substring-after(substring-before(@ico, '.jpg'), 'ico_'), '.png')}"/><xsl:value-of select="."/></a>
				</xsl:for-each>
			</div>
			<div style="position: absolute; left: 50%; top: 34px; width: 50%">
				<xsl:for-each select="item[position() &gt; $num-of-menu]">
					<a class="item" href="{@link}" onclick="hide();" onmousedown="createMovedDItem('{substring-after(substring-before(@ico, '.jpg'), 'ico_')}', '{.}'); return false;"><img class="png" onmousedown="return false;" src="{concat('/images/cms/admin/mac/icons/small/', substring-after(substring-before(@ico, '.jpg'), 'ico_'), '.png')}"/><xsl:value-of select="."/></a>
				</xsl:for-each>
			</div>
		</div>
		<!--div id="items">
			<div style="width: 50%; border-right: #4a87bb 1px solid">
				<xsl:for-each select="item[position() &lt;= $num-of-menu]">
					<a class="item" href="{@link}" onmouseout="document.timeoutId = window.setTimeout('hide()', 2000);" onmouseover="clearTimeout(document.timeoutId);"><img class="png" src="{concat('/images/cms/admin/mac/icons/small/', substring-after(substring-before(@ico, '.jpg'), 'ico_'), '.png')}"/><xsl:value-of select="."/></a>
				</xsl:for-each>
			</div>
			<div style="position: absolute; left: 50%; top: 34px; width: 50%">
				<xsl:for-each select="item[position() &gt; $num-of-menu]">
					<a class="item" href="{@link}" onmouseout="document.timeoutId = window.setTimeout('hide()', 2000);" onmouseover="clearTimeout(document.timeoutId);"><img class="png" src="{concat('/images/cms/admin/mac/icons/small/', substring-after(substring-before(@ico, '.jpg'), 'ico_'), '.png')}"/><xsl:value-of select="."/></a>
				</xsl:for-each>
			</div>
		</div-->
		<img class="png" src="/images/cms/admin/mac/common/drop_down_footer.png"/>
	</div>
</xsl:template>

<!--xsl:template match="menu" mode="dock">
	<div id="dock">
		<div style="position: absolute; width: 100%; height: 100%; left: 0px; top: 0px; background: #fff; opacity: 0.85; z-index: -1"></div>
		<xsl:apply-templates select="item" mode="dock"/>
	</div>
</xsl:template>

<xsl:template match="menu/item" mode="dock">
	<a href="{@link}"><img class="png" src="{concat('/images/cms/admin/mac/icons/medium/', substring-after(substring-before(@ico, '.jpg'), 'ico_'), '.png')}" title="{.}" alt="{.}" onmousedown="return false;" /></a>&nbsp;
</xsl:template-->

<xsl:template match="navibar">
	<div id="navibar"><xsl:apply-templates /></div>	
</xsl:template>

<xsl:template match="navibar/item">
	<xsl:choose>
		<xsl:when test="@last = 'yes'">
			<a class="active"><xsl:value-of select="."/></a>
		</xsl:when>
		<xsl:otherwise>
			<a href="{@link}"><xsl:value-of select="."/></a> \
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="imenu">
	<div class="panel">
		<div class="header">
			<span><span>
				<div class="tabs"><xsl:apply-templates select="item"/></div>
			</span></span>
		</div>
		<div class="content">
			<div class="rb" style="width: 100%">
				<xsl:apply-templates select="all"/>
			</div>
		</div>
	</div>
</xsl:template>

<xsl:template match="imenu/item">
	<xsl:param name="class">
		<xsl:if test="position() = 1">first </xsl:if>
		<xsl:if test="position() = last()">last	</xsl:if>
		<xsl:if test="@status = 'active'">active </xsl:if>
		<xsl:if test="@status = 'sub_active'">active </xsl:if>
	</xsl:param>
	<xsl:choose>
		<xsl:when test="@status = 'active'">
			<a>
				<xsl:if test="$class != ''">
					<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				</xsl:if>
				<xsl:value-of select="." />
			</a>
		</xsl:when>
		<xsl:otherwise>
			<a href="{@link}" style="color: #676767">
				<xsl:if test="$class != ''">
					<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				</xsl:if>
				<xsl:value-of select="." />
			</a>
		</xsl:otherwise>
	</xsl:choose>
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


<xsl:template match="warning">
	<span style="color: red"><b>Ошибка:</b>&nbsp;<xsl:apply-templates /></span>
</xsl:template>


<xsl:template match="notice">
	<span style="color: #AAAA00"><b>Notice:</b>&nbsp;<xsl:apply-templates /></span>
</xsl:template>








	


<xsl:template match="contentTree">
	<div>
		<xsl:attribute name="id">tp_<xsl:value-of select="domainName" /></xsl:attribute>
	</div>

	<script type="text/javascript">
		if(document.contentTreeInstance) {
			var someTree = document.contentTreeInstance;
		} else {
			var someTree = new contentTree("tp_<xsl:value-of select="domainName" />");
			someTree.pre_lang = "<xsl:value-of select="//umicms/pre_lang" />";
			document.contentTreeInstance = someTree;
		}
		someTree.addDomain("<xsl:value-of select="domainName" />");
	</script>
</xsl:template>









<xsl:template match="setgroup">
	<div class="panel">
		<div class="header full" style="cursor: pointer">
			<xsl:attribute name="onclick">javascript: v_switch(document.getElementById('<xsl:value-of select="@id" />')); return false;</xsl:attribute>
			<span><span>
				<nobr>
					<a href="#" onclick="javscript: return false;" class="sg_blue">
						<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
						<u><xsl:attribute name="id"><xsl:value-of select="@id" />_vtext</xsl:attribute></u>
						<img src="/images/cms/admin/mac/sg_arrow_up.gif" style="margin: -1px 7px 0 8px;" hspace="0" vspace="0" class="left">
							<xsl:attribute name="name"><xsl:value-of select="@id" />_img</xsl:attribute>
						</img>
					</a>
				</nobr>
				<b><xsl:value-of select="@name" /></b>
			</span></span>
		</div>
		<div class="content" style="padding: 0px; border-top: none">
			<div class="fixing">
				<xsl:attribute name="id"><xsl:value-of select="@id" />_table</xsl:attribute>
				<xsl:if test="@form = 'yes'">
					<form method="post" enctype="multipart/form-data">
						<xsl:attribute name="name"><xsl:value-of select="@id" /></xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
						<xsl:attribute name="enctype"><xsl:value-of select="@enctype" /></xsl:attribute>
						<xsl:apply-templates />
					</form>
				</xsl:if>
				<xsl:if test="@form = 'no'">
					<xsl:apply-templates />
				</xsl:if>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		v_onload('<xsl:value-of select="@id" />');
	</script>
	<br />
</xsl:template>

<xsl:template match="setgroup" mode="anchors">
	<a href="#{@id}" onclick="checkVisible('{@id}');" onmouseover="document.getElementById('anchors').style.display = 'block'" onmouseout="document.getElementById('anchors').style.display = 'block'"><xsl:value-of select="@name"/></a>
</xsl:template>











<xsl:template match="tinytable">
	<table class="tiny" cellspacing="1">
		<tr>
			<xsl:apply-templates />
		</tr>
	</table>
</xsl:template>


<xsl:template match="tinytable/col">
	<td style="background-color: #fafafa; padding: 10px; font-size: 11px; font-family: Tahoma; ">
		<xsl:copy-of select="@style | @align | @width"/>
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


<xsl:template match="tablegroup">
	<table class="tablegroup">
		<xsl:copy-of select="@style"/>
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
		<xsl:copy-of select="@align | @style | @colspan"/>
		<xsl:apply-templates />
	</td>
</xsl:template>

<xsl:template match="col">
	<td class="tablegroup_c">
		<xsl:copy-of select="@style | @align | @width | @colspan"/>
		<xsl:apply-templates />
	</td>
</xsl:template>

<xsl:template match="hrow | row">
	<tr>
		<xsl:apply-templates />
	</tr>
</xsl:template>	


</xsl:stylesheet>