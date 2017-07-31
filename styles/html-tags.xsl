<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="b | strong | i | u | h2 | br">
	<xsl:element name="{name()}"><xsl:apply-templates /></xsl:element>
</xsl:template>


<xsl:template match="p | div | span | font">
	<xsl:element name="{name()}">
		<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
		<xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		
		<xsl:attribute name="align"><xsl:value-of select="@align" /></xsl:attribute>
		<xsl:apply-templates />
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


<xsl:template match="hr">
	<br/><br/><hr width="50%" align="left" />
</xsl:template>


<xsl:template match="table">
	<table>
		<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
		<xsl:attribute name="border"><xsl:value-of select="@border" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		<xsl:attribute name="cellpadding"><xsl:value-of select="@cellpadding" /></xsl:attribute>
		<xsl:attribute name="cellspacing"><xsl:value-of select="@cellspacing" /></xsl:attribute>
		<xsl:attribute name="bgcolor"><xsl:value-of select="@bgcolor" /></xsl:attribute>
		<xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute>
		
		<xsl:apply-templates select="tr" />
	</table>
</xsl:template>


<xsl:template match="table/tr">
	<tr>
		<xsl:apply-templates select="td" />
	</tr>
</xsl:template>


<xsl:template match="table/tr/td">
	<td valign="top">
		<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
		<xsl:attribute name="height"><xsl:value-of select="@height" /></xsl:attribute>
		<xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		<xsl:attribute name="colspan"><xsl:value-of select="@colspan" /></xsl:attribute>
		<xsl:attribute name="rowspan"><xsl:value-of select="@rowspan" /></xsl:attribute>
		
		<xsl:apply-templates />
		<span style="font-size: 0px;">&nbsp;</span>
	</td>
</xsl:template>


<xsl:template match="swf">
	<OBJECT classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" id="index" ALIGN="middle">
		<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
		<xsl:attribute name="height"><xsl:value-of select="@height" /></xsl:attribute>

		<PARAM NAME="movie"> 
			<xsl:attribute name="value"><xsl:value-of select="@src" /></xsl:attribute>
		</PARAM>

		<PARAM NAME="quality" VALUE="low" />
		<param name="WMode" value="Transparent" />
		<PARAM NAME="menu" VALUE="true" />
		<PARAM NAME="scale" VALUE="noscale" />
		<PARAM NAME="bgcolor" VALUE="#ffffff" /> 

		<EMBED quality="low" wmode="transparent" menu="true" bgcolor="#ffffff" scale="noscale"  allowScriptAccess="sameDomain" NAME="index" ID="index" SWLIVECONNECT="true" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
			<xsl:attribute name="src"><xsl:value-of select="@src" /></xsl:attribute>
			<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
			<xsl:attribute name="height"><xsl:value-of select="@height" /></xsl:attribute>
		</EMBED>
	</OBJECT>
</xsl:template>


<xsl:template match="script">
	<script type="text/javascript">
		<xsl:value-of select="." />
	</script>
</xsl:template>




</xsl:stylesheet>