<?xml version="1.0"?>
<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">


<xsl:template match="form">
	<form method="post" enctype="multipart/form-data">
		<xsl:copy-of select="@action | @name | @onsubmit"/>
		<xsl:apply-templates/>
	</form>
</xsl:template>

<xsl:template match="select">
	<xsl:call-template name="ftext" />
	<xsl:call-template name="for-select" />
</xsl:template>

<xsl:template match="input">
	<xsl:call-template name="for-input"/>
</xsl:template>

<xsl:template match="checkbox">
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'checkbox'"/>
		<xsl:with-param name="class" select="'std_checkbox'"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="radio">
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'radio'"/>
		<xsl:with-param name="class" select="'std_radio'"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="submit">
	<div class="mbutton">
		<span>
			<input type="submit" value="{@title}">
				<xsl:copy-of select="@onclick"/>
			</input>
		</span>
	</div>
</xsl:template>

<xsl:template match="button">
	<div class="mbutton">
		<span>
			<input type="button" value="{@title}">
				<xsl:copy-of select="@onclick"/>
				<xsl:if test="onclick">
					<xsl:attribute name="onclick"><xsl:value-of select="onclick"/></xsl:attribute>
				</xsl:if>
			</input>
		</span>
	</div>
</xsl:template>

<xsl:template match="password">
	<xsl:call-template name="ftext" />
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'password'" />
		<xsl:with-param name="class" select="'std'"/>
	</xsl:call-template>
</xsl:template>

<xsl:template match="passthru">
	<input type="hidden">
		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="."/></xsl:attribute>
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


</xsl:stylesheet>
