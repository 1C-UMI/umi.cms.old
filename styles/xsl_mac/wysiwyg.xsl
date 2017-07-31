<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="wysiwyg">
		<xsl:if test="title">
			<div class="ftext"><xsl:value-of select="title"/></div>
		</xsl:if>
		<textarea id="content" name="content" style="width: 100%; height: 400px" rows="23" cols="70">
			<xsl:if test="@height != ''">
				<xsl:attribute name="style">width: 100%; height: <xsl:value-of select="@height" />px</xsl:attribute>
			</xsl:if>
			<xsl:if test="@id != ''"><xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="@id" /></xsl:attribute>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="value">
					<xsl:value-of select="value"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="."/>
				</xsl:otherwise>
			</xsl:choose>
		</textarea>
		<xsl:if test="not(@id = '')">
			<script type="text/javascript">
				reg_area('<xsl:value-of select="@id" />');
			</script>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
