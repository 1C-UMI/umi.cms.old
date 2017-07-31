<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="p | div | span | b | i | br">
	<xsl:element name="{name()}">
		<xsl:copy-of select="@id | @class | @style | @align"/>
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>


<xsl:template match="a">
	<a>
		<xsl:attribute name="href"><xsl:value-of select="href | @href" /></xsl:attribute>
		<xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute>
		<xsl:attribute name="title"><xsl:value-of select="title | @title" /></xsl:attribute>
		<xsl:attribute name="target"><xsl:value-of select="@target" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		<xsl:if test="@commit != ''">
			<xsl:attribute name="onclick">javascript: return if_sured(this, "<xsl:value-of select="@commit" />");</xsl:attribute>
		</xsl:if>
		<xsl:if test="@commit_unrestorable != ''">
			<xsl:attribute name="onclick">javascript: return if_sured_unrestorable(this, "<xsl:value-of select="@commit_unrestorable" />");</xsl:attribute>
		</xsl:if>

		<xsl:if test="@onclick != ''">
			<xsl:attribute name="onclick"><xsl:value-of select="@onclick" /></xsl:attribute>
		</xsl:if>

		<xsl:apply-templates />
	</a>
</xsl:template>


<xsl:template match="p[@align = 'right']">
	<div class="p"><xsl:apply-templates/></div>
</xsl:template>

<xsl:template match="img">
	<img>
		<xsl:copy-of select="@src | @title | @alt"/>
	</img>
</xsl:template>

<xsl:template match="img[../..//span[@class='permissionsModuleName']]">
	<img class="png" src="{concat('/images/cms/admin/mac/icons/small/', substring-before(substring-after(@src, 'ico_'), '.'), '.png')}"/>
</xsl:template>

<xsl:template match="table">
	<table>
		<xsl:if test="not(@width)"><xsl:attribute name="style">width: auto</xsl:attribute></xsl:if>
		<xsl:copy-of select="@class | @style | @width"/>
		<xsl:apply-templates/>
	</table>
</xsl:template>

<xsl:template match="tr | td">
	<xsl:element name="{name()}">
		<xsl:copy-of select="@class | @style | @width | @colspan"/>
		<xsl:if test="@rowspan">
			<xsl:attribute name="rowspan"/>
		</xsl:if>
		<xsl:if test="tablegroup">
			<xsl:attribute name="valign">top</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates/>
	</xsl:element>
</xsl:template>

<xsl:template match="script">
	<script type="text/javascript">
		<xsl:apply-templates/>
	</script>
</xsl:template>

<xsl:template match="swf">
	<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" id="index" ALIGN="middle">
		<xsl:copy-of select="@width | @height"/>
		<param name="movie"> 
			<xsl:attribute name="value"><xsl:value-of select="@src" /></xsl:attribute>
		</param>
		<param name="quality" value="low" />
		<param name="wmode" value="transparent" />
		<param name="menu" value="true" />
		<param name="scale" value="noscale" />
		<param name="bgcolor" value="#ffffff" /> 
		<embed quality="low" wmode="transparent" menu="true" bgcolor="#ffffff" scale="noscale"  allowScriptAccess="sameDomain" NAME="index" ID="index" SWLIVECONNECT="true" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
			<xsl:copy-of select="@src | @width | @height"/>
		</embed>
	</object>
</xsl:template>


</xsl:stylesheet>