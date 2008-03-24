<h1><a class="pagetitle" href="tiki-admin_poll_options.php?pollId={$pollId}">{tr}Admin Polls{/tr}: {$menu_info.title}</a></h1>

<a href="tiki-admin_polls.php" class="linkbut">{tr}List polls{/tr}</a>
<a href="tiki-admin_polls.php?pollId={$pollId}" class="linkbut">{tr}Edit this poll{/tr}</a>

<h2>{tr}Preview poll{/tr}</h2>
<div align="center">
<div style="text-align:left;width:130px;" class="cbox">
<div class="cbox-title">{$menu_info.name}</div>
<div class="cbox-data">
{include file=tiki-poll.tpl}
</div>
</div>
</div>
<br />


<h2>{tr}Edit or add poll options{/tr}</h2>
<form action="tiki-admin_poll_options.php" method="post">
<input type="hidden" name="optionId" value="{$optionId|escape}" />
<input type="hidden" name="pollId" value="{$pollId|escape}" />
<table>
<tr>
<td class="form">{tr}Option{/tr}:</td><td><input type="text" name="title" value="{$title|escape}" /></td>
<td class="form">{tr}Position{/tr}:</td><td><input type="text" name="position" value="{$position|escape}" size="4" /></td>
<td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Poll options{/tr}</h2>
<div  align="center">
<table class="normal">
<tr>
<td class="heading">{tr}Position{/tr}</td>
<td class="heading">{tr}Title{/tr}</td>
<td class="heading">{tr}Votes{/tr}</td>
<td class="heading">{tr}Action{/tr}</td>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].position}</td>
<td>{$channels[user].title}</td>
<td>{$channels[user].votes}</td>
<td>
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;remove={$channels[user].optionId}">{tr}Delete{/tr}</a>
   <a class="link" href="tiki-admin_poll_options.php?pollId={$pollId}&amp;optionId={$channels[user].optionId}">{tr}Edit{/tr}</a>
</td>
</tr>
{/section}
</table>
<br />

