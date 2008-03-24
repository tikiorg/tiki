<h1><a class="pagetitle" href="tiki-directory_add_site.php?parent={$parent}">{tr}Add a new site{/tr}</a></h1>
{* Display the title using parent *}
{include file=tiki-directory_bar.tpl}
<br /><br />

{if $categs[0] eq ''}
  {tr}You cannot add sites until Directory Categories are setup.{/tr}<br />
  {tr}Please contact the Site Administrator{/tr}
{else}
{if $save eq 'y'}
<h2>{tr}Site added{/tr}</h2>
<br /><b>
  {tr}The following site was added and validation by admin may be needed before appearing on the lists{/tr}
</b><br /><br />
<table class="normal">
<tr>
    <td class="formcolor">{tr}Name{/tr}:</td>
    <td class="formcolor">{$info.name}</td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Description{/tr}:</td>
    <td class="formcolor">{$info.description}</td>
  </tr>
  <tr>
    <td class="formcolor">{tr}URL{/tr}:</td>
    <td class="formcolor">{$info.url}</td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Country{/tr}:</td>
    <td class="formcolor">{$info.country}</td>
</tr>
</table>

{else}
  {if $msg}
    <div class="commentsedithelp">
      <br /><b>{tr}{$msg}{/tr}</b><br /><br />
    </div>
  {/if}

{* Dislay a form to add or edit a site *}
<h2>{tr}Add or edit a site{/tr}</h2>
<form action="tiki-directory_add_site.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
<input type="hidden" name="siteId" value="{$siteId|escape}" />

<table class="normal">
  <tr>
    <td class="formcolor">{tr}Name{/tr}:</td>
    <td class="formcolor"><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Description:{/tr}</td>
    <td class="formcolor"><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}URL:{/tr}</td>
    <td class="formcolor"><input type="text" size="60" name="url" value="{if $info.url ne ""}{$info.url|escape}{else}http://{/if}" /></td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Categories:{/tr}</td>
    <td class="formcolor">
    <select name="siteCats[]" multiple="multiple" size="4">
    {section name=ix loop=$categs}
      <option value="{$categs[ix].categId|escape}" {if $categs[ix].belongs eq 'y' or $categs[ix].categId eq $addtocat}selected="selected"{/if}>{$categs[ix].path}</option>
    {/section}
    </select>
    </td>
  </tr>
  <tr>
    <td class="formcolor">{tr}Country{/tr}:</td>
    <td class="formcolor">
      <select name="country">
        {section name=ux loop=$countries}
        <option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{$countries[ux]}</option>
        {/section}
      </select>
    </td>
  </tr>
  <!--
  <tr>
    <td class="formcolor">{tr}Is valid{/tr}:</td>
    <td class="formcolor"><input name="isValid" type="checkbox" {if $info.isValid eq 'y'}checked="checked"{/if} /></td>
  </tr>
  -->
  <input name="isValid" type="hidden" value="" />
  <tr>
    <td class="formcolor">&nbsp;</td>
    <td class="formcolor"><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
</table>
</form>
{/if}
<br /><br /><br />
{/if}
