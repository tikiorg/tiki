{* $Header: /cvsroot/tikiwiki/tiki/templates/tiki-view_tracker.tpl,v 1.159.2.21 2008-01-06 12:41:41 sylvieg Exp $ *}
<script type="text/javascript" src="lib/trackers/dynamic_list.js"></script>
{if !empty($tracker_info.showPopup)}
{popup_init src="lib/overlib.js"}
{/if}

<h1><a class="pagetitle" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}Tracker{/tr}: {$tracker_info.name}</a></h1>
<div class="navbar">
{if $prefs.feature_user_watches eq 'y' and $tiki_p_watch_trackers eq 'y' and $user}
{if $user_watching_tracker ne 'y'}
<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=add" title="{tr}Monitor{/tr}"><img src="pics/icons/eye.png" width="16" height="16" border="0" align="right" hspace="5" alt="{tr}Monitor{/tr}" /></a>
{else}
<a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;watch=stop" title="{tr}Stop Monitor{/tr}"><img src="pics/icons/no_eye.png" width="16" height="16" border="0" align="right" hspace="5" alt="{tr}Stop Monitor{/tr}" /></a>
{/if}
{/if}
{if $prefs.rss_tracker eq "y"}
<a href="tiki-tracker_rss.php?trackerId={$trackerId}"><img src='img/rss.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}'  align="right" /></a>
{/if}
{if (isset($tiki_p_list_trackers) and $tiki_p_list_trackers eq 'y') or (!isset($tiki_p_list_trackers) and $tiki_p_view_trackers eq 'y')}<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>{/if}
{if $filtervalue}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
&nbsp;&nbsp;
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>
{/if}
</div>

<div class="navbar" align="right">
	{if $user and $prefs.feature_user_watches eq 'y'}
		{if $category_watched eq 'y'}
			{tr}Watched by categories{/tr}:
			{section name=i loop=$watching_categories}
				<a href="tiki-browse_categories?parentId={$watching_categories[i].categId}">{$watching_categories[i].name}</a>&nbsp;
			{/section}
		{/if}	
	{/if}
</div>

{if !empty($tracker_info.description)}
<div class="wikitext">{$tracker_info.description}</div>
{/if}

{if !empty($mail_msg)}
<div class="wikitext">{$mail_msg}</div>
{/if}

