<a class="pagetitle" href="tiki-user_information.php?view_user={$userwatch}">{tr}User Information{/tr}</a><br/><br/>
<table width="100%">
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <table>
  <tr><td class="form">{tr}Name{/tr}:</td><td>{$userinfo.login}</td></tr>
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
  <tr><td class="form">{tr}Email{/tr}:</td><td>{$userinfo.email}</td></tr>
  <tr><td class="form">{tr}Country{/tr}:</td><td><img alt="flag" src="img/flags/{$country}.gif" /> {$country}</td></tr>
  <tr><td class="form">{tr}Theme{/tr}:</td><td>{$user_style}</td></tr>
  <tr><td  class="form">{tr}Language{/tr}:</td><td>{$language}</td></tr>
  <tr><td class="form">{tr}Real Name{/tr}:</td><td>{$realName}</td></tr>
  <tr><td class="form">{tr}Avatar{/tr}:</td><td>{$avatar}</td></tr>
  <tr><td class="form">{tr}HomePage{/tr}:</td><td>{$homePage}</td></tr>
  <tr><td class="form">{tr}Your personal Wiki Page{/tr}:</td><td><a class="link" href="tiki-index.php?page=UserPage{$userinfo.login}">UserPage{$userinfo.login}</a></td></tr>
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>{$display_timezone}</td></tr>
  </table>
  </form>
  </div>
  </div>
</tr>
</table>
<br/><br/>  
