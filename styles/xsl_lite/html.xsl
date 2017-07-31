<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	
<xsl:template match="img">
	<xsl:if test="@alt">
		<xsl:value-of select="@alt" />
	</xsl:if>
	<xsl:if test="not(@alt)">*</xsl:if>
</xsl:template>



</xsl:stylesheet>