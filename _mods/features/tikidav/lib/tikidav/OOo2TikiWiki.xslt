<?xml version="1.0" encoding="ISO-8859-1"?>
<!-- 

 OOo2TikiWiki is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.
  
 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

 Derived OOo_TWiki.xsl developed by Brad Dixon (twiki.20.bdixon@spamgourmet.com) 
 Modified, improved and adapted to UniWakka by Andrea Rossato
 Modified, adapted to TikiWiki by Javier Reyes (jreyes@escire.com)
-->

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:office="http://openoffice.org/2000/office" xmlns:style="http://openoffice.org/2000/style" xmlns:text="http://openoffice.org/2000/text" xmlns:table="http://openoffice.org/2000/table" xmlns:draw="http://openoffice.org/2000/drawing" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:number="http://openoffice.org/2000/datastyle" xmlns:svg="http://www.w3.org/2000/svg" xmlns:chart="http://openoffice.org/2000/chart" xmlns:dr3d="http://openoffice.org/2000/dr3d" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="http://openoffice.org/2000/form" xmlns:script="http://openoffice.org/2000/script">
	<xsl:output method="text" />
	<!-- Catch the non-content document sections -->
	<xsl:template match="/XML"/>
	<xsl:template match="/office:document/office:meta"/>
	<xsl:template match="/office:document/office:settings"/>
	<xsl:template match="/office:document/office:document-styles"/>
	<xsl:template match="/office:document/office:font-decls"/>
	<xsl:template match="/office:document/office:styles"/>
	<xsl:template match="/office:document/office:master-styles"/>
	<!-- Formats the text sections according to the style name -->
	<xsl:template name="style-font">
		<xsl:param name="style"/>
		<xsl:variable name="font-style" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:font-style"/>
		<xsl:variable name="font-weight" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:font-weight"/>
		<xsl:variable name="font-underline" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-underline"/>
		<xsl:variable name="centered" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:text-align"/>
		<xsl:variable name="indented" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:margin-left"/>
		<xsl:variable name="supscript" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-position"/>
		<xsl:variable name="subscript" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-position"/>
		<xsl:variable name="linethrough" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-crossing-out"/>

<!-- debug style
		<xsl:text>[</xsl:text>
		<xsl:value-of select="$subscript"/><xsl:text>, </xsl:text>
		<xsl:value-of select="$supscript"/><xsl:text>, </xsl:text>
		<xsl:value-of select="$linethrough"/>
                <xsl:value-of select="$font-weight"/>
                <xsl:value-of select="$font-style"/>
                <xsl:value-of select="$font-underline"/>
		<xsl:text>]</xsl:text>
