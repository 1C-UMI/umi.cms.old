<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
				xmlns:date="http://exslt.org/dates-and-times"
				exclude-result-prefixes="date">

<xsl:output method="xml" encoding="UTF-8"/>

<xsl:variable name="store">store</xsl:variable>
<xsl:variable name="to-order">to_order</xsl:variable>

<xsl:variable name="picture">izobrazhenie</xsl:variable>
<xsl:variable name="deliveryIncluded">deliveryIncluded</xsl:variable>
<xsl:variable name="price">cena</xsl:variable>
<xsl:variable name="description">opisanie</xsl:variable>
<xsl:variable name="sales_notes">sales_notes</xsl:variable>
<xsl:variable name="currencyId">currency_id</xsl:variable>
<xsl:variable name="currency">currency</xsl:variable>
<xsl:variable name="rate">rate</xsl:variable>
<xsl:variable name="plus">plus</xsl:variable>
<xsl:variable name="is_cbrf">is_cbrf</xsl:variable>




<!--+ =====================================
	+ 		Special properties
	+ =====================================-->
<!-- vendor.model -->
<xsl:variable name="typePrefix">typePrefiks</xsl:variable>
<xsl:variable name="vendor">proizvoditel</xsl:variable>
<xsl:variable name="model">model</xsl:variable>

<!-- book -->
<xsl:variable name="author">author</xsl:variable>
<xsl:variable name="publisher">publisher</xsl:variable>
<xsl:variable name="series">series</xsl:variable>
<xsl:variable name="year">year</xsl:variable>
<xsl:variable name="isbn">isbn</xsl:variable>

<!-- artist.title -->
<xsl:variable name="artist">artist</xsl:variable>
<xsl:variable name="title">nazvanie</xsl:variable>
<xsl:variable name="media">media</xsl:variable>
<xsl:variable name="starring">starring</xsl:variable>
<xsl:variable name="director">director</xsl:variable>
<xsl:variable name="originalName">originalName</xsl:variable>
<xsl:variable name="country">country</xsl:variable>

<!-- event-ticket -->
<xsl:variable name="place">place</xsl:variable>
<xsl:variable name="hall">hall</xsl:variable>
<xsl:variable name="hall-plan">hall_plan</xsl:variable>
<xsl:variable name="hall_part">hall_part</xsl:variable>
<xsl:variable name="date">date</xsl:variable>
<xsl:variable name="is_premiere">premiere</xsl:variable>
<xsl:variable name="is_kids">for-kids</xsl:variable>

<!-- tour -->
<xsl:variable name="worldRegion">worldRegion</xsl:variable>
<!--xsl:variable name="country">country</xsl:variable-->
<xsl:variable name="region">region</xsl:variable>
<xsl:variable name="days">days</xsl:variable>
<xsl:variable name="dataTour">dataTour</xsl:variable>
<xsl:variable name="hotel_stars">hotel_stars</xsl:variable>
<xsl:variable name="room">room</xsl:variable>
<xsl:variable name="meal">meal</xsl:variable>
<xsl:variable name="included">included</xsl:variable>
<xsl:variable name="transport">transport</xsl:variable>
<xsl:variable name="price_min">price_min</xsl:variable>
<xsl:variable name="price_max">price_max</xsl:variable>




<xsl:template match="umicmsDump">
	<xsl:variable name="date" select="concat(substring-before(date:date-time(), 'T'), ' ', substring(substring-after(date:date-time(), 'T'), 1, 5))" />
	<yml_catalog date="{$date}">
		<shop>
			<name><xsl:value-of select="companyName" /></name>
			<company><xsl:value-of select="companyName" /></company>
			<xsl:if test="phone">
				<phone><xsl:value-of select="phone" /></phone>
			</xsl:if>
			<url><xsl:value-of select="domain" /></url>
			<currencies>
				<xsl:apply-templates select="object[.//property[name = $currencyId]]"/>
	  		</currencies>
			<categories>
				<xsl:apply-templates select="element[behaviour/method = 'category']">
					<xsl:sort select="@id"/>
				</xsl:apply-templates>
			</categories>
			<offers>
				<xsl:apply-templates select="object[//element[behaviour/method = 'object']/@objectId = @id]"/>
				<!--xsl:apply-templates select="element[behaviour/method = 'object']"/-->
			</offers>
		</shop>
	</yml_catalog>
</xsl:template>


<xsl:template match="element[behaviour/method = 'category']">
	<category>
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:if test="@parentId != 0">
			<xsl:attribute name="parentId"><xsl:value-of select="@parentId"/></xsl:attribute>
		</xsl:if>
		<xsl:value-of select="name"/>
	</category>
</xsl:template>


