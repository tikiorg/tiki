<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="StyleSheet" media="all" href="styles/{$style}" type="text/css" />
    {include file="bidi.tpl"}
    <title>{$siteTitle}{if $page ne ''} : {$page}
                       {elseif $title ne ''} : {$title}
                       {elseif $thread_info.title ne ''} : {$thread_info.title}
                       {elseif $forum_info.name ne ''} : {$forum_info.name}
                       {/if}</title>
    {literal}
	<script type="text/javascript" src="lib/tiki-js.js">
	</script>
	{/literal}
	{if $uses_tabs eq 'y'}
	{/if}
{$trl}
</head>
<body {if $user_dbl eq 'y' and $dblclickedit eq 'y' and $tiki_p_edit eq 'y'} ondblclick="location.href='tiki-editpage.php?page={$page|escape:"url"}';"{/if}>
  {if $minical_reminders>100}
    <iframe style="width: 0; height: 0; border: 0" src="tiki-minical_reminders.php"></iframe>
  {/if}
