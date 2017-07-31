<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="img">
	<img border="0">
		<xsl:attribute name="src"><xsl:value-of select="@src" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		<xsl:attribute name="alt"><xsl:value-of select="@alt" /></xsl:attribute>
		<xsl:attribute name="title"><xsl:value-of select="@alt" /></xsl:attribute>
		<xsl:if test="@width != ''">
			<xsl:attribute name="width"><xsl:value-of select="@width" /></xsl:attribute>
		</xsl:if>
		<xsl:if test="@height!= ''">
		<xsl:attribute name="height"><xsl:value-of select="@height" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
	</img>
</xsl:template>


</xsl:stylesheet>