-->
		<xsl:choose>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and $font-underline='none'">__''</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and $font-underline='single'">__''===</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='normal' and $font-underline='single'">__===</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='italic' and $font-underline='single'">''===</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='normal' and $font-underline='single'">===</xsl:when>
			<xsl:when test="$font-weight='bold' and not($font-style) and not($font-underline)">__</xsl:when>
			<xsl:when test="not($font-weight) and $font-style='italic' and not($font-underline)">''</xsl:when>
			<xsl:when test="not($font-weight) and not($font-style) and $font-underline='single'">===</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='normal' and not($font-underline)">__</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='italic' and not($font-underline)">''</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and not($font-underline)">__''</xsl:when>
			<xsl:when test="$font-weight='bold' and not($font-style) and $font-underline='single'">__===</xsl:when>
			<xsl:when test="not($font-weight) and $font-style='italic' and $font-underline='single'">''===</xsl:when>
			<xsl:when test="$centered='center'">::</xsl:when>
			<xsl:when test="contains($indented, 'cm')">
				<xsl:choose>
					<xsl:when test="translate($indented, 'cm', '') &gt; 0 and translate($indented, 'cm', '') &lt; 2">
					<xsl:text disable-output-escaping="yes">   </xsl:text>
					</xsl:when>
					<xsl:when test="translate($indented, 'cm', '') &gt; 2 and translate($indented, 'cm', '') &lt; 3.5">
					<xsl:text disable-output-escaping="yes">      </xsl:text>
					</xsl:when>
					<xsl:when test="translate($indented, 'cm', '') &gt; 3.5">
					<xsl:text disable-output-escaping="yes">         </xsl:text>
					</xsl:when>
					</xsl:choose>
			</xsl:when>
			<xsl:when test="contains($indented, 'inch')">
				<xsl:choose>
					<xsl:when test="translate($indented, 'inch', '') &gt; 0 and translate($indented, 'inch', '') &lt; 0.5">
					<xsl:text disable-output-escaping="yes">   </xsl:text>
					</xsl:when>
					<xsl:when test="translate($indented, 'inch', '') &gt; 0.5 and translate($indented, 'inch', '') &lt; 1">
					<xsl:text disable-output-escaping="yes">      </xsl:text>
					</xsl:when>
					<xsl:when test="translate($indented, 'inch', '') &gt; 1">
					<xsl:text disable-output-escaping="yes">         </xsl:text>
					</xsl:when>
				</xsl:choose>
			</xsl:when>
			<xsl:when test="contains($supscript, 'sup')">^^</xsl:when>
			<xsl:when test="contains($subscript, 'sub')">,,</xsl:when>
			<xsl:when test="contains($linethrough, 'line')">--</xsl:when>
		</xsl:choose>
	</xsl:template>

	<xsl:template name="style-font-close">
		<xsl:param name="style"/>
		<xsl:variable name="font-style" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:font-style"/>
		<xsl:variable name="font-weight" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:font-weight"/>
		<xsl:variable name="font-underline" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-underline"/>
		<xsl:variable name="centered" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@fo:text-align"/>
		<xsl:variable name="supscript" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-position"/>
		<xsl:variable name="subscript" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-position"/>
		<xsl:variable name="linethrough" select="//office:automatic-styles/style:style[@style:name=$style]/style:properties/@style:text-crossing-out"/>

<!-- style debug
		<xsl:text>[</xsl:text>
		<xsl:value-of select="$font-weight"/><xsl:text>, </xsl:text>
		<xsl:value-of select="$font-style"/><xsl:text>, </xsl:text>
		<xsl:value-of select="$font-underline"/>
		<xsl:text>]</xsl:text>
-->
		<xsl:choose>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and $font-underline='none'">''__</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and $font-underline='single'">===''__</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='normal' and $font-underline='single'">===__</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='italic' and $font-underline='single'">===''</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='normal' and $font-underline='single'">===</xsl:when>
			<xsl:when test="$font-weight='bold' and not($font-style) and not($font-underline)">__</xsl:when>
			<xsl:when test="not($font-weight) and $font-style='italic' and not($font-underline)">''</xsl:when>
			<xsl:when test="not($font-weight) and not($font-style) and $font-underline='single'">===</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='normal' and not($font-underline)">__</xsl:when>
			<xsl:when test="$font-weight='normal' and $font-style='italic' and not($font-underline)">''</xsl:when>
			<xsl:when test="$font-weight='bold' and $font-style='italic' and not($font-underline)">''__</xsl:when>
			<xsl:when test="$font-weight='bold' and not($font-style) and $font-underline='single'">===__</xsl:when>
			<xsl:when test="not($font-weight) and $font-style='italic' and $font-underline='single'">===''</xsl:when>
			<xsl:when test="$centered='center'">::</xsl:when>
			<xsl:when test="contains($supscript, 'sup')">^^</xsl:when>
			<xsl:when test="contains($subscript, 'sub')">,,</xsl:when>
			<xsl:when test="contains($linethrough, 'line')">--</xsl:when>
		</xsl:choose>
	</xsl:template>

	<!-- Text blocks -->
	<xsl:template match="//text:p">
		<xsl:variable name="cur-style-name">
			<xsl:value-of select="@text:style-name"/>
		</xsl:variable>
		<xsl:variable name="text" select="."/>
		<xsl:choose>
                <xsl:when test="$text!=''">
			<xsl:call-template name="style-font">
				<xsl:with-param name="style">
					<xsl:value-of select="$cur-style-name"/>
				</xsl:with-param>
			</xsl:call-template>
			<xsl:apply-templates/>
			<xsl:call-template name="style-font-close">
				<xsl:with-param name="style">
					<xsl:value-of select="$cur-style-name"/>
				</xsl:with-param>
			</xsl:call-template>
			<!-- these are all newline rules -->
			<xsl:choose>
                                <xsl:when test="ancestor::table:table-cell"></xsl:when>
				<!-- we shouldn't add a newline for elements inside a table 
				<xsl:when test="ancestor::table:table-cell and not(following-sibling::text:p)"></xsl:when>
				but we would want a new between two text:p in a cell 
				<xsl:when test="ancestor::table:table-cell and following-sibling::text:p">
				<xsl:text disable-output-escaping="yes">
