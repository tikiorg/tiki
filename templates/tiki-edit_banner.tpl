{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-edit_banner.tpl,v 1.21.2.1 2007-11-28 22:04:17 sylvieg Exp $ *}
<h1><a class="pagetitle" href="tiki-edit_banner.php">{tr}Edit or create banners{/tr}</a></h1>
<div class="navbar">
<a class="linkbut" href="tiki-list_banners.php">{tr}List banners{/tr}</a>
</div>
<form action="tiki-edit_banner.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="bannerId" value="{$bannerId|escape}" />
<div class="simplebox">
  <table ><tr><td>
  <table >
  <tr>
     <td class="form">{tr}URL to link the banner{/tr}</td><td class="form"><input type="text" name="url" value="{$url|escape}" /></td>
   </tr>
  <tr><td class="form">{tr}Client{/tr}:</td>
      <td class="form">
        <select name="client">
        {section name=ix loop=$clients}
        <option value="{$clients[ix].user|escape}" {if $client eq $clients[ix].user}selected="selected"{/if}>{$clients[ix].user}</option>
        {/section}
        </select>
      </td>
   </tr>
   <tr><td class="form">{tr}Max impressions{/tr}:</td>
       <td class="form"><input type="text" name="maxImpressions" value="{$maxImpressions|escape}" size="7" /> <i>-1 for illimitted</i></td>
   </tr>
   <tr><td  class="form">{tr}Zone{/tr}:</td>
       <td class="form"><select name="zone">
           {section name=ix loop=$zones}
           <option value="{$zones[ix].zone|escape}" {if $zone eq $zones[ix].zone}selected="selected"{/if}>{$zones[ix].zone}</option>
           {/section}
           </select>
        </td>
   </tr>
   </table>
   </td>
   <td align="right"  class="form">
   {tr}Create Zone{/tr}:
   <input type="text" name="zoneName" size="10" /><br />
   <input type="submit" name="create_zone" value="{tr}Create Zone{/tr}" />
   </td>
   </tr>
   
   </table>
</div>



<div class="simplebox">
<table >
<tr><td colspan="2" class="form">{tr}Show the banner only between these dates{/tr}:</td></tr>
<tr><td class="form">{tr}From date{/tr}:</td><td class="form">{html_select_date time=$fromDate prefix="fromDate_" end_year="+2" field_order=$prefs.display_field_order}</td></tr>
<tr><td class="form">{tr}To date{/tr}:</td><td class="form">{html_select_date time=$toDate prefix="toDate_" end_year="+2" field_order=$prefs.display_field_order}</td></tr>
<tr><td class="form">{tr}Use dates{/tr}</td><td class="form"><input type="checkbox" name="useDates" {if $useDates eq 'y'}checked='checked'{/if}/></td></tr>
</table>
</div>


<div class="simplebox">
<table width="100%">
<tr><td colspan="2" class="form">{tr}Show the banner only in this hours{/tr}:</td></tr>
<tr><td class="form">{tr}from{/tr}:</td><td class="form">{html_select_time time=$fromTime display_seconds=false prefix='fromTime'}</td></tr>
<tr><td class="form">{tr}to{/tr}:</td><td class="form">{html_select_time time=$toTime display_seconds=false prefix='toTime'}</td></tr>
</table>
</div>

