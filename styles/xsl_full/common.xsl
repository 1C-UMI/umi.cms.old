<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="for-input">
	<xsl:param name="type">text</xsl:param>
	<xsl:param name="id">
		<xsl:value-of select="id | @id" />
	</xsl:param>
	<xsl:param name="title" select="title | @title" />
	<xsl:param name="style">
		<xsl:value-of select="style | @style" />
	</xsl:param>
	<xsl:param name="class">
		<xsl:value-of select="class | @class" />
	</xsl:param>
	<xsl:if test="$title and $type = 'text'">
		<label>
			<xsl:attribute name="for"><xsl:value-of select="name | @name"/></xsl:attribute>
			&nbsp;<xsl:value-of select="$title" />
		</label>
	</xsl:if>
	<xsl:if test="br or @br = 'yes'"><br /></xsl:if>
	<input type="{$type}">
		<xsl:if test="$id != ''">
			<xsl:attribute name="id"><xsl:value-of select="$id" /></xsl:attribute>
		</xsl:if>
		<xsl:if test="$style != ''">
			<xsl:attribute name="style"><xsl:value-of select="$style" /></xsl:attribute>
		</xsl:if>
		<xsl:if test="$class != ''">
			<xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="title"><xsl:value-of select="comment | @comment" /></xsl:attribute>
		<xsl:attribute name="onkeydown"><xsl:value-of select="onkeydown | @onkeydown" /></xsl:attribute>
		<xsl:attribute name="onkeyup"><xsl:value-of select="onkeyup | @onkeyup" /></xsl:attribute>
		<xsl:attribute name="onkeypress"><xsl:value-of select="onkeypress | @onkeypress" /></xsl:attribute>
		<xsl:attribute name="onchange"><xsl:value-of select="onchange | @onchange" /></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="name | @name" /></xsl:attribute>
		<xsl:attribute name="size"><xsl:value-of select="size | @size" /></xsl:attribute>
		<xsl:if test="disabled or @disabled != ''">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>
		<xsl:attribute name="value"><xsl:value-of select="value | @value | text()" /></xsl:attribute>
		<xsl:if test="(@checked = 'yes') or (checked = 'yes') or (text() = '1')">
			<xsl:attribute name="checked"></xsl:attribute>
		</xsl:if>
	</input>
	<xsl:if test="$title and $type != 'text'">
		<label>
			<xsl:attribute name="for"><xsl:value-of select="name | @name"/></xsl:attribute>
			&nbsp;<xsl:value-of select="$title" />
		</label>
	</xsl:if>
</xsl:template>

<xsl:template name="for-select">
	<xsl:param name="id" select="id | @id" />
	<xsl:param name="multiple" />
	<xsl:param name="style" select="style | @style" />
	<xsl:param name="class"><xsl:value-of select="class | @class"/></xsl:param>
	<select>
		<xsl:if test="$id != ''">
			<xsl:attribute name="id"><xsl:value-of select="$id" /></xsl:attribute>
		</xsl:if>
		<xsl:if test="$multiple !=''">
			<xsl:attribute name="multiple">multiple</xsl:attribute>
		</xsl:if>
		<xsl:if test="$style != ''">
			<xsl:attribute name="style"><xsl:value-of select="$style"/></xsl:attribute> 
		</xsl:if>
		<xsl:if test="@disabled != ''">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>
		<xsl:if test="$class != ''">
			<xsl:attribute name="class"><xsl:value-of select="$class" /></xsl:attribute>
		</xsl:if>
		<xsl:attribute name="name"><xsl:value-of select="name | @name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="value | @value | text()"/></xsl:attribute>
		<xsl:attribute name="size"><xsl:value-of select="size | @size" /></xsl:attribute>
		<xsl:attribute name="onchange"><xsl:value-of select="onchange | @onchange" /></xsl:attribute>
		<xsl:apply-templates select="item"/>
	</select>
</xsl:template>

<xsl:template name="for-option">
	<xsl:param name="selected" select="@selected | @checked"/>
	<xsl:param name="onclick" select="onclick | @onclick"/>
	<option>
		<xsl:if test="$selected != ''">
			<xsl:attribute name="selected">selected</xsl:attribute>
		</xsl:if>
		<xsl:if test="@disabled != ''">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>
		<xsl:attribute name="id"><xsl:value-of select="id"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="value | @value"/></xsl:attribute>
		<xsl:attribute name="onclick"><xsl:value-of select="$onclick"/></xsl:attribute>
		<xsl:choose>
			<xsl:when test="title">
				<xsl:value-of select="title"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="."/>
			</xsl:otherwise>
		</xsl:choose>
	</option>
</xsl:template>

<xsl:template match="item">
	<xsl:call-template name="for-option" />
</xsl:template>


</xsl:stylesheet>