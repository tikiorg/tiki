{if $save eq 'y'}
  <h2>{tr}Tiki site registered{/tr}</h2>
  {tr}The following site was added and validation by admin may be needed before appearing on the lists{/tr}
  <table class="formcolor">
  <tr>
    <td>{tr}Name:{/tr}</td>
    <td>{$info.name}</td>
  </tr>
  <tr>
    <td>{tr}Description:{/tr}</td>
    <td>{$info.description}</td>
  </tr>
  <tr>
    <td>{tr}URL:{/tr}</td>
    <td>{$info.url}</td>
  </tr>
  <tr>
    <td>{tr}Country:{/tr}</td>
    <td>{$info.country}</td>
  </tr></table>
{else}
<div class="cbox">
  <div class="cbox-title">
  {if $tiki_p_admin ne 'y'}
  {tr}Error{/tr}
  </div>
  <div class="cbox-data">
  {tr}You don't have permission to use this feature.{/tr}
  {tr}Please register.{/tr}
  </div>
  {else}
  {tr}Register this site at Tiki.org{/tr}
  </div>
  <div class="cbox-data">
  <table><tr><td>
    <div class="simplebox">
    <b>{tr}Read this first!{/tr}</b><br /><br />
    {tr}On this page you can make your tiki site known to Tiki.org. It will get shown there in a list of known tiki sites.{/tr}
    <ul>
    <li>{tr}Registering is voluntary.{/tr}</li>
    <li>{tr}Registering does not give you any benefits except one more link to your site.{/tr}</li>
    <li>{tr}You don't get any emails, we don't sell the data about your site.{/tr}</li>
    <li>{tr}Registering is just for us to get an overview of Tiki's usage.{/tr}</li>
    </ul>
    <b>{tr}If your site is private or inside your intranet, you should not register!{/tr}</b><br /><br />
    </div>
  </td><td align="center" width="30%"><br /><br /><br />
  <a href="http://tiki.org/" target="_tikiwiki"><img src="img/tiki/Tiki_WCG.png" /></a><br />
  <br />
  {tr}tiki.org{/tr}
  </td></tr>
  </table>

  <br /><br />
  <b>{tr}Information about your site:{/tr}</b><br />
  <form action="http://tiki.org/tiki-directory_add_tiki_site.php" method="post">
  <input type="hidden" name="registertiki" value="true" />
  <table class="formcolor">
  <tr>
    <td>{tr}Name:{/tr}</td>
    <td><input type="text" name="name" size="60" value="{$info.name|escape}" /></td>
  </tr>
  <tr>
    <td>{tr}Description:{/tr}</td>
    <td><textarea rows="5" cols="60" name="description">{$info.description|escape}</textarea></td>
  </tr>
  <tr>
    <td>{tr}URL:{/tr}</td>
    <td><input type="hidden" name="url" value="{$info.url|escape}" />{$info.url|escape}</td>
  </tr>
  <tr>
    <td>{tr}Country:{/tr}</td>
    <td>
      <select name="country">
        {section name=ux loop=$countries}
        <option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{$countries[ux]}</option>
        {/section}
      </select>
    </td>
  </tr>
  <input name="isValid" type="hidden" value="" />
  <tr>
    <td>&nbsp;</td>
    <td><input type="submit" name="save" value="{tr}Save{/tr}" /></td>
  </tr>
  </table>
  </form>
  {/if}
  </div>
</div>
{/if}
