<?xml version="1.0" encoding="UTF-8"?>

<!DOCTYPE xsl:stylesheet  [
	<!ENTITY nbsp   "&#160;">
]>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="langs">
	<xsl:apply-templates />
</xsl:template>


<xsl:template match="lang">
	<xsl:if test="@active = 'yes'">
		<span class="lang"><xsl:value-of select="." /></span>
	</xsl:if>
	<xsl:if test="not(@active = 'yes')">
		<a class="lang"><xsl:attribute name="href"><xsl:value-of select="@link" /></xsl:attribute><xsl:value-of select="." /></a>
	</xsl:if>
</xsl:template>


<xsl:template match="imgButton">
	<div class="imgBtn">
		<a class="imgBtn_icon" href="{link}">
			<xsl:if test="onclick">
				<xsl:attribute name="onclick"><xsl:value-of select="onclick"/></xsl:attribute>
			</xsl:if>
			<img src="{src}" alt="{title}" title="{title}" />
		</a>
		<a class="imgBtn_title" href="{link}">
			<xsl:if test="onclick">
				<xsl:attribute name="onclick"><xsl:value-of select="onclick"/></xsl:attribute>
			</xsl:if>
			<xsl:value-of select="title" />
		</a>
	</div>
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


<xsl:template match="button">
	<xsl:if test="not(disabled or @disabled = 'yes')">
		<input type="button" class="std_submit">
			<xsl:attribute name="onclick"><xsl:value-of select="onclick | @onclick" /></xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@title" /></xsl:attribute>
		</input>
	</xsl:if>
</xsl:template>


<xsl:template match="textarea">
	<textarea class="std">
		<xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
		<xsl:attribute name="style"><xsl:value-of select="@style" /></xsl:attribute>
		<xsl:if test="@disabled != ''">
			<xsl:attribute name="disabled"></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="." />
	</textarea>
</xsl:template>


<xsl:template match="password">
	<xsl:call-template name="ftext" />
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'password'" />
		<xsl:with-param name="class" select="'std'"/>
	</xsl:call-template>
</xsl:template>


<xsl:template match="submit">
	<input type="submit" class="std_submit">
		<xsl:attribute name="id"><xsl:value-of select="@id" /></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@title" /></xsl:attribute>
		<xsl:attribute name="onclick"><xsl:value-of select="@onclick" /></xsl:attribute>
		<xsl:if test='@disabled = "yes"'>
			<xsl:attribute name="disabled">disabled</xsl:attribute>
			<xsl:attribute name="style">background-color: #999; color: #FFF</xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@title" /></xsl:attribute>
		</xsl:if>
	</input>
</xsl:template>


<xsl:template match="passthru">
	<input type="hidden">
		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="."/></xsl:attribute>
	</input>
</xsl:template>

<!--
<xsl:template match="passthru">
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'hidden'" />
	</xsl:call-template>
</xsl:template>
-->


<xsl:template match="form">
	<form method="post" enctype="multipart/form-data">
		<xsl:attribute name="action"><xsl:value-of select="@action" /></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@name" /></xsl:attribute>
		<xsl:attribute name="onsubmit"><xsl:value-of select="@onsubmit" /></xsl:attribute>
		<xsl:apply-templates />
	</form>
</xsl:template>


<xsl:template match="file">
	<xsl:call-template name="ftext" /> 
	<xsl:call-template name="for-input">
		<xsl:with-param name="type" select="'file'"/>
		<xsl:with-param name="class" select="'std'"/>
	</xsl:call-template>
</xsl:template>


<xsl:template match="multiple">
	<xsl:call-template name="ftext" />
	<xsl:call-template name="for-select">
		<xsl:with-param name="multiple" select="'multiple'"/>
	</xsl:call-template>
</xsl:template>


<xsl:template match="multipleGuideInput">
	<xsl:variable name="height" select="15"/>
	<xsl:variable name="height_offset" select="20"/>
	<div style="width: {@width}px; height: {@height + 10}px; border: 0; margin-top: 3px;">
		<xsl:call-template name="ftext" />
		<xsl:call-template name="for-select">
			<xsl:with-param name="id">multipleGuideSelect_<xsl:value-of select="id | @id"/></xsl:with-param>
			<xsl:with-param name="multiple">multiple</xsl:with-param>
			<xsl:with-param name="style">width: <xsl:value-of select="@width"/>px; height: <xsl:value-of select="@height - $height - $height_offset + 5"/>px; margin-bottom: 1px; padding-bottom: 0px;</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="for-input">
			<xsl:with-param name="style">width: <xsl:value-of select="@width"/>px; height: <xsl:value-of select="$height"/>px; margin: 0px; padding: 0px;</xsl:with-param>
			<xsl:with-param name="id">multipleGuideInput_<xsl:value-of select="id"/></xsl:with-param>
			<xsl:with-param name="title" select="''"/>
		</xsl:call-template>
	</div>
	<script type="text/javascript">
		var t = new multipleGuideInput(<xsl:value-of select="id"/>);
	</script>
</xsl:template>


<xsl:template match="singleGuideInput">
	<xsl:variable name="height" select="15"/>
	<xsl:variable name="height_offset" select="20"/>
	<div style="width: {@width}px; height: {@height}px; border: 0;">
		<xsl:call-template name="ftext" />
		<xsl:call-template name="for-select">
			<xsl:with-param name="id">multipleGuideSelect_<xsl:value-of select="id | @id"/></xsl:with-param>
			<!--xsl:with-param name="multiple">multiple</xsl:with-param-->
			<xsl:with-param name="style">width: <xsl:value-of select="@width"/>px; height: <xsl:value-of select="@height - $height - $height_offset"/>px</xsl:with-param>
		</xsl:call-template>
		<xsl:call-template name="for-input">
			<xsl:with-param name="style">width: <xsl:value-of select="@width"/>px; height: <xsl:value-of select="$height"/>px; margin: 0px; padding: 0px;</xsl:with-param>
			<xsl:with-param name="id">multipleGuideInput_<xsl:value-of select="id"/></xsl:with-param>
			<xsl:with-param name="title" select="''"/>
		</xsl:call-template>
	</div>
	<script type="text/javascript">
		var t = new multipleGuideInput(<xsl:value-of select="id"/>);
	</script>
</xsl:template>


<xsl:template match="tip">
	<xsl:if test="content != ''">
		<img class="tip" src="/images/cms/admin/full/ico_help.gif">
			<xsl:attribute name="onmouseover">show_tip(this, '<xsl:value-of select="name"/>', '<xsl:value-of select="title"/>', '<xsl:value-of select="content"/>')</xsl:attribute>
		</img>
	</xsl:if>
</xsl:template>


<xsl:template match="symlinkInput">
	<div class="ftext"><xsl:value-of select="title" /></div>
	<div>
		<xsl:attribute name="id">symlinkInputPlacer_<xsl:value-of select="@id" /></xsl:attribute>
	</div>

	<script type="text/javascript">
		var symlinkInstance = new symlinkInput('<xsl:value-of select="@id" />');

		var values = new Array();
		<xsl:for-each select="values/node()">
			if("<xsl:value-of select="@id" />") {
				values[parseInt("<xsl:value-of select="@id" />")] = new Array("<xsl:value-of disable-output-escaping="no" select="@id" />", "<xsl:value-of select="title" />");
			}
		</xsl:for-each>
		symlinkInstance.init();
		symlinkInstance.addValues(values, true);
	</script>
</xsl:template>

</xsl:stylesheet>