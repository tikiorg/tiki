<a class="pagetitle" href="tiki-user_preferences.php">{tr}User Preferences{/tr}</a><br/><br/>
[<a href="tiki-user_preferences.php" class="link">{tr}User Preferences{/tr}</a>
{if $feature_user_bookmarks eq 'y'}|<a href="tiki-user_bookmarks.php" class="link">{tr}User Bookmarks{/tr}</a>{/if}
{if $user_assigned_modules eq 'y'}|<a href="tiki-user_assigned_modules.php" class="link">{tr}Configure modules{/tr}</a>{/if}]<br/><br/>
<table width="100%">
<tr>
  <td valign="top" width="50%">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch}" />
  <table>
  <tr><td class="form">{tr}Name{/tr}:</td><td>{$userinfo.login}</td></tr>
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|date_format:"%a %d of %b, %Y [%H:%M:%S]"}</td></tr>
  <tr><td class="form">{tr}Email{/tr}:</td><td><input type="text" name="email" value="{$userinfo.email}" /></td></tr>
  {if $change_theme eq 'y'}
  <tr><td class="form">{tr}Theme{/tr}:</td><td><select name="style">
        {section name=ix loop=$styles}
        <option value="{$styles[ix]}" {if $style eq $styles[ix]}selected="selected"{/if}>{$styles[ix]}</option>
        {/section}
        </select></td></tr>
  {/if}
  {if $change_language eq 'y'}      
  <tr><td  class="form">{tr}Language{/tr}:</td><td>
        <select name="language">
        {section name=ix loop=$languages}
        <option value="{$languages[ix]}" {if $language eq $languages[ix]}selected="selected"{/if}>{$languages[ix]}</option>
        {/section}
        </select></td></tr>
  {/if}      
  <tr><td class="form">{tr}Real Name{/tr}:</td><td><input type="text" name="realName" value="{$realName}" /></td></tr>
  <tr><td class="form">{tr}Number of visited pages to remember{/tr}:</td><td>
  <select name="userbreadCrumb">
  <option value="1" {if $userbreadCrumb eq 1}selected="selected"{/if}>1</option>
  <option value="2" {if $userbreadCrumb eq 2}selected="selected"{/if}>2</option>
  <option value="3" {if $userbreadCrumb eq 3}selected="selected"{/if}>3</option>
  <option value="4" {if $userbreadCrumb eq 4}selected="selected"{/if}>4</option>
  <option value="5" {if $userbreadCrumb eq 5}selected="selected"{/if}>5</option>
  <option value="10" {if $userbreadCrumb eq 10}selected="selected"{/if}>10</option>
  </select>
  </td></tr>
  <tr><td class="form">{tr}HomePage{/tr}:</td><td><input type="text" name="homePage" value="{$homePage}" /></td></tr>
  {if $feature_wiki eq 'y'}
  <tr><td class="form">{tr}Your personal Wiki Page{/tr}:</td><td><a class="link" href="tiki-index.php?page=UserPage{$userinfo.login}">UserPage{$userinfo.login}</a>({tr}<a class="link" href="tiki-editpage.php?page=UserPage{$user}">{tr}edit{/tr}</a>{/tr})</td></tr>
  {/if}
  <tr><td align="center" colspan="2"><input type="submit" name="prefs" value="{tr}set{/tr}" /></td></tr>
  </table>
  </form>
  </div>
  <div class="simplebox">
  {tr}Change your password{/tr}<br/>
  <form action="tiki-user_preferences.php" method="post">
  <input type="hidden" name="view_user" value="{$userwatch}" />
  <table>
  <tr><td>{tr}Old password{/tr}:</td><td><input type="password" name="old" /></td></tr>
  <tr><td>{tr}New password{/tr}:</td><td><input type="password" name="pass1" /></td></tr>
  <tr><td>{tr}Again please{/tr}:</td><td><input type="password" name="pass2" /></td></tr>
  <tr><td align="center" colspan="2"><input type="submit" name="chgpswd" value="{tr}change{/tr}"></td></tr>
  </table>
  </form>
  </div>
  </div>
  </td>
  <td valign="top" width="50%">
  <!--
  <div class="cbox">
  <div class="cbox-title">{tr}Configure this page{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
 
  </div>
  </div>
  -->
  </td>
</tr>
</table>
<br/>
  <div class="cbox">
  <div class="cbox-title">{tr}User Pages{/tr}</div>
  <div class="cbox-data">
  <table width="100%">
  {section name=ix loop=$user_pages}
  <tr><td>
  <a class="link" href="tiki-index.php?page={$user_pages[ix].pageName}">{$user_pages[ix].pageName|truncate:30:"(...)"}</a>
  </td><td align="right">
  (<a class="link" href="tiki-editpage.php?page={$user_pages[ix].pageName}">{tr}edit{/tr}</a>)
  </td></tr>
  {/section}
  </table>
  </div>
  </div>
<br/>  
  <div class="cbox">
  <div class="cbox-title">{tr}User Blogs{/tr}</div>
  <table width="100%">
  {section name=ix loop=$user_blogs}
  <tr><td>
  <a class="link" href="tiki-view_blog.php?blogId={$user_blogs[ix].blogId}">{$user_blogs[ix].title}</a>
  </td><td align="right">
  (<a class="link" href="tiki-edit_blog.php?blogId={$user_blogs[ix].blogId}">{tr}edit{/tr}</a>)
  </td></tr>
  {/section}
  </table>
  </div>
<br/>
  <div class="cbox">
  <div class="cbox-title">User Galleries</div>
  <div class="cbox-data">
  <table width="100%">
  {section name=ix loop=$user_galleries}
  <tr><td>
  <a class="link" href="tiki-browse_gallery.php?galleryId={$user_galleries[ix].galleryId}">{$user_galleries[ix].name}</a>
  </td><td align="right">
  <a class="link" href="tiki-galleries.php?editgal={$user_galleries[ix].galleryId}">({tr}edit{/tr})</a>
  </td></tr>
  {/section}
  </table>
  </div>


