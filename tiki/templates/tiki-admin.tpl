<h2>{tr}Administration{/tr}</h2>
<div align="center">
<table width="80%" cellpadding="0" cellspacing="0" border="1">
<tr>
  <td class="textbl" width="30%">
    {tr}Links/Commands{/tr}
  </td>
  <td class="textbl">
    {tr}Preferences{/tr}
  </td>
  <td class="textbl">
   {tr}Wiki Features{/tr}
  </td>
</tr>
<tr>
  <td valign="top">
    <ul>
      <li><a class="link" href="tiki-adminusers.php">{tr}Admin users{/tr}</a></li>
      <li><a class="link" href="tiki-admin.php?dump=1">{tr}Generate dump{/tr}</a></li>
      <li><a class="link" href="dump/new.tar">{tr}Download last dump{/tr}</a></li>
    </ul>    
    <form action="tiki-admin.php" method="post">
    <table>
      <tr><td class="textbl" colspan="2">{tr}Create a tag for the current wiki{/tr}</td></tr>
      <tr><td class="text">{tr}Tag Name{/tr}</td><td><input type="text" name="tagname"/></td><td class="text" colspan="2"><input type="submit" name="createtag" value="{tr}create tag{/tr}"/></td></tr>
    </table>
    </form>
    <form action="tiki-admin.php" method="post">
    <table>
      <tr><td class="textbl" colspan="2">{tr}Restore the wiki{/tr}</td></tr>
      <tr><td class="text">Tag Name</td>
      <td><select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
      </td>
      <td class="text" colspan="2"><input type="submit" name="restoretag" value="{tr}restore{/tr}"/></td></tr>
    </table>
    </form>
    <form action="tiki-admin.php" method="post">
    <table>
      <tr><td class="textbl" colspan="2">{tr}Remove a tag{tr}</td></tr>
      <tr><td class="text">{tr}Tag Name{/tr}</td>
      <td><select name="tagname">
          {section name=sel loop=$tags}
          <option value="{$tags[sel]}">{$tags[sel]}</option>
          {sectionelse}
          <option value=''></option>
          {/section}
          </select>
      </td>
      <td class="text" colspan="2"><input type="submit" name="removetag" value="{tr}remove{/tr}"/></td></tr>
    </table>
    </form>
  </td>
  <td valign="top">
    <form action="tiki-admin.php" method="post">    
    <table>
      <tr>
        <td class="text">{tr}Anonymous users can edit pages{/tr}:</td><td><input type="checkbox" name="anonCanEdit" {if $anonCanEdit eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Users can register{/tr}:</td><td><input type="checkbox" name="allowRegister" {if $allowRegister eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Open external links in new window{/tr}:</td><td><input type="checkbox" name="popupLinks" {if $popupLinks eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Maximum number of versions for history{/tr}: </td><td><input size="5" type="text" name="maxVersions" value="{$maxVersions}" /></td>
      </tr>
      <tr>
        <td class="text">{tr}Maximum number of records in listings{/tr}: </td><td><input size="5" type="text" name="maxRecords" value="{$maxRecords}" /></td>
      </tr>
      <tr>
        <td class="text">{tr}Wiki_Tiki_Title{/tr}: </td><td><input type="text" size="5" name="title" value="{$title}" /></td>
      </tr>
      <tr>
        <td class="text">{tr}Theme{/tr}:</td><td>
        <select name="style">
        {section name=ix loop=$styles}
        <option value="{$styles[ix]}" {if $style eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
        {/section}
        </select>
        </td>
      </tr>
      <tr>
        <td align="center" class="text" colspan="2"><input type="submit" name="prefs" value="{tr}Change preferences{/tr}" /></td>
      </tr>
    </table>
    </form>
  </td>
  <td valign="top">
    <form action="tiki-admin.php" method="post">    
    <table>
      <tr>
        <td class="text">{tr}Last changes{/tr}:</td><td><input type="checkbox" name="feature_lastChanges" {if $feature_lastChanges eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Dump{/tr}:</td><td><input type="checkbox" name="feature_dump" {if $feature_dump eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Ranking{/tr}:</td><td><input type="checkbox" name="feature_ranking" {if $feature_ranking eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}History{/tr}:</td><td><input type="checkbox" name="feature_history" {if $feature_history eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}List pages{/tr}:</td><td><input type="checkbox" name="feature_listPages" {if $feature_listPages eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Backlinks{/tr}:</td><td><input type="checkbox" name="feature_backlinks" {if $feature_backlinks eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Like pages{/tr}:</td><td><input type="checkbox" name="feature_likePages" {if $feature_likePages eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <tr>
        <td class="text">{tr}Search{/tr}:</td><td><input type="checkbox" name="feature_search" {if $feature_search eq 'y'}checked="checked"{/if}/></td>
      </tr>
       <tr>
        <td class="text">{tr}Image Galleries{/tr}:</td><td><input type="checkbox" name="feature_galleries" {if $feature_galleries eq 'y'}checked="checked"{/if}/></td>
      </tr>
      <!--
      <tr>
        <td class="text">{tr}User versions{/tr}:</td><td><input type="checkbox" name="feature_userVersions" {if $feature_userVersions eq 'y'}checked="checked"{/if}/></td>
      </tr>
      -->
      <tr>
        <td align="center" class="text" colspan="2"><input type="submit" name="features" value="{tr}Set features{/tr}" /></td>
      </tr>
    </table>
    </form>
  </td>
</tr>
</table>
<br/>
<table width="80%" cellpadding="0" cellspacing="0" border="1">
<tr>
  <td>
  <form method="post" action="tiki-admin.php">
    <table>
    <tr><td>{tr}Change admin password{/tr}:</td><td><input type="password" name="adminpass" /></td></tr>
    <tr><td>{tr}Again{/tr}:</td><td><input type="password" name="again" /></td></tr>
    <tr><td>&nbsp;</td><td><input type="submit" name="newadminpass" value="{tr}change{/tr}" /></td></tr>
    </table>
  </form>
  </td>
</tr>
</table>
</div>

