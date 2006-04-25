<office:document-content xmlns:office="http://openoffice.org/2000/office" xmlns:style="http://openoffice.org/2000/style" xmlns:text="http://openoffice.org/2000/text" xmlns:table="http://openoffice.org/2000/table" xmlns:draw="http://openoffice.org/2000/drawing" xmlns:fo="http://www.w3.org/1999/XSL/Format" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:number="http://openoffice.org/2000/datastyle" xmlns:svg="http://www.w3.org/2000/svg" xmlns:chart="http://openoffice.org/2000/chart" xmlns:dr3d="http://openoffice.org/2000/dr3d" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="http://openoffice.org/2000/form" xmlns:script="http://openoffice.org/2000/script" office:class="spreadsheet" office:version="1.0">
  <office:script /> 
<office:font-decls>
  <style:font-decl style:name="Arial Unicode MS" fo:font-family="'Arial Unicode MS'" style:font-pitch="variable" /> 
  <style:font-decl style:name="Tahoma" fo:font-family="Tahoma" style:font-pitch="variable" /> 
  <style:font-decl style:name="Arial" fo:font-family="Arial" style:font-family-generic="swiss" style:font-pitch="variable" /> 
  </office:font-decls>
<office:automatic-styles>
<style:style style:name="co1" style:family="table-column">
  <style:properties fo:break-before="auto" style:column-width="2.267cm" /> 
  </style:style>
<style:style style:name="ro1" style:family="table-row">
  <style:properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true" /> 
  </style:style>
<style:style style:name="ta1" style:family="table" style:master-page-name="Default">
  <style:properties table:display="true" /> 
  </style:style>
  </office:automatic-styles>
<office:body>

{section name=ii loop=$periods}
	<table:table table:name="{$periods[ii].name}" table:style-name="ta1">
	  <table:table-column table:style-name="co1" table:number-columns-repeated="4" table:default-cell-style-name="Default" /> 
	<table:table-row table:style-name="ro1">
	  <table:table-cell />
	  {section name=i loop=$assignments}
	  	{if $assignments[i].periodId == $periods[ii].periodId}
		  	<table:table-cell>
		      	  <text:p>{$assignments[i].name}</text:p> 
		  	</table:table-cell>
		{/if}
	  {/section}
	</table:table-row>
	{foreach name=usergrades key=userid item=usergrade from=$gradebook}
		<table:table-row table:style-name="ro1">
		
		<table:table-cell>
		  <text:p>{$userid}</text:p> 
		</table:table-cell>
		
		{foreach name=userassign key=assigId item=assignment from=$assignments}
		     {if $assignment.periodId == $periods[ii].periodId}      
			  <table:table-cell table:value-type="float" table:value="{$usergrade[$assignment.assignmentId].grade}">
			   <text:p>{$usergrade[$assignment.assignmentId].grade}</text:p> 
			  </table:table-cell>
		     {/if}
		{/foreach}
		</table:table-row>
	{/foreach}
	</table:table>
{/section}


<table:table table:name="Sheet2" table:style-name="ta1">
  <table:table-column table:style-name="co1" table:default-cell-style-name="Default" /> 
<table:table-row table:style-name="ro1">
  <table:table-cell /> 
  </table:table-row>
  </table:table>
<table:table table:name="Sheet3" table:style-name="ta1">
  <table:table-column table:style-name="co1" table:default-cell-style-name="Default" /> 
<table:table-row table:style-name="ro1">
  <table:table-cell /> 
  </table:table-row>
  </table:table>
  </office:body>
</office:document-content>
