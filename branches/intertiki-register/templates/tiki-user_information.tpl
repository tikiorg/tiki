<h1><a class="pagetitle" href="tiki-user_information.php?view_user={$userwatch}">{tr}User Information{/tr}</a></h1>
{if $prefs.feature_display_my_to_others eq 'y' and $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y' and $allowMsgs eq 'y'}
<div class="navbar"><a class="linkbut" href="#message">{tr}Send me a message{/tr}</a></div>
{/if}
<table>
<tr>
  <td valign="top">
  <div class="cbox">
  <div class="cbox-title">{tr}User Information{/tr}</div>
  <div class="cbox-data">
  <div class="simplebox">
  <table>
  <tr><td class="form">{tr}User{/tr}:</td><td>
	{if $tiki_p_admin eq 'y'}
	<a class="link" href="tiki-assignuser.php?assign_user={$userinfo.login|escape:"url"}" title="{tr}Assign Group{/tr}">{icon _id='key' align="right" alt="{tr}Assign Group{/tr}"}</a>
	<a class="link" href="tiki-user_preferences.php?userId={$userinfo.userId}" title="{tr}Change user preferences{/tr}">{icon _id='wrench' align="right" alt="{tr}Change user preferences{/tr}"}</a>
	{/if}
	{$userinfo.login|userlink}
	</td></tr>
{if $prefs.feature_score eq 'y'}
  <tr><td class="form">{tr}Score{/tr}:</td><td>{$userinfo.score|star}{$userinfo.score}</td></tr>
{/if}
  <tr><td class="form">{tr}Last login{/tr}:</td><td>{$userinfo.lastLogin|tiki_short_datetime}</td></tr>
{if $email_isPublic neq 'n'}
  <tr><td class="form">{tr}Email{/tr}:</td><td>{$userinfo.email}</td></tr>
{/if}  
{if !empty($country) and $contry != 'Other'}
  <tr><td class="form">{tr}Country{/tr}:</td><td><img alt="{tr}{$country}{/tr}" src="img/flags/{$country}.gif" /> {tr}{$country}{/tr}</td></tr>
{/if}
  {if $prefs.change_theme ne 'n'}<tr><td class="form">{tr}Theme{/tr}:</td><td>{$user_style}</td></tr>{/if}
  {if $prefs.change_language eq 'y'}<tr><td  class="form">{tr}Language{/tr}:</td><td>{$user_language}</td></tr>{/if}
  {if $realName }
  <tr><td class="form">{tr}Real Name{/tr}:</td><td>{$realName}</td></tr>
  {/if}
{if $gender neq 'Hidden' and $gender}
  <tr><td>{tr}Gender{/tr}:</td><td>{tr}{$gender}{/tr}</td></tr>
{/if}

  {* Custom fields *}
  {section name=ir loop=$customfields}
  {if $customfields[ir].show}
    <tr><td class="form">{tr}{$customfields[ir].label}{/tr}:</td><td>{$customfields[ir].value}</td></tr>
  {/if}
  {/section}

  {if $avatar}<tr><td class="form">{tr}Avatar{/tr}:</td><td>{$avatar}</td></tr>{/if}
  {if $homepage}  <tr><td class="form">{tr}Homepage{/tr}:</td><td>{if $homePage ne ""}<a href="{$homePage}" class="link" title="{tr}Users HomePage{/tr}">{$homePage}</a>{/if}</td></tr>
{if $prefs.feature_wiki eq 'y' && $prefs.feature_wiki_userpage eq 'y'}
  <tr><td class="form">{tr}Personal Wiki Page{/tr}:</td><td>
{if $userPage_exists}
<a class="link" href="tiki-index.php?page={$prefs.feature_wiki_userpage_prefix|escape:'url'}{$userinfo.login|escape:'url'}">{$prefs.feature_wiki_userpage_prefix}{$userinfo.login}</a>
{elseif $user == $userinfo.login}
{$prefs.feature_wiki_userpage_prefix}{$userinfo.login}<a class="link" href="tiki-editpage.php?page={$prefs.feature_wiki_userpage_prefix|escape:'url'}{$userinfo.login|escape:'url'}" title="{tr}Create page{/tr}">?</a>
{else}&nbsp;{/if}
</td></tr>
{/if}
{/if}
  <tr><td class="form">{tr}Displayed time zone{/tr}:</td><td>{$user_prefs.display_timezone|default:"{tr}System{/tr}"}</td></tr>

{if $prefs.user_tracker_infos}
  {foreach item=itemField from=$userItem.field_values}
	{if $itemField.value ne '' or !empty($itemField.categs) or !empty($itemField.links)}
		<tr><td class="form">{tr}{$itemField.name}{/tr}:</td>
		<td>{include file="tracker_item_field_value.tpl" field_value=$itemField item=$itemField}</td></tr>
	{/if}
  {/foreach}
{/if}

{if $prefs.feature_friends eq 'y' && $user ne $userwatch && $user}
  {if $friend}
  <tr><td class="form">&nbsp;</td><td class="form">
    {icon _id='user'} {tr}This user is your friend{/tr}
  </td></tr>  
  {else}
  <tr><td class="form">&nbsp;</td><td class="form">
    {icon _id='user_delete'} <a class="link" href="tiki-friends.php?request_friendship={$userinfo.login}">{tr}Request friendship from this user{/tr}</a>
  </td></tr>  
  {/if}
{/if}
  </table>
  </div>
  </div>
  </div>
</td></tr>

{if $prefs.feature_display_my_to_others eq 'y'}
<tr><td>
{if $user_pages|@count > 0}
<h2>{tr}Wiki Pages{/tr}</h2>
<table class="normal">
{cycle values="even,odd" print=false}
{section name=ix loop=$user_pages}
<tr><td class="{cycle}"><a class="link" title="{tr}View{/tr}: {$user_pages[ix].pageName}" href="tiki-index.php?page={$user_pages[ix].pageName|escape:"url"}">{$user_pages[ix].pageName|truncate:40:"(...)"}</a></td></tr>
{/section}
</table>
{/if}
{if $user_galleries|@count > 0}
<h2>{tr}Image Galleries{/tr}</h2>
<table class="normal">
{cycle values="even,odd" print=false}
{section name=ix loop=$user_galleries}
<tr><td class="{cycle}"><a class="link" href="tiki-browse_gallery.php?galleryId={$user_galleries[ix].galleryId}">{$user_galleries[ix].name}</a>{/section}</td></tr>
</table>
{/if}
{if $user_blogs|@count > 0}
<h2>{tr}Blogs{/tr}</h2>
<table class="normal">
{cycle values="even,odd" print=false}
{section name=ix loop=$user_blogs}
<tr><td class="{cycle}"><a class="link" title="{tr}View{/tr}" href="tiki-view_blog.php?blogId={$user_blogs[ix].blogId}">{$user_blogs[ix].title}</a></td></tr>
{/section}
</table>
{/if}
{if $user_articles|@count > 0}
<h2>{tr}Articles{/tr}</h2>
<table class="normal">
{cycle values="even,odd" print=false}
{section name=ix loop=$user_articles}
<tr><td class="{cycle}"><a class="link" title="{tr}View{/tr}" href="tiki-read_article.php?articleId={$user_articles[ix].articleId}">{$user_articles[ix].title}</a></td></tr>
{/section}
</table>
{/if}
{if $user_forum_comments|@count > 0}
<h2>{tr}Forum comments{/tr}</h2>
<table class="normal">
{cycle values="even,odd" print=false}
{section name=ix loop=$user_forum_comments}
<tr><td class="{cycle}"><a class="link" title="{tr}View{/tr}" href="tiki-view_forum_thread.php?comments_parentId={$user_forum_comments[ix].threadId}&forumId={$user_forum_comments[ix].object}">{$user_forum_comments[ix].title}</a></td></tr>
{/section}
</table>
{/if}
</td></tr>
{/if}

{if $user and $prefs.feature_messages eq 'y' and $tiki_p_messages eq 'y' and $allowMsgs eq 'y'}
{if $sent}
{$message}
{/if}
<tr>
  <td valign="top"><a name="message"></a>
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
    <td class="form">{tr}Subject{/tr}:</td><td class="form"><input type="text" name="subject" value="" maxlength="255" style="width:100%;"/></td>
  </tr>
  <tr>
    <td colspan="2" style="text-align: center;" class="form"><textarea rows="20" cols="80" name="body"></textarea></td>
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
<br /><br />