</xsl:text>
				</xsl:when>-->
				<!-- we do want a single newline at the end of a list item -->
				<xsl:when test="ancestor::text:list-item and following::text:list-item">
					<xsl:text disable-output-escaping="yes">
</xsl:text>
				</xsl:when>
				<!-- and double at the end of the list (el doble salto se estaba generando para todos los parrafos posteriores a una lista)
				<xsl:when test="preceding-sibling::text:ordered-list or preceding-sibling::text:unordered-list">
					<xsl:text disable-output-escaping="yes">

</xsl:text>
				</xsl:when>-->
				<xsl:when test="ancestor::text:align='center'">
					<xsl:text disable-output-escaping="yes">
					</xsl:text>
				</xsl:when>

				<xsl:otherwise>
					<xsl:text disable-output-escaping="yes">
</xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:when test="$text=''">
			<xsl:apply-templates/><xsl:text disable-output-escaping="yes">
</xsl:text></xsl:when></xsl:choose>
	</xsl:template>
	<!-- span formatting -->
	<xsl:template match="//text:span">
        		<xsl:variable name="cur-style-name">
			<xsl:value-of select="@text:style-name"/>
		</xsl:variable>
		<xsl:variable name="text" select="."/>
                <xsl:choose>
                    <xsl:when test="$text!=''">
                    <xsl:call-template name="style-font">
				<xsl:with-param name="style">
					<xsl:value-of select="$cur-style-name"/>
				</xsl:with-param>
			</xsl:call-template>
			<xsl:apply-templates/>
			<xsl:call-template name="style-font-close">
				<xsl:with-param name="style">
					<xsl:value-of select="$cur-style-name"/>
				</xsl:with-param>
			</xsl:call-template>
                    </xsl:when>		
                    <xsl:otherwise>
                    <xsl:text disable-output-escaping="yes"> </xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
	</xsl:template>

	<!-- Tables -->
	<xsl:template match="//table:table">
		<!--<xsl:variable name="table-name" select="@table:name"/>-->
		<xsl:text disable-output-escaping="yes">||</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">||
</xsl:text>
	</xsl:template>
	<!-- Table header rows -->
	<xsl:template match="//table:table-header-rows">
	<xsl:choose>
                <xsl:when test="following-sibling::table:table-row">
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>	
	</xsl:when>		
	<xsl:otherwise>
 		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text> 
	</xsl:otherwise>
	</xsl:choose>

	</xsl:template>
	<!-- Table rows -->
	<xsl:template match="//table:table-row">
	<xsl:choose>
                <xsl:when test="following-sibling::table:table-row">
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>	
	</xsl:when>		
	<xsl:otherwise>
 		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text> 
	</xsl:otherwise>
	</xsl:choose>

	</xsl:template>
	<!-- Table cells -->
	<xsl:template match="//table:table//table:table-row/table:table-cell">
		<xsl:apply-templates/>
		<xsl:if test="position()!=last()">
			<xsl:text disable-output-escaping="yes">|</xsl:text>
      		</xsl:if>
	</xsl:template>
	<!-- Handles horizontally merged cells -->
	<xsl:template match="//table:covered-table-cell">
		<xsl:text disable-output-escaping="yes">|</xsl:text>
	</xsl:template>
	<!-- Table of Contents -->
	<xsl:template match="//text:table-of-content">
		<xsl:text disable-output-escaping="yes">{TOC}