<div class="simplebox">
<table width="100%">
<tr><td colspan="7" class="form">{tr}Show the banner only on{/tr}:</td></tr>
<tr>
<td class="form">{tr}Mon{/tr}:<input type="checkbox" name="Dmon" {if $Dmon eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Tue{/tr}:<input type="checkbox" name="Dtue" {if $Dtue eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Wed{/tr}:<input type="checkbox" name="Dwed" {if $Dwed eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Thu{/tr}:<input type="checkbox" name="Dthu" {if $Dthu eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Fri{/tr}:<input type="checkbox" name="Dfri" {if $Dfri eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Sat{/tr}:<input type="checkbox" name="Dsat" {if $Dsat eq 'y'}checked="checked"{/if} /></td>
<td class="form">{tr}Sun{/tr}:<input type="checkbox" name="Dsun" {if $Dsun eq 'y'}checked="checked"{/if} /></td>
</tr>
</table>
</div>

<div class="simplebox">
{tr}Select ONE method for the banner{/tr}
<table border="1" >
  <tr>
  <td><input type="radio" name="use" value="useHTML" {if $use eq 'useHTML'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use HTML{/tr}
  <table>
  <tr>
  <td>{tr}HTML code{/tr}:</td>
  <td><textarea rows="5" cols="50" name="HTMLData">{if $use ne 'useFlash'}{$HTMLData|escape}{/if}</textarea></td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useImage" {if $use eq 'useImage'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use image{/tr}
  <table>
  <tr><td class="form">{tr}Image:{/tr}</td>
     <td><input type="hidden" name="imageData" value="{$imageData|escape}" />
         <input type="hidden" name="imageName" value="{$imageName|escape}" />
         <input type="hidden" name="imageType" value="{$imageType|escape}" />
         <input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
         <input name="userfile1" type="file" /></td>
  </tr>
  {if $hasImage eq 'y'}
  <tr><td class="form">{tr}Current Image{/tr}</td><td>
  {$imageName}: <img border="0" src="{$tempimg}" alt='{tr}Current Image{/tr}'/>
  </td></tr>   
  {/if}
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useFixedURL" {if $use eq 'useFiexedURL'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use image generated by URL (the image will be requested at the URL for each impression){/tr}
  <table>
  <tr><td class="form">{tr}URL{/tr}:</td>
      <td><input type="text" name="fixedURLData" value="{$fixedURLData|escape}" /></td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useFlash" {if $use eq 'useFlash'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use Flash{/tr}
  <table>
  {if $use eq 'useFlash'}
  <tr><td colspan=2>
  {banner zone="$zone"}
  </td></tr>
  {/if}
  <tr> <td><input type="hidden" name="movieId" value="{$movieId}"/><input type="hidden" name="movieInstallUrl" value="{$movieInstallUrl}"/>
      {tr}Movie URL{/tr} <input type="text" size="50" name="movieUrl" value="{$movieUrl|escape}" /><br />
      {tr}Movie Size{/tr}: <input type="text" size="4" name="movieWidth" value="{$movieWidth|escape}" /> {tr}Pixels{/tr} x <input type="text" size="4" name="movieHeight" value="{$movieHeight|escape}" /> {tr}Pixels{/tr} <br />
      {tr}FlashPlugin min version{/tr}: <input type="text" name="movieVersion" value="{$movieVersion|escape}" />({tr}ex{/tr}:8.0.0)<br />
     	Note: To be managed with tiki , your flash banner link should be: <a class="link" href="banner_click.php?id={$bannerId}&amp;url={$url}">banner_click.php?id={$bannerId}&amp;url={$url}</a> 
      </td>
  </tr>
  </table>
  </td>
  </tr>
  <tr>
  <td><input type="radio" name="use" value="useText" {if $use eq 'useText'}checked="checked"{/if}/></td>
  <td class="form">{tr}Use text{/tr}
  <table>
  <tr>
  <td class="form">{tr}Text{/tr}:</td>
  <td><textarea rows="8" cols="20" name="textData">{$textData|escape}</textarea></td>
  </tr>
  </table>
  </td>
  </tr>
</table>
</div>

<div align="center" class="simplebox">
<input type="submit" name="save" value="{tr}Save the Banner{/tr}" />
</div>
</form>
<br /><br />

<div align="left" class="simplebox">
<h2>{tr}Remove Zones (you lose entered info for the banner){/tr}</h2>
<table class="normal">
<tr>
<td class="heading">{tr}Name{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle print=false values="even,odd"}
{section name=ix loop=$zones}
<tr>
<td class="{cycle advance=false}">{$zones[ix].zone}</td>
<td class="{cycle}"><a class="link" href="tiki-edit_banner.php?removeZone={$zones[ix].zone}"><img src="pics/icons/cross.png" border="0" width="16" height="16"  alt='{tr}Remove{/tr}' title='{tr}Remove{/tr}' /></a></td>
</tr>
{/section}
</table>
</div>