<xsl:template match="object[.//property[name = $currencyId]]">
	<currency id="{.//property[name = $currencyId]//value}">
		<xsl:choose>
			<xsl:when test=".//property[name = $is_cbrf]//value = 1">
				<xsl:attribute name="rate">CBRF</xsl:attribute>
				<xsl:if test=".//property[name = $plus]//value != ''">
					<xsl:attribute name="plus"><xsl:value-of select=".//property[name = $plus]//value"/></xsl:attribute>
				</xsl:if>
			</xsl:when>
			<xsl:when test=".//property[name = $rate]//value != ''">
				<xsl:attribute name="rate"><xsl:value-of select=".//property[name = $rate]//value"/></xsl:attribute>
			</xsl:when>
		</xsl:choose>
	</currency>
</xsl:template>

<xsl:template match="object">
	<offer>
		<xsl:choose>
			<xsl:when test=".//property[name = $vendor]//value != ''
							and .//property[name = $model]//value != ''">
				<xsl:attribute name="type">vendor.model</xsl:attribute>		
			</xsl:when>
			<xsl:when test=".//property[name = $isbn]//value != ''">
				<xsl:attribute name="type">book</xsl:attribute>		
			</xsl:when>
			<xsl:when test=".//property[name = $title]//value != ''">
				<xsl:attribute name="type">artist.title</xsl:attribute>
			</xsl:when>
			<xsl:when test=".//property[name = $place]//value != ''">
				<xsl:attribute name="type">event-ticket</xsl:attribute>
			</xsl:when>
			<xsl:when test=".//property[name = $transport]//value != ''">
				<xsl:attribute name="type">tour</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test=".//property[name = $store]//value &gt; 0">
				<xsl:attribute name="available">true</xsl:attribute>
			</xsl:when>
			<xsl:when test=".//property[name = $to-order]//value = 1
							and (.//property[name = $store]//value = 0
								or .//property[name = $store]//value = '')">
				<xsl:attribute name="available">false</xsl:attribute>
			</xsl:when>
		</xsl:choose>
		<url><xsl:value-of select="//element[@objectId = current()/@id]/link"/></url>
		<price><xsl:value-of select=".//property[name = $price]//value"/></price>
		<currencyId><xsl:value-of select="//object[@id = current()//property[name = $currency]//value/@id]//property[name = $currencyId]//value"/></currencyId>
		<xsl:for-each select="//element[@objectId = current()/@id]">
			<categoryId><xsl:value-of select="@parentId"/></categoryId>
		</xsl:for-each>
		<xsl:if test=".//property[name = $picture]//value != ''">
			<picture>
				<xsl:choose>
					<xsl:when test="substring(.//property[name = $picture]//value, 1, 1) = '.'">
						<xsl:value-of select="concat(/umicmsDump/domain ,substring-after(.//property[name = $picture]//value, './'))"/>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select=".//property[name = $picture]//value"/>
					</xsl:otherwise>
				</xsl:choose>
			</picture>
		</xsl:if>
		<xsl:if test=".//property[name = $deliveryIncluded]//value = 1">
			<xsl:element name="deliveryIncluded"><xsl:value-of select=".//property[name = $deliveryIncluded]//value"/></xsl:element>
		</xsl:if>
		<xsl:choose>
			<!-- vendor.model -->
			<xsl:when test="(.//property[name = $vendor]//value != '') and (.//property[name = $model]//value != '')">
				<xsl:if test=".//property[name = $typePrefix]//value != ''">
					<typePrefix><xsl:value-of select=".//property[name = $typePrefix]//value"/></typePrefix>
				</xsl:if>
				<vendor><xsl:value-of select=".//property[name = $vendor]//value"/></vendor>
				<model><xsl:value-of select=".//property[name = $model]//value"/></model>
			</xsl:when>
			<!-- book -->
			<xsl:when test=".//property[name = $isbn]//value != ''">
				<xsl:if test=".//property[name = $author]//value != ''">
					<author><xsl:value-of select=".//property[name = $author]//value"/></author>
				</xsl:if>
				<name><xsl:value-of select="name"/></name>
				<xsl:if test=".//property[name = $publisher]//value != ''">
					<publisher><xsl:value-of select=".//property[name = $publisher]//value"/></publisher>
				</xsl:if>
				<xsl:if test=".//property[name = $series]//value != ''">
					<series><xsl:value-of select=".//property[name = $series]//value"/></series>
				</xsl:if>
				<xsl:if test=".//property[name = $year]//value != ''">
					<year><xsl:value-of select=".//property[name = $year]//value"/></year>
				</xsl:if>
				<xsl:if test=".//property[name = $isbn]//value != ''">
					<ISBN><xsl:value-of select=".//property[name = $isbn]//value"/></ISBN>
				</xsl:if>
			</xsl:when>
			<!-- artist.title -->
			<xsl:when test=".//property[name = $title]//value != ''">
				<xsl:if test=".//property[name = $artist]//value != ''">
					<artist><xsl:value-of select=".//property[name = $artist]//value"/></artist>
				</xsl:if>
				<title><xsl:value-of select=".//property[name = $title]//value"/></title>
				<xsl:if test=".//property[name = $year]//value != ''">
					<year><xsl:value-of select=".//property[name = $year]//value"/></year>
				</xsl:if>
				<xsl:if test=".//property[name = $media]//value != ''">
					<media><xsl:value-of select=".//property[name = $media]//value"/></media>
				</xsl:if>
				<xsl:if test=".//property[name = $starring]//value != ''">
					<starring><xsl:value-of select=".//property[name = $starring]//value"/></starring>
				</xsl:if>
				<xsl:if test=".//property[name = $director]//value != ''">
					<director><xsl:value-of select=".//property[name = $director]//value"/></director>
				</xsl:if>
				<xsl:if test=".//property[name = $originalName]//value != ''">
					<originalName><xsl:value-of select=".//property[name = $originalName]//value"/></originalName>
				</xsl:if>
				<xsl:if test=".//property[name = $country]//value != ''">
					<country><xsl:value-of select=".//property[name = $country]//value"/></country>
				</xsl:if>
			</xsl:when>
			<!-- event-ticket -->
			<xsl:when test=".//property[name = $place]//value != ''">
				<name><xsl:value-of select="name"/></name>
				<place><xsl:value-of select=".//property[name = $place]//value"/></place>
				<xsl:if test="(.//property[name = $hall]//value != '') and (.//property[name = $hall-plan]//value != '')">
					<hall plan="{.//property[name = $hall-plan]//value}"><xsl:value-of select=".//property[name = $hall]//value"/></hall>
				</xsl:if>
				<xsl:if test=".//property[name = $hall_part]//value != ''">
					<hall_part><xsl:value-of select=".//property[name = $hall_part]//value"/></hall_part>
				</xsl:if>
				<xsl:if test=".//property[name = $date]//value != ''">
					<date><xsl:value-of select=".//property[name = $date]//value"/></date>
				</xsl:if>
				<xsl:if test=".//property[name = $is_premiere]//value != ''">
					<is_premiere><xsl:value-of select=".//property[name = $is_premiere]//value"/></is_premiere>
				</xsl:if>
				<xsl:if test=".//property[name = $is_kids]//value != ''">
					<is_kids><xsl:value-of select=".//property[name = $is_kids]//value"/></is_kids>
				</xsl:if>
			</xsl:when>
			<!-- tour -->
			<xsl:when test=".//property[name = $transport]//value != ''">
				<xsl:if test=".//property[name = $worldRegion]//value != ''">
					<worldRegion><xsl:value-of select=".//property[name = $worldRegion]//value"/></worldRegion>
				</xsl:if>
				<xsl:if test=".//property[name = $country]//value != ''">
					<country><xsl:value-of select=".//property[name = $country]//value"/></country>
				</xsl:if>
				<xsl:if test=".//property[name = $region]//value != ''">
					<region><xsl:value-of select=".//property[name = $region]//value"/></region>
				</xsl:if>
				<xsl:if test=".//property[name = $days]//value != ''">
					<days><xsl:value-of select=".//property[name = $days]//value"/></days>
				</xsl:if>
				<xsl:if test=".//property[name = $dataTour]//value != ''">
					<dataTour><xsl:value-of select=".//property[name = $dataTour]//value"/></dataTour>
				</xsl:if>
				<name><xsl:value-of select="name"/></name>
				<xsl:if test=".//property[name = $hotel_stars]//value != ''">
					<hotel_stars><xsl:value-of select=".//property[name = $hotel_stars]//value"/></hotel_stars>
				</xsl:if>
				<xsl:if test=".//property[name = $room]//value != ''">
					<room><xsl:value-of select=".//property[name = $room]//value"/></room>
				</xsl:if>
				<xsl:if test=".//property[name = $meal]//value != ''">
					<meal><xsl:value-of select=".//property[name = $meal]//value"/></meal>
				</xsl:if>
				<xsl:if test=".//property[name = $included]//value != ''">
					<included><xsl:value-of select=".//property[name = $included]//value"/></included>
				</xsl:if>
				<xsl:if test=".//property[name = $transport]//value != ''">
					<transport><xsl:value-of select=".//property[name = $transport]//value"/></transport>
				</xsl:if>
				<xsl:if test=".//property[name = $price_min]//value != ''">
					<price_min><xsl:value-of select=".//property[name = $price_min]//value"/></price_min>
				</xsl:if>
				<xsl:if test=".//property[name = $price_max]//value != ''">
					<price_max><xsl:value-of select=".//property[name = $price_max]//value"/></price_max>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<name><xsl:value-of select="name"/></name>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test=".//property[name = $description]//value != ''">
			<description><xsl:value-of select=".//property[name = $description]//value"/></description>
		</xsl:if>
		<xsl:if test=".//property[name = $sales_notes]//value != ''">
			<sales_notes><xsl:value-of select=".//property[name = $sales_notes]//value"/></sales_notes>
		</xsl:if>
	</offer>
</xsl:template>


</xsl:stylesheet>