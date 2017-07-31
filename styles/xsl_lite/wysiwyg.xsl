<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

 <xsl:template match="wysiwyg">



<textarea id="content" name="content" style="width: 100%; height: 400px" rows="23" cols="70"><xsl:if test="@height != ''"><xsl:attribute name="style">width: 100%; height: <xsl:value-of select="@height" />px</xsl:attribute></xsl:if><xsl:if test="@id != ''"><xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute><xsl:attribute name="name"><xsl:value-of select="@id" /></xsl:attribute></xsl:if><xsl:apply-templates /></textarea>

<xsl:if test="not(@id = '')">
<script type="text/javascript">
reg_area('<xsl:value-of select="@id" />');
</script>
</xsl:if>
 </xsl:template>

</xsl:stylesheet>