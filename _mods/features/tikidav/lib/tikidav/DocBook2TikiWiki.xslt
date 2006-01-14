<?xml version="1.0" encoding="UTF-8"?>
<!-- 

 DocBook2TikiWiki is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
  
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 Author: Javier Reyes (jreyes@escire.com)
 date: 12/02/2004
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">
<xsl:output method="text" encoding="UTF-8"/>

<xsl:strip-space elements="*"/>	
<xsl:decimal-format name="staff" digit="D" />
<xsl:template match="/">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="article">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="subtitle">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="title">
<xsl:choose>
	<xsl:when test="parent::figure">
	</xsl:when>
	<xsl:when test="parent::table">
	</xsl:when>
	<xsl:when test="parent::informaltable">
			<xsl:apply-templates/>
	</xsl:when>
<xsl:otherwise>				
<xsl:choose>	
<xsl:when test="parent::sect1">
!<xsl:apply-templates/></xsl:when>
<xsl:when test="parent::sect2">
!!<xsl:apply-templates/></xsl:when>
<xsl:when test="parent::sect3">
!!!<xsl:apply-templates/></xsl:when>
<xsl:when test="parent::sect4">
!!!!<xsl:apply-templates/></xsl:when>
<xsl:when test="parent::sect5">
!!!!!<xsl:apply-templates/></xsl:when>
<xsl:when test="parent::appendix">
!<xsl:apply-templates/></xsl:when>
<xsl:otherwise>
-=<xsl:apply-templates/>=-
</xsl:otherwise>
</xsl:choose>
</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="articleinfo">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="appendix">
<xsl:apply-templates/>
</xsl:template>

<!--
<xsl:template match="author">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="firstname">
	 <xsl:element name="text:variable-set">
		 <xsl:attribute name="text:name">
		 	<xsl:if test="ancestor::articleinfo/author">
		 		<xsl:text disable-output-escaping="yes">articleinfo.author</xsl:text><xsl:value-of select="count(parent::author[preceding-sibling::author])"/><xsl:text disable-output-escaping="yes">.firstname</xsl:text><xsl:value-of select="count(preceding-sibling::firstname)"/>
		 	</xsl:if>
		</xsl:attribute>
		<xsl:apply-templates/>
	</xsl:element>

</xsl:template>-->

<xsl:template match="articleinfo/title">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="articleinfo/subtitle">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="articleinfo/edition">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="articleinfo/releaseinfo">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="author/firstname">
<xsl:apply-templates/>
</xsl:template>



<xsl:template match="articleinfo/copyright/year">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="authorgroup">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="articleinfo/copyright/holder">
<xsl:apply-templates/>
</xsl:template>

<xsl:template name="affiliation">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="author/affiliation/address">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="author/affiliation/orgname">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="author/surname">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="para">
<xsl:choose><xsl:when test="ancestor-or-self::informaltable"><xsl:apply-templates/>
</xsl:when>
<xsl:when test="ancestor-or-self::table">
<xsl:apply-templates/>
</xsl:when>
<xsl:when test="ancestor-or-self::listitem">
<xsl:apply-templates/>
</xsl:when>
<xsl:otherwise>
<xsl:text disable-output-escaping="yes">
</xsl:text>
<xsl:apply-templates/>			
</xsl:otherwise>
</xsl:choose>
        
</xsl:template>

<xsl:template match="section">
!<xsl:apply-templates/>
</xsl:template>

<xsl:template match="abstract">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="sect1">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="sect2">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="sect3">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="sect4">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="sect5">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="informaltable">
||<xsl:apply-templates/>||
</xsl:template>


<xsl:template match="table">
||<xsl:apply-templates/>||
</xsl:template>

<xsl:template match="tgroup">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="indexterm">
</xsl:template>

<xsl:template match="thead">
<xsl:apply-templates />
</xsl:template>

<xsl:template match="tbody">
<xsl:apply-templates />
</xsl:template>

<xsl:template match="row">
<xsl:choose>
<xsl:when test="following-sibling::row">
<xsl:apply-templates/><xsl:text disable-output-escaping="yes">
</xsl:text>
</xsl:when>
<xsl:when test="following::tbody">
<xsl:apply-templates/><xsl:text disable-output-escaping="yes">
</xsl:text>
</xsl:when>
<xsl:otherwise>
<xsl:apply-templates/></xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="entry">
<xsl:choose>
<xsl:when test="following-sibling::entry">
<xsl:apply-templates/>|</xsl:when>
<xsl:otherwise>
<xsl:apply-templates/></xsl:otherwise>

</xsl:choose>

</xsl:template>


<xsl:template match="figure">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="itemizedlist">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="variablelist">
<xsl:apply-templates />
</xsl:template>

<xsl:template match="orderedlist">
<xsl:apply-templates/>	
</xsl:template>

<xsl:template match="term">
<xsl:apply-templates />
</xsl:template>


