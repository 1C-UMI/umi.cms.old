<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0"
			xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
			xmlns:umi="http://www.umi-cms.ru/2007/umi-cms-markup">

<xsl:template match="umi:filter">
	<div id="filter">
		<form action="{/umicms/pre_lang}/admin/{/umicms/module}/apply_filters/">
			<xsl:apply-templates select="//umi:property[@name='fltr_sstring']"/>
			<a id="advanced-search">Расширенный поиск</a>
			<div id="advanced-settings">
				<div style="float: left; width: 250px">
					<xsl:apply-templates select="//umi:property[@type='multipleSelect']"/>
					<xsl:apply-templates select="//umi:group[position() = last()]" mode="special"/>
				</div>
				<div class="groups">
					<xsl:apply-templates select="//umi:group[position() != last()]"/>
				</div>
			</div>
			<xsl:apply-templates select="//umi:message"/>
			<input type="hidden" name="hierarchy_type_id" value="{@hierarchy_type_id}"/>
		</form>
	</div>
</xsl:template>

<xsl:template match="umi:filter/umi:group" mode="special">
	<div>
		<strong style="margin: 5px 0"><xsl:apply-templates select="umi:title" mode="default"/></strong>
		<div class="properties">
			<xsl:apply-templates select="umi:property"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="umi:filter/umi:message">
	<div class="message">
		<xsl:apply-templates />
	</div>
</xsl:template>

<xsl:template match="umi:filter/umi:group">
	<div class="group">
		<xsl:apply-templates select="umi:title"/>
		<div class="properties">
			<xsl:apply-templates select="umi:property"/>
		</div>
	</div>
</xsl:template>

<xsl:template match="umi:filter/umi:group/umi:title">
	<div class="title">
		<xsl:apply-templates />:
	</div>
</xsl:template>


<xsl:template match="umi:filter//umi:property[@name='fltr_sstring']" priority="1">
	Что искать: <input class="input" type="text" name="{@name}" value="{umi:value}"/>&nbsp;&nbsp;<input class="mbutton" type="submit" value="{umi:title}"/>
</xsl:template>

<xsl:template match="umi:filter//umi:property">
	<span style="white-space: nowrap">
		<input type="checkbox" name="{@name}" id="{@name}">
			<xsl:if test="(umi:value = 1) or (umi:value = 'true') or (umi:value = 'yes')">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:if>
		</input>
		<label for="{@name}"><xsl:value-of select="umi:title"/></label>
	</span>
</xsl:template>


<xsl:template match="umi:property[@type = 'multipleSelect']">
</xsl:template>


<xsl:template match="umi:property[@type = 'multipleSelect'][umi:choices/umi:item[position() &gt; 1]]">
	<!--strong><xsl:apply-templates select="umi:title"/></strong-->
	<select name="{@name}[]" multiple="multiple" style="width: 235px">
		<xsl:apply-templates select="umi:choices"/>
	</select>
</xsl:template>


<xsl:template match="umi:property[@type = 'multipleSelect']//umi:item">
	<option value="{@name}">
		<xsl:if test="(@selected = 1) or (@selected = 'true') or (@selected = 'yes')">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates/>
	</option>
</xsl:template>

<xsl:template match="umi:property[@type = 'select'][umi:choices/umi:item[position() &gt; 1]]">
	<xsl:apply-templates select="umi:title"/>
	<select name="{@name}">
		<xsl:apply-templates select="umi:choices"/>
	</select>
</xsl:template>


<xsl:template match="umi:property[@type = 'select']//umi:item">
	<option value="{@name}">
		<xsl:if test="(@selected = 1) or (@selected = 'true') or (@selected = 'yes')">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>
		<xsl:apply-templates/>
	</option>
</xsl:template>

</xsl:stylesheet>