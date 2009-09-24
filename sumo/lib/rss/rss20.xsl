<?xml version="1.0" ?>
<!--
  Feedster RSS 2.0 experimental stylesheet.  Please be patient as we work this out.
  modifications marked by ** 2004-10-19 by kinrowan; kinrowan@gmail.com
  further mods 2004-10-27 by kinrowan
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0">
<xsl:output method="html" />

<xsl:template match="/">
  <html>
  <head>
    <title><xsl:value-of select="/rss/channel/title"/></title>
  </head>
  <body>
    <xsl:attribute name="style">font-family: sans-serif; font-size: 10pt; background-color: #ffffff;</xsl:attribute>

    <xsl:apply-templates/>
  </body>
  <script><![CDATA[
function sr(s,f,r)
{
  var ret = s;
  var start = ret.indexOf(f);
  while (start>=0)
  {
    ret = ret.substring(0,start) + r + ret.substring(start+f.length,ret.length);
    start = ret.indexOf(f,start+r.length);
  }
  return ret;
}
function moz()
{
  var i, o, d, t;
  for( i = 1; i; i++)
  {
    d = "d_" + i;
    o = document.getElementById(d);
    if( o == null ) break;
    if( null != o.innerText ) break; // IE ok
    t = unescape( o.innerHTML );
    t = sr( t, "&gt;", ">" );
    t = sr( t, "&lt;", "<" );
    t = sr( t, "&amp;", "&" );
    o.innerHTML = t;
  }
}
moz();
  ]]></script>
  </html>
</xsl:template>

<xsl:template match="/rss/channel">
<a><xsl:attribute name="name">top</xsl:attribute></a>
<p>
<xsl:if test="image">
<a><xsl:attribute name="href"><xsl:value-of select="image/link"/></xsl:attribute>

<img>
<xsl:attribute name="border">0</xsl:attribute>
<xsl:attribute name="src"><xsl:value-of select="image/url"/></xsl:attribute>
<xsl:attribute name="alt"><xsl:value-of select="image/description"/></xsl:attribute>
<xsl:attribute name="title"><xsl:value-of select="image/title"/></xsl:attribute>
</img></a><br />
</xsl:if>
<p />
<!-- ** kinrowan.end	-->

Feed Title: <xsl:value-of select="title"/><br />
Updated on: <xsl:value-of select="lastBuildDate"/><br />
<xsl:choose>
<xsl:when test="count(item) = 1">
There is only 1 post shown in this feed.
</xsl:when>
</xsl:choose>
</p>

<HR/>
<p align='center'>
<i id='comment'>
<xsl:attribute name="style">color: #909595;</xsl:attribute>
<xsl:attribute name="alignment">center</xsl:attribute>
You are looking at an RSS feed.  It has been rendered as HTML using an XSL stylesheet. <BR/>
To see the underlying XML tags please select the "View Source" command in your browser.
</i>
</p>
<HR/>
<table>
<xsl:attribute name="border">0</xsl:attribute>

<xsl:attribute name="cellpadding">10</xsl:attribute>
<xsl:attribute name="cellspacing">10</xsl:attribute>
<xsl:attribute name="style">font-size: 10pt;</xsl:attribute>
<xsl:for-each select="item">

  <tr>
  <xsl:choose>
    <xsl:when test="position() mod 2 = 0">
      <xsl:attribute name="style">background: #ffffff; margin: 10 10 10 10; </xsl:attribute>

    </xsl:when>
    <xsl:otherwise>
      <xsl:attribute name="style">background: #EEEEEE; margin: 10 10 10 10; </xsl:attribute>
    </xsl:otherwise>
  </xsl:choose>
  <td><xsl:attribute name='style'>border: 1px solid #999999;  border-right: 1; border-bottom: 1;</xsl:attribute>
  <table>
  <xsl:attribute name="style">font-size: 8pt;</xsl:attribute>

  <xsl:attribute name="border">0</xsl:attribute>
  <xsl:attribute name="cellspacing">0</xsl:attribute>
  <xsl:attribute name="cellpadding">0</xsl:attribute>
  <xsl:attribute name="width">100%</xsl:attribute>
  <tr><td>

  <xsl:value-of select="position()"/> of <xsl:value-of select="count(/rss/channel/item)"/>
  (Published: <xsl:value-of select="pubDate"/>)
  </td>

</tr>
  <tr>
    <td>

		Title:
		<a>
		<xsl:attribute name="href">
			<xsl:value-of select="link" />
		</xsl:attribute>
		<xsl:value-of select="title"/>
		</a>
		<br />

	<!--** kinrowan.end		   -->
    </td>
  </tr>
  </table>
  <br />
  <div>
  <xsl:attribute name="id">d_<xsl:value-of select="position()"/></xsl:attribute>

  <xsl:value-of disable-output-escaping="yes" select="description"/>
  </div><br />
  </td></tr>

</xsl:for-each>
</table>

<p>
<xsl:value-of select="copyright"/>
</p>
</xsl:template>

</xsl:stylesheet>
