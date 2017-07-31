<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template name="tip">
	<xsl:if test="tip != ''">
		<img class="tip" src="/images/cms/admin/full/ico_help.gif">
			<xsl:attribute name="onmouseover">show_tip(this, '<xsl:value-of select="name"/>', '<xsl:value-of select="title"/>', '<xsl:value-of select="tip"/>')</xsl:attribute>
		</img>
	</xsl:if>
</xsl:template>


<xsl:template name="ftext">
	<xsl:if test="title | @title">
		<span class="ftext">
			<xsl:value-of select="title | @title" /><xsl:if test="not(@quant = 'no')">:&nbsp;</xsl:if>
			<xsl:if test="tip">
				<xsl:call-template name="tip"/>
			</xsl:if>
		</span>
	</xsl:if>
	<!--xsl:if test="@br = 'yes'"><br /></xsl:if-->
</xsl:template>


<xsl:template name="for-input">
	<xsl:param name="type">text</xsl:param>
	<xsl:param name="id">
		<xsl:choose>
			<xsl:when test="id | @id">
				<xsl:value-of select="id | @id" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="name | @name" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:param>
	<xsl:param name="title" select="title | @title" />
	<xsl:param name="style">
		<xsl:value-of select="style | @style" />
	</xsl:param>
	<xsl:param name="class">
		<xsl:value-of select="class | @class" />
	</xsl:param>
	<xsl:if test="$title and $type = 'text'">
		<label class="ftext">
			<xsl:attribute name="for"><xsl:value-of select="$id"/></xsl:attribute>
			<xsl:value-of select="$title" />
			<xsl:call-template name="tip"/>
		</label>
	</xsl:if>
	<!--xsl:if test="br or @br = 'yes'"><br /></xsl:if-->
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
		<xsl:attribute name="onsubmit"><xsl:value-of select="onsubmit | @onsubmit" /></xsl:attribute>
		<xsl:attribute name="onreset"><xsl:value-of select="onreset | @onreset" /></xsl:attribute>
		<xsl:attribute name="onclick"><xsl:value-of select="onclick | @onclick" /></xsl:attribute>
		<xsl:attribute name="ondblclick"><xsl:value-of select="ondblclick | @ondblclick" /></xsl:attribute>
		<xsl:attribute name="onmousedown"><xsl:value-of select="onmousedown | @onmousedown" /></xsl:attribute>
		<xsl:attribute name="onmouseup"><xsl:value-of select="onmouseup | @onmouseup" /></xsl:attribute>
		<xsl:attribute name="onmouseover"><xsl:value-of select="onmouseover | @onmouseover" /></xsl:attribute>
		<xsl:attribute name="onmouseout"><xsl:value-of select="onmouseout | @onmouseout" /></xsl:attribute>
		<xsl:attribute name="onmousemove"><xsl:value-of select="onmousemove | @onmousemove" /></xsl:attribute>
		<xsl:attribute name="onkeydown"><xsl:value-of select="onkeydown | @onkeydown" /></xsl:attribute>
		<xsl:attribute name="onkeyup"><xsl:value-of select="onkeyup | @onkeyup" /></xsl:attribute>
		<xsl:attribute name="onkeypress"><xsl:value-of select="onkeypress | @onkeypress" /></xsl:attribute>
		<xsl:attribute name="onchange"><xsl:value-of select="onchange | @onchange" /></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="name | @name" /></xsl:attribute>
		<xsl:attribute name="size"><xsl:value-of select="size | @size" /></xsl:attribute>
		<xsl:if test="disabled or @disabled != ''">
			<xsl:attribute name="disabled">disabled</xsl:attribute>
		</xsl:if>
		<xsl:attribute name="value">
			<xsl:if test="value"><xsl:value-of select="value" /></xsl:if>
		</xsl:attribute>
		<xsl:if test="@selected != ''">
			<xsl:attribute name="checked"></xsl:attribute>
		</xsl:if>
	</input>
	<xsl:if test="$title and $type != 'text'">
		<label>
			<xsl:attribute name="for"><xsl:value-of select="$id"/></xsl:attribute>
			&nbsp;<xsl:value-of select="$title" />
			<xsl:call-template name="tip"/>
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
		<xsl:apply-templates select="item|ortgroup"/>
	</select>
</xsl:template>


<xsl:template name="for-option">
	<xsl:param name="selected" select="@selected"/>
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


<xsl:template match="ortgroup">
	<ortgroup><xsl:value-of select="." /></ortgroup>
</xsl:template>


</xsl:stylesheet>