{if count($err_mandatory) > 0}<div class="simplebox highlight">
{tr}Following mandatory fields are missing{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_mandatory}
{$err_mandatory[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />{/if}
{if count($err_value) > 0}<div class="simplebox highlight">
{tr}Following fields are incorrect{/tr}&nbsp;:<br/>
	{section name=ix loop=$err_value}
{$err_value[ix].name}{if !$smarty.section.ix.last},&nbsp;{/if}
	{/section}
</div><br />{/if}
{if $prefs.feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false reset=true}
<div id="page-bar">
{if $tiki_p_view_trackers eq 'y' or ($tracker_info.writerCanModify eq 'y' and $user)}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Tracker{/tr} <i>{$tracker_info.name}</i></a></span>
{/if}
{if $tiki_p_create_tracker_items eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Insert new item{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3" print=false advance=false reset=true}
{* -------------------------------------------------- tab with list --- *}
{if $tiki_p_view_trackers eq 'y' or ($tracker_info.writerCanModify eq 'y' and $user)}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

{if (($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y') or $show_filters eq 'y'}
{include file="tracker_filter.tpl"}
{/if}

{if $cant_pages > 1 or $initial}
<div align="center">
{section name=ini loop=$initials}
{if $initial and $initials[ini] eq $initial}
<span class="button2"><span class="linkbuton">{$initials[ini]|capitalize}</span></span> . 
{else}
<a href="tiki-view_tracker.php?initial={$initials[ini]}&amp;trackerId={$trackerId}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if $status}&amp;status={$status|escape:"url"}{/if}" 
class="prevnext">{$initials[ini]}</a> . 
{/if}
{/section}
<a href="tiki-view_tracker.php?initial=&amp;trackerId={$trackerId}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}{if $status}&amp;status={$status|escape:"url"}{/if}" 
class="prevnext">{tr}All{/tr}</a>
</div>
{/if}

{* ------- list headings --- *}
<form name="checkform" method="post" action="{$smarty.server.PHP_SELF}">
<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}
{section name=ix loop=$fields}
{if $fields[ix].type eq 'l' and $fields[ix].isTblVisible eq 'y'}
<td class="heading auto">{$fields[ix].name|default:"&nbsp;"}</td>
{elseif $fields[ix].type eq 's' and ($fields[ix].name eq "Rating" or $fields[ix].name eq tra("Rating")) and $fields[ix].isTblVisible eq 'y'}
	<td class="heading auto"{if $tiki_p_tracker_vote_ratings eq 'y' and $user ne ''} colspan="2"{/if}>
		<a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode=f_{if $sort_mode eq 'f_'|cat:$fields[ix].fieldId|cat:'_asc'}
		{$fields[ix].fieldId|escape:"url"}_desc{else}{$fields[ix].fieldId|escape:"url"}_asc{/if}">
			{$fields[ix].name|truncate:255:"..."|default:"&nbsp;"}
		</a>
	</td>
	{assign var=rateFieldId value=$fields[ix].fieldId}
{elseif $fields[ix].isTblVisible eq 'y' and $fields[ix].type ne 'x' and $fields[ix].type ne 'h' and ($fields[ix].isHidden eq 'n' or $fields[ix].isHidden eq 'c' or $tiki_p_admin_trackers eq 'y')}
<td class="heading auto"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode=f_{if $sort_mode eq
'f_'|cat:$fields[ix].fieldId|cat:'_asc'}{$fields[ix].fieldId|escape:"url"}_desc{else}{$fields[ix].fieldId|escape:"url"}_asc{/if}{if $filterfield}&amp;filterfield={$filterfield}&amp;filtervalue={$filtervalue}{/if}">{$fields[ix].name|truncate:255:"..."|default:"&nbsp;"}</a></td>
{/if}
{/section}
{if $tracker_info.showCreated eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}{if $find}find={$find}&amp;{/if}trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if 
$sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a></td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;{if $initial}initial={$initial}&amp;{/if}find={$find}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}Coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{if $tiki_p_admin_trackers eq 'y'}<td class="heading" width="5%">{tr}dls{/tr}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td class="heading" width="5%">
<script type='text/javascript'>
document.write("<input name=\"switcher\" id=\"clickall\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'action[]',this.checked)\"/><label for=\"clickall\">{tr}All{/tr}</label>");
</script>
</td>
{/if}
</tr>

{* ------- Items loop --- *}
{assign var=itemoff value=0}
{cycle values="odd,even" print=false}
{section name=user loop=$items}
<tr class="{cycle}">
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="auto" style="width:20px;">
{assign var=ustatus value=$items[user].status|default:"c"}
{html_image file=$status_types.$ustatus.image title=$status_types.$ustatus.label alt=$status_types.$ustatus.label}
</td>
{/if}

{* ------- list values --- *}
{section name=ix loop=$items[user].field_values}

{if $items[user].field_values[ix].isTblVisible eq 'y'}
{if $items[user].field_values[ix].type eq 'l'}
<td class="auto">
{foreach key=tid item=tlabel from=$items[user].field_values[ix].links}
{if $items[user].field_values[ix].options_array[4] eq '1'}
<div><a href="tiki-view_tracker_item.php?itemId={$tid}&trackerId={$items[user].field_values[ix].options_array[0]}" class="link">{$tlabel|truncate:255:"..."}</a></div>
{else}
<div>{$tlabel|truncate:255:"..."}</div>
{/if}
{/foreach}
</td>
{elseif $items[user].field_values[ix].isMain eq 'y'}
<td class="auto">

{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y' 
 or ($tracker_info.writerCanModify eq 'y' and $user and $my eq $user) or ($tracker_info.writerCanModify eq 'y' and $group and $ours eq $group)}
{if !empty($tracker_info.showPopup)}
{capture name=popup}
<div class="cbox">
<table>
{cycle values="odd,even" print=false}
{foreach from=$items[user].field_values item=f}
	{if in_array($f.fieldId, $popupFields)}
<tr><th class="{cycle advance=false}">{$f.name}</th><td class="{cycle}">{include file="tracker_item_field_value.tpl" field_value=$f}</td></tr>
	{/if}
{/foreach}
</table>
</div>
{/capture}
{/if}
<a class="tablename" href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=view&amp;{if $offset}offset={$offset}{/if}&amp;reloff={$smarty.section.user.index}&amp;cant={$item_count}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}"{if !empty($tracker_info.showPopup)} {popup text=$smarty.capture.popup|escape:"javascript"|escape:"html" fullhtml="1" hauto=true vauto=true  }{/if}>
{/if}

{if  ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[2]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[2]}</span>{/if}

{if $items[user].field_values[ix].type eq 'f' or $items[user].field_values[ix].type eq 'j'}
{if $items[user].field_values[ix].value}{if $items[user].field_values[ix].options_array[0] eq 'd'}{$items[user].field_values[ix].value|tiki_short_date|truncate:255:"..."|default:"&nbsp;"}{else}{$items[user].field_values[ix].value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}{/if}{else}&nbsp;{/if}

{elseif $items[user].field_values[ix].type eq 'c'}
{$items[user].field_values[ix].value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"}

{elseif $items[user].field_values[ix].type eq 'r'}
    {if $items[user].field_values[ix].displayedvalue ne ""}
        {$items[user].field_values[ix].displayedvalue}
    {else}
        {$items[user].field_values[ix].value}
    {/if}
{elseif $items[user].field_values[ix].type eq 'a'}
{if $items[user].field_values[ix].options_array[4] ne ''}
{$items[user].field_values[ix].pvalue|truncate:$items[user].field_values[ix].options_array[4]:"...":true}
{else}
{$items[user].field_values[ix].pvalue}
{/if}

{elseif $items[user].field_values[ix].type eq 'i'}
{assign var=width value=$items[user].field_values[ix].options_array[0]}
{assign var=height value=$items[user].field_values[ix].options_array[1]}
{if $items[user].field_values[ix].value ne ''}
<img border="0" src="{$items[user].field_values[ix].value}"  width="{$width}" height="{$height}" alt="n/a" />
{else}
<img border="0" src="img/icons/na_pict.gif" alt="n/a" />
{/if}

{elseif $items[user].field_values[ix].type eq 'M'}
{if $items[user].field_values[ix].value ne ''}
<img border="0" src="img/icons/multimedia.png"  width=20 height=20 alt="Flash multimedia content" />
{/if}
{elseif $items[user].field_values[ix].type eq 'm'}
{$items[user].field_values[ix].value|default:"&nbsp;"}

{elseif $items[user].field_values[ix].type eq 'e'}
{foreach item=ii from=$items[user].field_values[ix].categs}{$ii.name}<br />{/foreach}

{elseif $items[user].field_values[ix].type eq 'y'}
{if !empty($items[user].field_values[ix].value) and $items[user].field_values[ix].value ne 'None'}
{assign var=o_opt value=$items[user].field_values[ix].options_array[0]}
{capture name=flag}
{tr}{$items[user].field_values[ix].value}{/tr}
{/capture}
{if $o_opt ne '1'}<img border="0" src="img/flags/{$items[user].field_values[ix].value}.gif" title="{$smarty.capture.flag|replace:'_':' '}" alt="{$smarty.capture.flag|replace:'_':' '}" />{/if}
{if $o_opt ne '1' and $o_opt ne '2'}&nbsp;{/if}
{if $o_opt ne '2'}{$smarty.capture.flag|replace:'_':' '}{/if}
{else}
&nbsp;
{/if}

{else}
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}

{/if}

{if ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[3]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[3]}</span>{/if}

{if $tiki_p_view_trackers eq 'y' or $tiki_p_modify_tracker_items eq 'y' or $tiki_p_comment_tracker_items eq 'y'}</a>{/if}
</td>
{else}

{if $items[user].field_values[ix].linkId and $items[user].field_values[ix].trackerId}
<td class="auto">
{if $items[user].field_values[ix].options_array[2] eq '1'}
<a href="tiki-view_tracker_item.php?itemId={$items[user].field_values[ix].linkId}&amp;trackerId={$items[user].field_values[ix].trackerId}" class="link">
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}
</a>
{else}
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}
{/if}
</td>

{elseif $items[user].field_values[ix].type eq 'm'}
<td class="auto">
{if $items[user].field_values[ix].options_array[0] eq '1' and $items[user].field_values[ix].value}
{mailto address=$items[user].field_values[ix].value|escape encode="hex"}
{elseif $items[user].field_values[ix].options_array[0] eq '2' and $items[user].field_values[ix].value}
{mailto address=$items[user].field_values[ix].value|escape encode="none"}
{else}
{$items[user].field_values[ix].value|escape|default:"&nbsp;"}
{/if}
</td>
{elseif $items[user].field_values[ix].type eq 'f' or $items[user].field_values[ix].type eq 'j'}
<td class="auto">
{$items[user].field_values[ix].value|tiki_short_datetime|default:"&nbsp;"}
</td>
{elseif $items[user].field_values[ix].type eq 'a'}
<td class="auto">
{if $items[user].field_values[ix].options_array[4] ne ''}
{$items[user].field_values[ix].pvalue|truncate:$items[user].field_values[ix].options_array[4]:"...":true}
{else}
{$items[user].field_values[ix].pvalue}
{/if}
</td>
{elseif $items[user].field_values[ix].type eq 'e'}
<td class="auto">
{foreach item=ii from=$items[user].field_values[ix].categs}{$ii.name}<br />{/foreach}
</td>
{elseif $items[user].field_values[ix].type eq 'i'}
<td class="auto">
{assign var=width value=$items[user].field_values[ix].options_array[0]}
{assign var=height value=$items[user].field_values[ix].options_array[1]}
{if $items[user].field_values[ix].value ne ''}
<img border="0" src="{$items[user].field_values[ix].value}" width="{$width}" height="{$height}" alt="n/a" />
{else}
<img border="0" src="img/icons/na_pict.gif" alt="n/a" />
{/if}
</td>
{elseif $items[user].field_values[ix].type eq 'y'}
<td class="auto">
{if !empty($items[user].field_values[ix].value) and $items[user].field_values[ix].value ne 'None'}
{assign var=o_opt value=$items[user].field_values[ix].options_array[0]}
{capture name=flag}
{tr}{$items[user].field_values[ix].value}{/tr}
{/capture}
{if $o_opt ne '1'}<img border="0" src="img/flags/{$items[user].field_values[ix].value}.gif"  title="{$smarty.capture.flag|replace:'_':' '}" alt="{$smarty.capture.flag|replace:'_':' '}"/>{/if}
{if $o_opt ne '1' and $o_opt ne '2'}&nbsp;{/if}
{if $o_opt ne '2'}{$smarty.capture.flag|replace:'_':' '}{/if}
{/if}
</td>


{elseif $items[user].field_values[ix].type eq 'U'}
<td class="auto">

{$items[user].field_values[ix].value|how_many_user_inscriptions} {tr}subscriptions{/tr}

</td>

{elseif $items[user].field_values[ix].type eq 's' and ($items[user].field_values[ix].name eq "Rating" or $items[user].field_values[ix].name eq tra("Rating"))  and $tiki_p_tracker_view_ratings eq 'y'}
	<td class="auto">
		<b title="{tr}Rating{/tr}: {$items[user].field_values[ix].value|default:"-"}, {tr}Number of voices{/tr}: {$items[user].field_values[ix].numvotes|default:"-"}, {tr}Average{/tr}: {$items[user].field_values[ix].voteavg|default:"-"}">
			&nbsp;{$items[user].field_values[ix].value|default:"-"}&nbsp;
		</b>
	</td>
	{if $tiki_p_tracker_vote_ratings eq 'y'}
		<td class="auto" nowrap="nowrap">
			<span class="button2">
			{if $items[user].my_rate eq NULL}
				<b class="linkbut highlight">-</b>
			{else}
				<a href="{$smarty.server.PHP_SELF}?{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}&amp;trackerId={$items[user].trackerId}&amp;rateitemId={$items[user].itemId}&amp;fieldId={$rateFieldId}&amp;rate_{$items[user].trackerId}=NULL" class="linkbut">-</a>
			{/if}
				{section name=i loop=$items[user].field_values[ix].options_array}
					{if $items[user].field_values[ix].options_array[i] eq $items[user].my_rate}
						<b class="linkbut highlight">{$items[user].field_values[ix].options_array[i]}</b>
					{else}
						<a href="{$smarty.server.PHP_SELF}?{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}&amp;trackerId={$items[user].trackerId}&amp;rateitemId={$items[user].itemId}&amp;fieldId={$rateFieldId}&amp;rate_{$items[user].trackerId}={$items[user].field_values[ix].options_array[i]}" class="linkbut">{$items[user].field_values[ix].options_array[i]}</a>
					{/if}
				{/section}
			</span>
		</td>
	{/if}

{elseif $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h'}
<td class="auto">
{if  ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[2]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[2]}&nbsp;</span>{/if}
{if $items[user].field_values[ix].type eq 'c'}
{tr}{$items[user].field_values[ix].value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"|default:"&nbsp;"}{/tr}
{else}
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}
{/if}
{if ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[3]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[3]}</span>{/if}
</td>
{/if}
{/if}
{/if}
{/section}

{if $tracker_info.showCreated eq 'y'}
<td>{if $tracker_info.showCreatedFormat}{$items[user].created|tiki_date_format:$tracker_info.showCreatedFormat}{else}{$items[user].created|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td>{if $tracker_info.showLastModifFormat}{$items[user].lastModif|tiki_date_format:$tracker_info.showLastModifFormat}{else}{$items[user].lastModif|tiki_short_datetime}{/if}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=att{if $offset}&amp;offset={$offset}{/if}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}" 
link="{tr}List Attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a> {$items[user].attachments}</td>
{if $tiki_p_admin_trackers eq 'y'}<td  style="text-align:center;">{$items[user].downloads}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
  <td>
    <input type="checkbox" name="action[]" value='{$items[user].itemId}' style="border:1px;font-size:80%;" />
    <a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}{if $offset}&amp;offset={$offset}{/if}{if $sort_mode ne ''}&amp;sort_mode={$sort_mode}{/if}&amp;remove={$items[user].itemId}" 
title="{tr}Delete{/tr}"><img src="pics/icons/cross.png" border="0" height="16" width="16" alt='{tr}Delete{/tr}' /></a>
  </td>
{/if}
</tr>
{assign var=itemoff value=$itemoff+1}
{/section}
</table>
{if $tiki_p_admin_trackers eq 'y'}
<div style="text-align:right;">
<script type='text/javascript'>
document.write("<input name=\"switcher\" id=\"clickall2\" type=\"checkbox\" onclick=\"switchCheckboxes(this.form,'action[]',this.checked)\"/><label for=\"clickall2\">{tr}Select All{/tr}</label>");
</script>
<select name="batchaction">
<option value="">{tr}with checked{/tr}</option>
<option value="delete">{tr}Delete{/tr}</option>
</select>
<input type="hidden" name="trackerId" value="{$trackerId}" />
<input type="submit" name="act" value="{tr}OK{/tr}" />
</div>
{/if}
</form>
{include file="tiki-pagination.tpl"}
</div>
{else}<!-- {cycle name=content assign=focustab} -->
{/if}

{* --------------------------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_create_tracker_items eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $prefs.feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<form enctype="multipart/form-data" action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />

<h2>{tr}Insert new item{/tr}</h2>
<table class="normal">
<tr class="formcolor"><td  class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>

{if $tracker_info.showStatus eq 'y' and ($tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y')}
<tr class="formcolor"><td>{tr}Status{/tr}</td>
<td>
<select name="status">
{foreach key=st item=stdata from=$status_types}
<option value="{$st}"{if $tracker_info.newItemStatus eq $st} selected="selected"{/if} 
style="background-image:url('{$stdata.image}');background-repeat:no-repeat;padding-left:17px;">{$stdata.label}</option>
{/foreach}
</select>
</td></tr>
{/if}


{section name=ix loop=$fields}
{assign var=fid value=$fields[ix].fieldId}

{* -------------------- header and others -------------------- *}
{if $fields[ix].isHidden eq 'n' or $fields[ix].isHidden eq '-'  or $tiki_p_admin_trackers eq 'y'}
{if $fields[ix].type ne 'x' and $fields[ix].type ne 'l' and $fields[ix].type ne 'q' and (($fields[ix].type ne 'u' and $fields[ix].type ne 'g' and $fields[ix].type ne 'I') or !$fields[ix].options_array[0] or $tiki_p_admin_trackers eq 'y')}
{if $fields[ix].type eq 'h'}
</table>
<h2>{$fields[ix].name}</h2>
<table class="normal">
{else}
{if ($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel" >{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}</td><td class="formcontent">
{elseif $stick eq 'y'}
<td class="formlabel right">{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}</td><td >
{else}
<tr class="formcolor"><td class="formlabel" >{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}
{if $fields[ix].type eq 'a' and $fields[ix].options_array[0] eq 1}
{* --- display quicktags --- *}
  <br />
  {if $prefs.quicktags_over_textarea neq 'y'}
    {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=$fields[ix].ins_id}
  {/if}
{/if}
</td><td colspan="3" class="formcontent" >
{/if}
{/if}

{* -------------------- system -------------------- *}
{if $fields[ix].type eq 's' and ($fields[ix].name eq "Rating" or $fields[ix].name eq tra("Rating")) and $tiki_p_tracker_vote_ratings eq 'y'}
	{section name=i loop=$fields[ix].options_array}
		<input name="{$fields[ix].ins_id}" type="radio" value="{$fields[ix].options_array[i]|escape}" />{$fields[ix].options_array[i]}
	{/section}
{/if}

{* -------------------- user selector -------------------- *}
{if $fields[ix].type eq 'u'}
{if !$fields[ix].options_array[0] or $tiki_p_admin_trackers eq 'y'}
<select name="{$fields[ix].ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
{if ( ! isset($fields[ix].itemChoices) || $fields[ix].itemChoices|@count eq 0 || in_array($one, $fields[ix].itemChoices) )}
{if $fields[ix].value}
<option value="{$one|escape}"{if $one eq $fields[ix].value} selected="selected"{/if}>{$one}</option>
{else}
<option value="{$one|escape}"{if $one eq $user and $fields[ix].options_array[0] ne '2'} selected="selected"{/if}>{$one}</option>
{/if}
{/if}
{/foreach}
</select>
{else}
{$user}
{/if}

{* -------------------- IP selector -------------------- *}
{elseif $fields[ix].type eq 'I'}
{if !$fields[ix].options_array[0] or $tiki_p_admin_trackers eq 'y'}
<input type="text" name="{$fields[ix].ins_id}" value="{if $input_err}{$fields[ix].value}{elseif $defaultvalues.fid}{$defaultvalues.$fid|escape}{else}{$IP}{/if}" />
{else}
{$IP}
{/if}

{* -------------------- group selector -------------------- *}
{elseif $fields[ix].type eq 'g'}
{if !$fields[ix].options_array[0] or $tiki_p_admin_trackers eq 'y'}
<select name="{$fields[ix].ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
{if ( ! isset($fields[ix].itemChoices) || $fields[ix].itemChoices|@count eq 0 || in_array($groups[ux], $fields[ix].itemChoices) )}
<option value="{$groups[ux]|escape}" {if $input_err and $fields[ix].value eq $groups[ux]} selected="selected"{/if}>{$groups[ux]}</option>
{/if}
{/section}
</select>
{else}
{$group}
{/if}

{* -------------------- category -------------------- *}
{elseif $fields[ix].type eq 'e'}
{if !empty($fields[ix].options_array[2]) && ($fields[ix].options_array[2] eq '1' or $fields[ix].options_array[2] eq 'y')}
<script type="text/javascript"> /* <![CDATA[ */
document.write('<div  class="categSelectAll"><input type="checkbox" id="clickall" onclick="switchCheckboxes(this.form,\'ins_cat_{$fields[ix].fieldId}[]\',this.checked)"/>{tr}Select All{/tr}</div>');
/* ]]> */</script>
{/if}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=2_$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].categories name=eforeach}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap"><input type={if $fields[ix].options_array[1] eq "radio"}"radio"{else}"checkbox"{/if} name="ins_cat_{$fields[ix].fieldId}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if $fields[ix].cat.$fcat eq 'y'}checked="checked"{/if}/><label for="cat{$i.categId}">{$iu.name}</label></td>{if !$smarty.foreach.eforeach.last}{cycle name=2_$fca}{else}{if $fields[ix].categories|@count%2}<td></td>{/if}</tr>{/if}
{/foreach}
</tr></table>

{* -------------------- image -------------------- *}
{elseif $fields[ix].type eq 'i'}
<input type="file" name="{$fields[ix].ins_id}" {if $input_err}value="{$fields[ix].value}"{/if}/>

{* -------------------- multimedia -------------------- *}
{elseif $fields[ix].type eq 'M'}
{if ($fields[ix].options_array[0] > '2')}
<input type="file" name="{$fields[ix].ins_id}" /><br />
{else}
<input type="text" name="{$fields[ix].ins_id}" value="{$fields[ix].value}" /><br />
{/if}

{* -------------------- text field / email -------------------- *}
{elseif $fields[ix].type eq 't' || $fields[ix].type eq 'm'}
{if $fields[ix].isMultilingual ne "y"}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}
{else}
<table>
    {foreach from=$fields[ix].lingualvalue item=ling}
    <TR><TD>{$ling.lang}</td><td>
            {if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
        <input type="text" name="{$fields[ix].ins_id}_{$ling.lang}" value="{$ling.value|escape}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} />
        {if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}
        </td></tr>
    {/foreach}
</table>
{/if}


{* -------------------- numeric field -------------------- *}
{elseif $fields[ix].type eq 'n'}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}

{* -------------------- static text -------------------- *}
{elseif $fields[ix].type eq 'S'}
{if $fields[ix].description}
{$fields[ix].description|escape|nl2br}
{/if}

{* -------------------- textarea -------------------- *}
{elseif $fields[ix].type eq 'a'}
{if $fields[ix].description}
<em>{$fields[ix].description|escape|nl2br}</em><br />
{/if}
{if $fields[ix].isMultilingual ne "y"}
  {if $prefs.quicktags_over_textarea eq 'y' and $fields[ix].options_array[0] eq 1}
    {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=`$fields[ix].ins_id`}
  {/if}
<textarea id="{$fields[ix].ins_id}" name="{$fields[ix].ins_id}" cols="{if $fields[ix].options_array[1] gt 1}{$fields[ix].options_array[1]}{else}50{/if}" 
rows="{if $fields[ix].options_array[2] gt 1}{$fields[ix].options_array[2]}{else}4{/if}">{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}</textarea>
{else}
<table>
{foreach from=$fields[ix].lingualvalue item=ling}
    <TR>
      <TD>{$ling.lang}</td>
      <td>
        {if $prefs.quicktags_over_textarea eq 'y' and $fields[ix].options_array[0] eq 1}
          {include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=ins_`$fields[ix].id`_`$ling.lang`}
        {/if}
        <textarea name="ins_{$fields[ix].id}_{$ling.lang}" id="area_{$fields[ix].id}" cols="{if $fields[ix].options_array[1] gt 1}{$fields[ix].options_array[1]}{else}50{/if}" rows="{if $fields[ix].options_array[2] gt 1}{$fields[ix].options_array[2]}{else}4{/if}">{$ling.value|escape}</textarea>
      </td>
    </tr>
    {/foreach}
</table>
{/if}

{* -------------------- date and time -------------------- *}
{elseif $fields[ix].type eq 'f'}
{html_select_date prefix=$fields[ix].ins_id time=$fields[ix].value start_year=$prefs.calendar_start_year end_year=$prefs.calendar_end_year field_order=$prefs.display_field_order}{if $fields[ix].options_array[0] ne 'd'} {tr}at{/tr} {html_select_time prefix=$fields[ix].ins_id time=$fields[ix].value display_seconds=false}{/if}

{* -------------------- drop down -------------------- *}
{elseif $fields[ix].type eq 'd' or $fields[ix].type eq 'D'}
<select name="{$fields[ix].ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
{assign var=otherValue value=$fields[ix].value}
<option value="">&nbsp;</option>
{section name=jx loop=$fields[ix].options_array}
<option value="{$fields[ix].options_array[jx]|escape}" {if $input_err}{if $fields[ix].value eq $fields[ix].options_array[jx]}{assign var=otherValue value=''}selected="selected"{/if}{elseif $defaultvalues.$fid eq $fields[ix].options_array[jx] or $fields[ix].defaultvalue eq $fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]|tr_if}</option>
{/section}
</select>
{if $fields[ix].type eq 'D'}
<br />{tr}Other:{/tr} <input type="text" name="{$fields[ix].ins_id}_other" value="{$otherValue|escape}" />
{/if}

{* -------------------- radio buttons -------------------- *}
{elseif $fields[ix].type eq 'R'}
{section name=jx loop=$fields[ix].options_array}
<input type="radio" name="{$fields[ix].ins_id}" value="{$fields[ix].options_array[jx]|escape}" {if $input_err}{if $fields[ix].value eq $fields[ix].options_array[jx]}checked="checked"{/if}{elseif $defaultvalues.$fid eq $fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</input>
{/section}

{* -------------------- checkbox -------------------- *}
{elseif $fields[ix].type eq 'c'}
<input type="checkbox" name="{$fields[ix].ins_id}" {if $input_err}{if $fields[ix].value eq 'y'}checked="checked"{/if}{elseif $defaultvalues.$fid eq 'y'}checked="checked"{/if}/>

{* -------------------- jscalendar ------------------- *}
{elseif $fields[ix].type eq 'j'}
{if $fields[ix].options_array[0] eq 'd'}
{jscalendar date=$now id=$fields[ix].ins_id fieldname=$fields[ix].ins_id showtime="n"}
{else}
{jscalendar date=$now id=$fields[ix].ins_id fieldname=$fields[ix].ins_id showtime="y"}
{/if}

{* -------------------- item link -------------------- *}
{elseif $fields[ix].type eq 'r'}
<select name="{$fields[ix].ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
{if $fields[ix].isMandatory ne 'y'}<option value="" />{/if}
{foreach key=id item=label from=$fields[ix].list}
<option value="{$label|escape}" {if $input_err}{if $fields[ix].value eq $label}selected="selected"{/if}{elseif $defaultvalue eq $label}selected="selected"{/if}>{if $fields[ix].listdisplay.$id eq ''}{$label}{else}{$fields[ix].listdisplay.$id}{/if}</option>
{/foreach}
</select>

{* -------------------- dynamic list -------------------- *}
{elseif $fields[ix].type eq 'w'}
<select name="{$fields[ix].ins_id}" {if $listfields.$fid.http_request}onchange="selectValues('trackerIdList={$listfields.$fid.http_request[0]}&amp;fieldlist={$listfields.$fid.http_request[3]}&amp;filterfield={$listfields.$fid.http_request[1]}&amp;status={$listfields.$fid.http_request[4]}&amp;mandatory={$listfields.$fid.http_request[6]}&amp;filtervalue='+escape(this.value),'{$listfields.$fid.http_request[5]}')"{/if}>
</select>


{* -------------------- User subscription -------------------- *}
{elseif $fields[ix].type eq 'U'}
<input type="text" name="{$fields[ix].ins_id}" value="{$fields[ix].value}" />


{* -------------------- Google Map -------------------- *}
{elseif $fields[ix].type eq 'G'}
<input type="text" name="{$fields[ix].ins_id}" value="{$fields[ix].value}" />
<br />{tr}Format : x,y,zoom - You can use Google Map Locator in the item view script.{/tr}

{* -------------------- country selector -------------------- *}
{elseif $fields[ix].type eq 'y'}
<select name="{$fields[ix].ins_id}">
<option value=""{if $fields[ix].value eq '' or $fields[ix].value eq 'None'} selected="selected"{/if}>&nbsp;</option>
{sortlinks}
{foreach item=flag from=$fields[ix].flags}
{if $flag ne 'None' and ( ! isset($fields[ix].itemChoices) || $fields[ix].itemChoices|@count eq 0 || in_array($flag, $fields[ix].itemChoices) )}
{capture name=flag}
{tr}{$flag}{/tr}
{/capture}
<option value="{$flag|escape}" {if $input_err}{if $fields[ix].value eq $flag}selected="selected"{/if}{elseif $flag eq $fields[ix].defaultvalue}selected="selected"{/if}{if $fields[ix].options_array[0] ne '1'} style="background-image:url('img/flags/{$flag}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;"{/if}>{$smarty.capture.flag|replace:'_':' '}</option>
{/if}
{/foreach}
{/sortlinks}
</select>

{/if}
{if $fields[ix].type ne 'a' and $fields[ix].type ne 'S'}
{if $fields[ix].description}
<br /><em>{$fields[ix].description|escape}</em>
{/if}
{/if}
</td>
{if (($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0]) eq '1' and $stick ne 'y'}
{assign var=stick value="y"}
{else}
</tr>{assign var=stick value="n"}
{/if}
{/if}
{/if}
{/section}

{* -------------------- antibot code -------------------- *}
{if $prefs.feature_antibot eq 'y' && $user eq ''}
{include file="antibot.tpl"}
{/if}

<tr class="formcolor"><td class="formlabel">&nbsp;</td><td colspan="3" class="formcontent">
<input type="submit" name="save" value="{tr}Save{/tr}" /> <input type="checkbox" name="viewitem"/> {tr}View inserted item{/tr}</td></tr>
</table>
</form>
<br /><em>{tr}Fields marked with a * are mandatory.{/tr}</em>
</div>
{/if}
{section name=ix loop=$fields}
{assign var=fid value=$fields[ix].fieldId}
{if $listfields.$fid.http_request}
<script type="text/javascript">
selectValues('trackerIdList={$listfields.$fid.http_request[0]}&fieldlist={$listfields.$fid.http_request[3]}&filterfield={$listfields.$fid.http_request[1]}&status={$listfields.$fid.http_request[4]}&mandatory={$listfields.$fid.http_request[6]}','{$listfields.$fid.http_request[5]}','{$fields[ix].ins_id}')
</script>
{/if}
{/section}
