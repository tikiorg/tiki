<a class="pagetitle" href="tiki-user_information.php?view_user={$userwatch}">{tr}User Information{/tr}</a><br/><br/>
<table >
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <table>
  <tr><td class="form">{tr}Name{/tr}:</td><td>{$userinfo.login}</td></tr>
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
{if $email_isPublic neq 'n'}  
  <tr><td class="form">{tr}Email{/tr}:</td><td>{$userinfo.email}</td></tr>
{/if}  
  <tr><td class="form">{tr}Country{/tr}:</td><td><img alt="flag" src="img/flags/{$country}.gif" /> {$country}</td></tr>
  <tr><td class="form">{tr}Theme{/tr}:</td><td>{$user_style}</td></tr>
  <tr><td  class="form">{tr}Language{/tr}:</td><td>{$user_language}</td></tr>
  <tr><td class="form">{tr}Real Name{/tr}:</td><td>{$realName}</td></tr>
  <tr><td class="form">{tr}Avatar{/tr}:</td><td>{$avatar}</td></tr>
  <tr><td class="form">{tr}HomePage{/tr}:</td><td>{$homePage}</td></tr>
  <tr><td class="form">{tr}Personal Wiki Page{/tr}:</td><td><a class="link" href="tiki-index.php?page=UserPage{$userinfo.login}">UserPage{$userinfo.login}</a></td></tr>
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>{$display_timezone}</td></tr>
  </table>
  </form>
  </div>
  </div>
  </div>
</td></tr>
{if $user and $feature_messages eq 'y' and $tiki_p_messages eq 'y' and $allowMsgs eq 'y'}
{if $sent}
{$message}
{/if}
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}Send me a message{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <form method="post" action="tiki-user_information.php" name="f">
  <input type="hidden" name="to" value="{$userwatch|escape}" />
  <input type="hidden" name="view_user" value="{$userwatch|escape}" />
  <table class="normalnoborder">
  <tr>
    <td class="form">{tr}Priority{/tr}:</td><td class="form">
    <select name="priority">
      <option value="1" {if $priority eq 1}selected="selected"{/if}>1 -{tr}Lowest{/tr}-</option>
      <option value="2" {if $priority eq 2}selected="selected"{/if}>2 -{tr}Low{/tr}-</option>
      <option value="3" {if $priority eq 3}selected="selected"{/if}>3 -{tr}Normal{/tr}-</option>
      <option value="4" {if $priority eq 4}selected="selected"{/if}>4 -{tr}High{/tr}-</option>
      <option value="5" {if $priority eq 5}selected="selected"{/if}>5 -{tr}Very High{/tr}-</option>
    </select>
    <input type="submit" name="send" value="{tr}Send{/tr}" />
    </td>
  </tr>
  <tr>
    <td class="form">{tr}Subject{/tr}:</td><td class="form"><input type="text" name="subject" value="" size="80" maxlength="255"/></td>
  </tr>
  </table>
  <table class="normalnoborder">
  <tr>
    <td style="text-align: center;" class="form"><textarea rows="20" cols="80" name="body"></textarea></td>
  </tr>
</table>

  
  </form>
  </div>
  </div>
  </div>

  </td>
</tr>  
{/if}
</table>
<br/><br/>  
