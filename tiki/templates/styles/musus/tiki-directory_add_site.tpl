<a class="pagetitle" href="tiki-directory_add_site.php?parent={$parent}">{tr}Add a new site{/tr}</a><br /><br />
{* Display the title using parent *}
{include file=tiki-directory_bar.tpl}
<br /><br />

{if $save eq 'y'}
<h2>{tr}Site added{/tr}</h2>
{tr}The following site was added and validation by admin may be needed before appearing on the lists{/tr}
<table>
<tr>
    <td>{tr}Name{/tr}:</td>
    <td>{$info.name}</td>
  </tr>
  <tr>
    <td>{tr}Description{/tr}:</td>
    <td>{$info.description}</td>
  </tr>
  <tr>
    <td>{tr}URL{/tr}:</td>
    <td>{$info.url}</td>
  </tr>
  <tr>
    <td>{tr}Country{/tr}:</td>
    <td>{$info.country}</td>
</tr></table>

{else}
{* Dislay a form to add or edit a site *}
<h2>{tr}Add or edit a site{/tr}</h2>
<form action="tiki-directory_add_site.php" method="post">
<input type="hidden" name="parent" value="{$parent|escape}" />
<input type="hidden" name="siteId" value="{$siteId|escape}" />
<table>
{if $msg}
	<tr><td colspan="2"><span class="hightlight">{$msg}</span></td></tr>
{/if}
  <tr>
    <td>{tr}Name:{/tr}</td>
    <td><input type="text" name="name" value="{$info.name|escape}" /></td>
  </tr>
  <tr>
    <td>{tr}Description:{/tr}</td>
    <td><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
  </tr>
  <tr>
    <td>{tr}URL:{/tr}</td>
    <td><input type="text" name="url" value="{$info.url|escape}" /></td>
  </tr>
  <tr>
    <td>{tr}Categories:{/tr}</td>
    <td>
    <select name="siteCats[]" multiple="multiple" size="4" />
    {section name=ix loop=$categs}
      <option value="{$categs[ix].categId|escape}" {if $categs[ix].belongs eq 'y'}selected="selected"{/if}>{$categs[ix].path}</option>
    {/section}
    </select>
    </td>
  </tr>
  <tr>
    <td>{tr}Country{/tr}:</td>
    <td>
      <select name="country">
        {section name=ux loop=$countries}
        <option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{$countries[ux]}</option>
        {/section}
      </select>
    </td>
  </tr>
  <!--
  <tr>
    <td>{tr}Is valid{/tr}:</td>
    <td><input name="isValid" type="checkbox" {if $info.isValid eq 'y'}checked="checked"{/if} /></td>
  </tr>
  -->
  <input name="isValid" type="hidden" value="" />
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" value="{tr}save{/tr}" /></td>
  </tr>
</table>
</form>
{/if}
<br /><br /><br />