</xsl:text>
	</xsl:template>
	<!-- Headings -->
	<xsl:template match="//text:h[@text:level='1']">
		<xsl:text disable-output-escaping="yes">!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='2']">
		<xsl:text disable-output-escaping="yes">!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='3']">
		<xsl:text disable-output-escaping="yes">!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='4']">
		<xsl:text disable-output-escaping="yes">!!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='5']">
		<xsl:text disable-output-escaping="yes">!!!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='6']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='7']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='8']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='9']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:h[@text:level='10']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>

	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 1']">
		<xsl:text disable-output-escaping="yes">!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>

	<xsl:template match="//text:p[@text:style-name='Heading 2']">
		<xsl:text disable-output-escaping="yes">!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 3']">
		<xsl:text disable-output-escaping="yes">!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 4']">
		<xsl:text disable-output-escaping="yes">!!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 5']">
		<xsl:text disable-output-escaping="yes">!!!!!</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">
</xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 6']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 7']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">!</xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 8']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 9']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>
	<xsl:template match="//text:p[@text:style-name='Heading 10']">
		<xsl:text disable-output-escaping="yes"></xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes"></xsl:text>
	</xsl:template>


	<!-- Footnote -->
	<xsl:template match="//text:footnote-body">
		<xsl:text disable-output-escaping="yes">{{fn </xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">}}</xsl:text>
	</xsl:template>
	<xsl:template match="//text:footnote-citation">
	</xsl:template>

	<!-- my styles -->
	<xsl:template match="//text:span[@text:style-name='emph']">
		<xsl:text disable-output-escaping="yes">''</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">''</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='italic']">
		<xsl:text disable-output-escaping="yes">''</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">''</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='underline']">
		<xsl:text disable-output-escaping="yes">===</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">===</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='textbf']">
		<xsl:text disable-output-escaping="yes">__</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">__</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='linethrough']">
		<xsl:text disable-output-escaping="yes">--</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">--</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='subscript']">
		<xsl:text disable-output-escaping="yes">,,</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">,,</xsl:text>
	</xsl:template>
	<xsl:template match="//text:span[@text:style-name='supscript']">
		<xsl:text disable-output-escaping="yes">^^</xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">^^</xsl:text>
	</xsl:template>

	<!-- biblio citations -->
	<xsl:template match="//text:span[@text:style-name='bibliocit']">
		<xsl:text disable-output-escaping="yes">[[cite </xsl:text>
		<xsl:apply-templates/>
		<xsl:text disable-output-escaping="yes">]]</xsl:text>
	</xsl:template>

	<!-- links -->
	<xsl:template match="//text:a">
		<xsl:variable name="link">
			<xsl:value-of select="@xlink:href"/>
		</xsl:variable>
		<xsl:variable name="link-text">
			<xsl:value-of select="."/>
		</xsl:variable>
		<xsl:choose>
		<xsl:when test="$link = $link-text">
			<xsl:apply-templates/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:text disable-output-escaping="yes">[</xsl:text>
			<xsl:value-of select="$link"/>
			<xsl:text disable-output-escaping="yes">|</xsl:text>
			<xsl:apply-templates/>
			<xsl:text disable-output-escaping="yes">]</xsl:text>
		</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

	<!-- lists -->
	<xsl:template match="//text:unordered-list/text:list-item">
		<xsl:variable name="level">
			<xsl:value-of select="count(ancestor::text:unordered-list | ancestor::text:ordered-list)"/>
		</xsl:variable>
		<xsl:if test="$level=1">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">*</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=2">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">**</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=3">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">***</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=4">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">****</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=5">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">*****</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=6">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">******</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=7">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">*******</xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:apply-templates/>
	</xsl:template>

	<xsl:template match="//text:ordered-list/text:list-item">
		<xsl:variable name="level">
			<xsl:value-of select="count(ancestor::text:ordered-list | ancestor::text:unordered-list)"/>
		</xsl:variable>
		<xsl:variable name="number-type" select="1"/>
		<xsl:variable name="num-type">
		<xsl:choose>
			<xsl:when test="../@text:style-name='upper-roman' or //office:automatic-styles/text:list-style[text:list-level-style-number[@text:level = $level and @style:num-format = 'I']]">
				<xsl:text>#</xsl:text>
			</xsl:when>
			<xsl:when test="../@text:style-name='lower-roman' or //office:automatic-styles/text:list-style[text:list-level-style-number[@text:level = $level and @style:num-format = 'i']]">
				<xsl:text>i</xsl:text>
			</xsl:when>
			<xsl:when test="../@text:style-name='upper-alpha' or //office:automatic-styles/text:list-style[text:list-level-style-number[@text:level = $level and @style:num-format = 'A']]">
				<xsl:text>#</xsl:text>
			</xsl:when>
			<xsl:when test="../@text:style-name='lower-alpha' or //office:automatic-styles/text:list-style[text:list-level-style-number[@text:level = $level and @style:num-format = 'a']]">
				<xsl:text>#</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text>#</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		</xsl:variable>
		<xsl:if test="$level=1">
			<xsl:choose>
				<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
				</xsl:when>
				<xsl:otherwise>
					<xsl:text disable-output-escaping="yes">   </xsl:text>
					<xsl:value-of select="$num-type"/>
					<xsl:text disable-output-escaping="yes">) </xsl:text>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=2">

			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">

			</xsl:when>

			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">      </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=3">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">

			</xsl:when>

			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">         </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>

		</xsl:if>
		<xsl:if test="$level=4">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">            </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=5">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">               </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=6">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">                  </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="$level=7">
			<xsl:choose>
			<xsl:when test="not(following-sibling::text:list-item) and not(text:p | text:a)">
			</xsl:when>
			<xsl:otherwise>
				<xsl:text disable-output-escaping="yes">                     </xsl:text>
				<xsl:value-of select="$num-type"/>
				<xsl:text disable-output-escaping="yes">) </xsl:text>
			</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:apply-templates/>
	</xsl:template>

