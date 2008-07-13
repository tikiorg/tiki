<?xml version="1.0" encoding="ISO-8859-1"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
<xsl:output method = "html" version = "4.0" encoding="UTF-8" indent = "no" />
<xsl:strip-space elements = "*" />

<xsl:variable name="markerNormal">&#9658;</xsl:variable>
<xsl:variable name="markerComment">&#8810;</xsl:variable>
<xsl:variable name="markerLink">&#9788;</xsl:variable>

<xsl:template match = "/opml" >
<html>
  <link rel="stylesheet" href="lib/rss/opml.css" />
  <head><title><xsl:value-of select="head/title" /></title></head>
  <body>

  <span class="title"><xsl:value-of select="head/title" /></span>

    <div id="outlineRoot" class="outlineRoot">
        <xsl:for-each select="head/*" >
        <span class="outlineAttribute" title="{name()}"><xsl:value-of select="." /></span>
        </xsl:for-each>
  	<xsl:apply-templates select="body"/>
    </div>
    <span id="markerNormal" style="display:none"><xsl:value-of select="$markerNormal" /></span>
    <span id="markerComment" style="display:none"><xsl:value-of select="$markerComment" /></span>
    <span id="markerLink" style="display:none"><xsl:value-of select="$markerLink" /></span>
  </body>
</html>
</xsl:template>

<xsl:template match = "outline" >
  <div class="outline">
       <xsl:attribute name="style">
         <xsl:if test="parent::outline">display:none</xsl:if>       
       </xsl:attribute>
       <xsl:for-each select="@*[name() !='text']" >
       <span class="outlineAttribute" title="{name()}"><xsl:value-of select="." /></span>
       </xsl:for-each>
       <span>
            <xsl:attribute name="class">
              <xsl:choose>
                <xsl:when test="./*">markerClosed</xsl:when>
                <xsl:when test="contains(@url,'.opml') or contains(@url,'.OPML')">markerClosed</xsl:when>
                <xsl:otherwise>markerOpen</xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:choose>
            	<xsl:when test="@isComment = 'true'"><xsl:value-of select="$markerComment" /></xsl:when>
            	<xsl:when test="@type = 'link' and not(contains(@url,'.opml') or contains(@url,'.OPML'))"><xsl:value-of select="$markerLink" /></xsl:when>
            	<xsl:otherwise><xsl:value-of select="$markerNormal" /></xsl:otherwise>
            </xsl:choose>
       </span>
       <span class="outlineText">
		<a>
		<xsl:attribute name="href">
			<xsl:value-of select="@url" />
		</xsl:attribute>
		<xsl:value-of select="@text"/>
		</a>
       </span>
       <xsl:apply-templates />
  </div>
</xsl:template>

</xsl:stylesheet>