<xsl:template match="itemizedlist/listitem">
<xsl:variable name="level">
<xsl:value-of select="count(ancestor::itemizedlist | ancestor::orderedlist)"/>
</xsl:variable>
<xsl:if test="$level=1"><xsl:text disable-output-escaping="yes">
*</xsl:text></xsl:if>
<xsl:if test="$level=2"><xsl:text disable-output-escaping="yes">
**</xsl:text></xsl:if>
<xsl:if test="$level=3"><xsl:text disable-output-escaping="yes">
***</xsl:text></xsl:if>
<xsl:if test="$level=4"><xsl:text disable-output-escaping="yes">
****</xsl:text></xsl:if>
<xsl:if test="$level=5"><xsl:text disable-output-escaping="yes">
*****</xsl:text></xsl:if>
<xsl:if test="$level=6"><xsl:text disable-output-escaping="yes">
******</xsl:text></xsl:if>
<xsl:if test="$level=7"><xsl:text disable-output-escaping="yes">
*******</xsl:text></xsl:if>
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="orderedlist/listitem">
<xsl:variable name="level">
<xsl:value-of select="count(ancestor::itemizedlist | ancestor::orderedlist)"/>
</xsl:variable>
<xsl:if test="$level=1"><xsl:text disable-output-escaping="yes">
#</xsl:text></xsl:if>
<xsl:if test="$level=2"><xsl:text disable-output-escaping="yes">
##</xsl:text></xsl:if>
<xsl:if test="$level=3"><xsl:text disable-output-escaping="yes">
###</xsl:text></xsl:if>
<xsl:if test="$level=4"><xsl:text disable-output-escaping="yes">
####</xsl:text></xsl:if>
<xsl:if test="$level=5"><xsl:text disable-output-escaping="yes">
#####</xsl:text></xsl:if>
<xsl:if test="$level=6"><xsl:text disable-output-escaping="yes">
######</xsl:text></xsl:if>
<xsl:if test="$level=7"><xsl:text disable-output-escaping="yes">
#######</xsl:text></xsl:if>
<xsl:apply-templates/>
</xsl:template>

<!--  end of lists-->

<xsl:template match="menuchoice">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="guimenuitem">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="guibutton">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="guisubmenu">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="quote">
===<xsl:apply-templates/>===
</xsl:template>

<xsl:template match="emphasis[@role='bold']">
__<xsl:apply-templates/>__
</xsl:template>

<xsl:template match="emphasis">
''<xsl:apply-templates/>''
</xsl:template>


<xsl:template match="guimenu">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="guisubmenu">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="guilabel">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="guibutton">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="keycap">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="keysym">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="keycombo">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="command">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="application">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="filename">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="systemitem">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="computeroutput">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="inlinegraphic">
{img src="<xsl:value-of select="@fileref"/>"<xsl:if test="@width"> width=<xsl:value-of select="@width"/></xsl:if><xsl:if test="@height"> height=<xsl:value-of select="@height"/></xsl:if><xsl:if test="@align"> align=<xsl:value-of select="@align"/></xsl:if>}
</xsl:template> 


<xsl:template match="footnote">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="highlight">__<xsl:apply-templates/>__</xsl:template>

<xsl:template match="ulink">[<xsl:value-of select="@url"/>|<xsl:apply-templates/>]</xsl:template>

<xsl:template match="link">[#<xsl:value-of select="@linkend"/>|<xsl:value-of select="@endterm"/><xsl:apply-templates/>]</xsl:template>

<xsl:template match="olink">[<xsl:value-of select="@targetdocent"/>|<xsl:apply-templates/>]</xsl:template>

<xsl:template match="note">^<xsl:apply-templates/>^</xsl:template>

<xsl:template match="imageobject">
{img src="<xsl:value-of select="./imagedata/@fileref"/>" desc="<xsl:value-of select="./objectinfo/title"/>"<xsl:if test="./imagedata/@width"> width=<xsl:value-of select="./imagedata/@width"/></xsl:if><xsl:if test="./imagedata/@height"> height=<xsl:value-of select="./imagedata/@height"/></xsl:if><xsl:if test="./imagedata/@align"> align=<xsl:value-of select="./imagedata/@align"/></xsl:if>}
</xsl:template>

<xsl:template match="textobject">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="caption">
<xsl:apply-templates/>
</xsl:template>


<xsl:template match="imagedata">
</xsl:template>

<xsl:template match="audioobject">
{audio src="<xsl:value-of select="./audiodata/@fileref"/>" desc="<xsl:value-of select="./objectinfo/title"/>"}
</xsl:template>

<xsl:template match="remark">__<xsl:apply-templates/>__</xsl:template>

<xsl:template match="mediaobject">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="superscript">
^^<xsl:apply-templates/>^^
</xsl:template>

<xsl:template match="subscript">
^_<xsl:apply-templates/>_^
</xsl:template>

</xsl:stylesheet>