<xsl:template match="text:s">
<xsl:text disable-output-escaping="yes"> </xsl:text></xsl:template>

<xsl:template match="draw:a">
<xsl:apply-templates/>
</xsl:template>

<xsl:template match="draw:image"><xsl:variable name="link">
    <xsl:choose>
        <xsl:when test="parent::draw:a"><xsl:value-of select="../@xlink:href"/>
        </xsl:when>
        <xsl:otherwise></xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="width">
    <xsl:choose>
        <xsl:when test="contains(@svg:width, 'cm')">
             <xsl:value-of select="ceiling(translate(@svg:width, 'cm', '')*28.38)"/>
        </xsl:when>
        <xsl:when test="contains(@svg:width, 'inch')">
        <!-- TODO -->
            <xsl:value-of select="@svg:width"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="@svg:width"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="height">
    <xsl:choose>
        <xsl:when test="contains(@svg:height, 'cm')">
             <xsl:value-of select="ceiling(translate(@svg:height, 'cm', '')*28.38)"/>
        </xsl:when>
        <xsl:when test="contains(@svg:height, 'inch')">
        <!-- TODO -->
            <xsl:value-of select="@svg:height"/>
        </xsl:when>
        <xsl:otherwise>
            <xsl:value-of select="@svg:height"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:variable>
<xsl:variable name="src">
<xsl:choose>
<xsl:when test="starts-with(@xlink:href,'#')">
    <xsl:value-of select="concat('./tiki-dowload_wiki_attachmentOOo.php?attName=',substring(@xlink:href,2))"/>
    </xsl:when>
<xsl:when test="starts-with(@xlink:href,'../')">
    <xsl:value-of select="concat('./tiki-dowload_wiki_attachmentOOo.php?attName=',substring(@xlink:href,2))"/>
</xsl:when>
<xsl:otherwise>
<xsl:value-of select="@xlink:href"/>
</xsl:otherwise>
</xsl:choose>
</xsl:variable>{img src=<xsl:value-of select="$src"/> desc=<xsl:value-of select="@draw:name"/><xsl:if test="@svg:width"> width=<xsl:value-of select="$width"/></xsl:if><xsl:if test="@svg:height"> height=<xsl:value-of select="$height"/></xsl:if><xsl:if test="$link"> link=<xsl:value-of select="$link"/></xsl:if>}</xsl:template>        
</xsl:stylesheet>
