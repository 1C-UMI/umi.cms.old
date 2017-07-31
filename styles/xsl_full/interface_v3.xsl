<?xml version="1.0"?>

<!DOCTYPE HTML [
<!ENTITY nbsp "&#160;">


<!ATTLIST table
	height CDATA #IMPLIED
>
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:template match="textInput">

		<label>
			<xsl:if test='not(@id)'>
				<xsl:attribute name="for"><xsl:value-of select="@name" /></xsl:attribute>
			</xsl:if>

			<xsl:if test='@id'>
				<xsl:attribute name="for"><xsl:value-of select="@id" /></xsl:attribute>
			</xsl:if>

			<xsl:value-of select="title/." />
		</label>

		<xsl:if test="not(title/@br = 'no')"><br /></xsl:if>

		<input type="text">
			<xsl:attribute name="name">
				<xsl:value-of select="@name" />
			</xsl:attribute>


			<xsl:attribute name="value">
				<xsl:value-of select="value/." />
			</xsl:attribute>


			<xsl:if test="@disabled != ''">
				<xsl:attribute name="disabled"></xsl:attribute>
			</xsl:if>

			<xsl:if test="not(@class = '')">
				<xsl:attribute name="class"><xsl:value-of select="class" /></xsl:attribute>
			</xsl:if>

			<xsl:if test='not(@id)'>
				<xsl:attribute name="id"><xsl:value-of select="@name" /></xsl:attribute>
			</xsl:if>

			<xsl:if test='@id'>
				<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
			</xsl:if>

			<xsl:attribute name="onkeydown"><xsl:value-of select="@onkeydown" /></xsl:attribute>
			<xsl:attribute name="onkeyup"><xsl:value-of select="@onkeyup" /></xsl:attribute>
			<xsl:attribute name="onkeypress"><xsl:value-of select="@onkeypress" /></xsl:attribute>
			<xsl:attribute name="onchange"><xsl:value-of select="@onchange" /></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
			<xsl:attribute name="size"><xsl:value-of select="@size" /></xsl:attribute>
			<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>

		</input>
	</xsl:template>

	<xsl:template match="multipleTextInput">


		<label>
			<xsl:if test='not(@id)'>
				<xsl:attribute name="for"><xsl:value-of select="@name" /></xsl:attribute>
			</xsl:if>

			<xsl:if test='@id'>
				<xsl:attribute name="for"><xsl:value-of select="@id" /></xsl:attribute>
			</xsl:if>

			<xsl:value-of select="title/." />
		</label>

		<xsl:if test="not(title/@br = 'no')"><br /></xsl:if>

		<script type="text/javascript">
			var multipleTextInputConfig = {};
			multipleTextInputConfig.values = new Array();
			multipleTextInputConfig.name = "<xsl:value-of select="@name" />";
			multipleTextInputConfig.id = "<xsl:value-of select="@id" />";

			<xsl:for-each select="node()/value">
				multipleTextInputConfig.values[multipleTextInputConfig.values.length] = "<xsl:value-of disable-output-escaping="no" select="." />";
			</xsl:for-each>
		</script>
	</xsl:template>


	<xsl:template match="contentTree">
		<div>
			<xsl:attribute name="id">tp_<xsl:value-of select="@domainName" /></xsl:attribute>
		</div>

		<script type="text/javascript">
			if(document.contentTreeInstance) {
				var someTree = document.contentTreeInstance;
			} else {
				var someTree = new contentTree("tp_<xsl:value-of select="@domainName" />");
				someTree.pre_lang = "<xsl:value-of select="//umicms/pre_lang" />";
				document.contentTreeInstance = someTree;
			}
			someTree.addDomain("<xsl:value-of select="domainName/." />");
		</script>
	</xsl:template>

</xsl:stylesheet>