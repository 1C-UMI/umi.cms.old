<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="//phrases">
	var phrases = new Array();
	<xsl:for-each select="node()">
		<xsl:if test="text() != ''">
			phrases['<xsl:value-of select="local-name()"/>'] = "<xsl:value-of select="text()"/>";
		</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template match="//variables">
	<xsl:apply-templates/>
</xsl:template>

<xsl:template match="variable">
	<xsl:if test="@name and text() != ''">
		var <xsl:value-of select="@name"/> = "<xsl:value-of select="text()"/>";
	</xsl:if>
</xsl:template>



</xsl:stylesheet>