{* $Id: tiki-view_tracker.tpl,v 1.1 2006-05-02 05:54:38 chibaguy Exp $ *}
<div class="tabt2"> {* div added and buttons moved up for tikipedia *}
<span class="button2"><a href="tiki-list_trackers.php" class="linkbut">{tr}List trackers{/tr}</a></span>
{if $filtervalue}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}" class="linkbut">{tr}View this tracker items{/tr}</a></span>
{/if}
{if $user}
<span class="button2"><a href="tiki-view_tracker.php?trackerId={$trackerId}&amp;monitor=1" class="linkbut">{tr}{$email_mon}{/tr}</a></span>
{/if}
{if $tiki_p_admin_trackers eq 'y'}
&nbsp;&nbsp;
<span class="button2"><a href="tiki-admin_trackers.php" class="linkbut">{tr}Admin trackers{/tr}</a></span>
<span class="button2"><a href="tiki-admin_trackers.php?trackerId={$trackerId}" class="linkbut">{tr}Edit this tracker{/tr}</a></span>
<span class="button2"><a href="tiki-admin_tracker_fields.php?trackerId={$trackerId}" class="linkbut">{tr}Edit fields{/tr}</a></span>
{/if}
{if $rss_tracker eq "y"}
<span class="button2"><a href="tiki-tracker_rss.php?trackerId={$trackerId}" class="linkbut"><img src='img/rss.png' border='0' alt='{tr}RSS feed{/tr}' title='{tr}RSS feed{/tr}' /></a></span>
{/if}
</div>
<h1><a class="pagetitle" href="tiki-view_tracker.php?trackerId={$trackerId}">{tr}Tracker{/tr}: {$tracker_info.name}</a></h1>
<div class="wikitext">{$tracker_info.description}</div>
{if $mail_msg}
<div class="wikitext">{$mail_msg}</div>
{/if}
<br />{* 

***  Display warnings about incorrect values and missing mandatory fields *** 

*}{if count($err_mandatory) > 0}<div class="simplebox highlight">
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
{if $feature_tabs eq 'y'}
{cycle name=tabs values="1,2,3" print=false advance=false}
<div id="page-bar">
{if $tiki_p_view_trackers eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Tracker{/tr} <i>{$tracker_info.name}</i></a></span>
{/if}
{if $tiki_p_create_tracker_items eq 'y'}
<span id="tab{cycle name=tabs advance=false assign=tabi}{$tabi}" class="tabmark" style="border-color:{if $cookietab eq $tabi}black{else}white{/if};"><a href="javascript:tikitabs({cycle name=tabs},3);">{tr}Insert new item{/tr}</a></span>
{/if}
</div>
{/if}

{cycle name=content values="1,2,3" print=false advance=false}
{* -------------------------------------------------- tab with list --- *}
{if $tiki_p_view_trackers eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>

{if (($tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y') or $tiki_p_admin_trackers eq 'y') or $show_filters eq 'y'}
<form action="tiki-view_tracker.php" method="get">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />
{if $status}<input type="hidden" name="status" value="{$status}" />{/if}
{if $sort_mode}<input type="hidden" name="sort_mode" value="{$sort_mode}" />{/if}
<table class="normal"><tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
{foreach key=st item=stdata from=$status_types}
<td><div class="{$stdata.class}">
<a href="tiki-view_tracker.php?trackerId={$trackerId}{if $filtervalue}&amp;filtervalue={$filtervalue|escape:"url"}{/if}{if $filterfield}&amp;filterfield={$filterfield|escape:"url"}{/if}{if $sort_mode}&amp;sort_mode={$sort_mode}{/if}&amp;status={$stdata.statuslink}" 
class="statusimg"><img src="{$stdata.image}" title="{$stdata.label}" alt="{$stdata.label}" align="top" border="0" width="12" height="12" /></a></div></td>
{/foreach}
{/if}
<td class="formcolor" style="width:100%;">
{assign var=cnt value=0}
{foreach key=fid item=field from=$listfields}
{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
{if $field.type eq 'c'}
<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}"><select name="filtervalue[{$fid}]">
<option value="y"{if $filtervalue eq 'y'} selected="selected"{/if}>{tr}Yes{/tr}</option>
<option value="n"{if $filtervalue eq 'n'} selected="selected"{/if}>{tr}No{/tr}</option>
</select></div>
{elseif $field.type eq 'd'}
<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}"><select name="filtervalue[{$fid}]">
{section name=jx loop=$field.options_array}
<option value="{$field.options_array[jx]|escape}" {if $filtervalue eq $field.options_array[jx]}selected="selected"{/if}>{$field.options_array[jx]}</option>
{/section}
</select></div>

{elseif $field.type eq 'e'}{* category *}

<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}">
<table><tr>

{cycle name=rows values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$field.categories}
  <td width="50%" nowrap="nowrap">
    <input type="checkbox" name="filtervalue[{$fid}][]" value="{$iu.categId}" id="cat{$iu.categId}" 
      {if $fid == $filterfield && is_array($filtervalue) && in_array($iu.categId,$filtervalue)} checked{/if}
    />
    <label for="cat{$i.categId}">{$iu.name}</label>
  </td>
  {cycle name=rows}
{/foreach}

</tr></table>
</div>

{else}

<div style="display:{if $filterfield eq $fid}block{else}none{/if};" id="fid{$fid}"><input type="text" name="filtervalue[{$fid}]" value="{if $fid == $filterfield}{$filtervalue}{/if}" /></div>
{/if}
{assign var=cnt value=$cnt+1}
{/if}
{/foreach}
</td>
{if $show_filters eq 'y'}
<td>
<script type="text/javascript">
fields = new Array({$cnt})
{assign var=c value=0}
{foreach key=fid item=field from=$listfields}
{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
fields[{$c}] = '{$fid}'
{assign var=c value=$c+1}
{/if}
{/foreach}
</script>
<select name="filterfield" onchange="multitoggle(fields,this.options[selectedIndex].value);">
<option value="">{tr}Choose a filter{/tr}</option>
{foreach key=fid item=field from=$listfields}
{if $field.isSearchable eq 'y' and $field.type ne 'f' and $field.type ne 'j' and $field.type ne 'i'}
<option value="{$fid}"{if $fid eq $filterfield} selected="selected"{/if}>{$field.name|truncate:65:"..."}</option>
{assign var=filter_button value='y'}
{/if}
{/foreach}
</select>
</td>
{/if}

{if $filter_button eq 'y'}
<td><input type="submit" name="filter" value="{tr}filter{/tr}" /></td>
{/if}
</tr>
</table>
<div align='left'>{$item_count}{if $item_count eq 1}{tr} item found{/tr}{else}{tr} items found{/tr}{/if}</div>
</form>
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
<table class="normal">
<tr>
{if $tracker_info.showStatus eq 'y' or ($tracker_info.showStatusAdminOnly eq 'y' and $tiki_p_admin_trackers eq 'y')}
<td class="heading auto" style="width:20px;">&nbsp;</td>
{/if}
{section name=ix loop=$fields}
{if $fields[ix].type eq 'l' and $fields[ix].isTblVisible eq 'y'}
<td class="heading auto">{$fields[ix].name|default:"&nbsp;"}</td>
{elseif $fields[ix].type eq 's' and $fields[ix].name eq "Rating" and $fields[ix].isTblVisible eq 'y'}
	<td class="heading auto"{if $tiki_p_tracker_vote_ratings eq 'y' and $user ne ''} colspan="2"{/if}>
		<a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}trackerId={$trackerId}
	        &amp;offset={$offset}&amp;sort_mode=f_{if $sort_mode eq 'f_'|cat:$fields[ix].fieldId|cat:'_asc'}
		{$fields[ix].fieldId|escape:"url"}_desc{else}{$fields[ix].fieldId|escape:"url"}_asc{/if}">
			{$fields[ix].name|truncate:255:"..."|default:"&nbsp;"}
		</a>
	</td>
	{assign var=rateFieldId value=$fields[ix].fieldId}
{elseif $fields[ix].isTblVisible eq 'y' and $fields[ix].type ne 'x' and $fields[ix].type ne 'h'}
<td class="heading auto"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode=f_{if $sort_mode eq
'f_'|cat:$fields[ix].fieldId|cat:'_asc'}{$fields[ix].fieldId|escape:"url"}_desc{else}{$fields[ix].fieldId|escape:"url"}_asc{/if}{if $filterfield}&amp;filterfield={$filterfield}&amp;filtervalue={$filtervalue}{/if}">{$fields[ix].name|truncate:255:"..."|default:"&nbsp;"}</a></td>
{/if}
{/section}
{if $tracker_info.showCreated eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?{if $status}status={$status}&amp;{/if}{if $initial}initial={$initial}&amp;{/if}{if $find}find={$find}&amp;{/if}trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={if 
$sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}created{/tr}</a></td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td class="heading"><a class="tableheading" href="tiki-view_tracker.php?status={$status}&amp;{if $initial}initial={$initial}&amp;{/if}find={$find}&amp;trackerId={$trackerId}&amp;offset={$offset}{section 
name=ix loop=$fields}{if $fields[ix].value}&amp;{$fields[ix].name}={$fields[ix].value}{/if}{/section}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}lastModif{/tr}</a></td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td class="heading" width="5%">{tr}coms{/tr}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and  $tracker_info.showAttachments eq 'y'}
<td class="heading" width="5%">{tr}atts{/tr}</td>
{if $tiki_p_admin_trackers eq 'y'}<td class="heading" width="5%">{tr}dls{/tr}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td class="heading" width="5%">&nbsp;</td>
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
<a class="tablename" href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=view&amp;offset={$offset}&amp;reloff={$itemoff}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}">
{/if}

{if  ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[2]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[2]}</span>{/if}

{if $items[user].field_values[ix].type eq 'f'}
{if $items[user].field_values[ix].value}{$items[user].field_values[ix].value|tiki_short_datetime|truncate:255:"..."|default:"&nbsp;"}{else}&nbsp;{/if}

{elseif $items[user].field_values[ix].type eq 'c'}
{$items[user].field_values[ix].value|replace:"y":"Yes"|replace:"n":"No"|replace:"on":"Yes"}

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

{elseif $items[user].field_values[ix].type eq 'm'}
{$items[user].field_values[ix].value|default:"&nbsp;"}

{elseif $items[user].field_values[ix].type eq 'e'}
{foreach item=ii from=$items[user].field_values[ix].categs}{$ii.name}<br />{/foreach}

{elseif $items[user].field_values[ix].type eq 'y'}
{assign var=o_opt value=$items[user].field_values[ix].options_array[0]}
{if $o_opt ne '1'}<img border="0" src="img/flags/{$items[user].field_values[ix].value}.gif" />{/if}
{if $o_opt ne '1' and $o_opt ne '2'}&nbsp;{/if}
{if $o_opt ne '2'}{tr}{$items[user].field_values[ix].value}{/tr}{/if}

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
{if $items[user].field_values[ix].value}{$items[user].field_values[ix].value|tiki_short_datetime|default:"&nbsp;"}{else}&nbsp;{/if}
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
{assign var=o_opt value=$items[user].field_values[ix].options_array[0]}
{if $o_opt eq '0' or $o_opt eq 2}<img border="0" src="img/flags/{$items[user].field_values[ix].value}.gif" />{/if}
{if $o_opt eq '0'}&nbsp;{/if}
{if $o_opt eq '0' or $o_opt eq 1}{tr}{$items[user].field_values[ix].value}{/tr}{/if}
</td>

{elseif $items[user].field_values[ix].type eq 's' and $items[user].field_values[ix].name eq "Rating" and $tiki_p_tracker_view_ratings eq 'y'}
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
				<a href="{$smarty.server.PHP_SELF}{if $query_string}?{$query_string}{else}?{/if}
					trackerId={$items[user].trackerId}
					&amp;rateitemId={$items[user].itemId}
					&amp;fieldId={$rateFieldId}
					&amp;rate_{$items[user].trackerId}=NULL"
					class="linkbut">-</a>
			{/if}
				{section name=i loop=$items[user].field_values[ix].options_array}
					{if $items[user].field_values[ix].options_array[i] eq $items[user].my_rate}
						<b class="linkbut highlight">{$items[user].field_values[ix].options_array[i]}</b>
					{else}
						<a href="{$smarty.server.PHP_SELF}?
						trackerId={$items[user].trackerId}
						&amp;rateitemId={$items[user].itemId}
						&amp;fieldId={$rateFieldId}
						&amp;rate_{$items[user].trackerId}={$items[user].field_values[ix].options_array[i]}"
						class="linkbut">{$items[user].field_values[ix].options_array[i]}</a>
					{/if}
				{/section}
			</span>
		</td>
	{/if}

{elseif $items[user].field_values[ix].type ne 'x' and $items[user].field_values[ix].type ne 'h'}
<td class="auto">
{if  ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[2]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[2]}&nbsp;</span>{/if}
{$items[user].field_values[ix].value|truncate:255:"..."|default:"&nbsp;"}
{if ($items[user].field_values[ix].type eq 't' or $items[user].field_values[ix].type eq 'n' or $items[user].field_values[ix].type eq 'c') 
 and $items[user].field_values[ix].options_array[3]}<span class="formunit">&nbsp;{$items[user].field_values[ix].options_array[3]}</span>{/if}
</td>
{/if}
{/if}
{/if}
{/section}


{if $tracker_info.showCreated eq 'y'}
<td>{$items[user].created|tiki_short_datetime}</td>
{/if}
{if $tracker_info.showLastModif eq 'y'}
<td>{$items[user].lastModif|tiki_short_datetime}</td>
{/if}
{if $tracker_info.useComments eq 'y' and $tracker_info.showComments eq 'y'}
<td  style="text-align:center;">{$items[user].comments}</td>
{/if}
{if $tracker_info.useAttachments eq 'y' and $tracker_info.showAttachments eq 'y'}
<td  style="text-align:center;"><a href="tiki-view_tracker_item.php?itemId={$items[user].itemId}&amp;show=att&amp;offset={$offset}&amp;reloff={$itemoff}{foreach key=urlkey item=urlval from=$urlquery}{if $urlval}&amp;{$urlkey}={$urlval|escape:"url"}{/if}{/foreach}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}" 
link="{tr}list attachments{/tr}"><img src="img/icons/folderin.gif" border="0" alt="{tr}List Attachments{/tr}" 
/></a> {$items[user].attachments}</td>
{if $tiki_p_admin_trackers eq 'y'}<td  style="text-align:center;">{$items[user].downloads}</td>{/if}
{/if}
{if $tiki_p_admin_trackers eq 'y'}
<td><a class="link" href="tiki-view_tracker.php?status={$status}&amp;trackerId={$trackerId}&amp;offset={$offset}&amp;sort_mode={$sort_mode}{section name=mix loop=$fields}{if $fields[mix].value}&amp;{$fields[mix].name}={$fields[mix].value}{/if}{/section}&amp;remove={$items[user].itemId}" 
title="{tr}delete{/tr}"><img src="img/icons2/delete.gif" border="0" height="16" width="16" alt='{tr}delete{/tr}' /></a></td>
{/if}
</tr>
{assign var=itemoff value=$itemoff+1}
{/section}
</table>
{include file="tiki-pagination.tpl"}
</div>
{else}<!-- {cycle name=content assign=focustab} -->
{/if}

{* --------------------------------------------------------------------------------- tab with edit --- *}
{if $tiki_p_create_tracker_items eq 'y'}
<div id="content{cycle name=content assign=focustab}{$focustab}" class="tabcontent"{if $feature_tabs eq 'y'} style="display:{if $focustab eq $cookietab}block{else}none{/if};"{/if}>
<form enctype="multipart/form-data" action="tiki-view_tracker.php" method="post">
<input type="hidden" name="trackerId" value="{$trackerId|escape}" />

<h2>{tr}Insert new item{/tr}</h2>
<table class="normal">
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" /></td></tr>

{if $tracker_info.showStatus eq 'y' and $tracker_info.showStatusAdminOnly ne 'y' or $tiki_p_admin_trackers eq 'y'}
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

{if $fields[ix].type ne 'x' and $fields[ix].type ne 'l'}
{if $fields[ix].type eq 'h'}
</table>
<h2>{$fields[ix].name}</h2>
<table class="normal">
{else}
{if ($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0] eq '1'}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}</td><td nowrap="nowrap">
{elseif $stick eq 'y'}
<td class="formlabel right">{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}</td><td nowrap="nowrap">
{else}
<tr class="formcolor"><td class="formlabel">{$fields[ix].name}{if $fields[ix].isMandatory eq 'y'} *{/if}
{if $fields[ix].type eq 'a' and $fields[ix].options_array[0] eq 1}
<br />
{include file=tiki-edit_help_tool.tpl qtnum=$fid area_name=$fields[ix].ins_id}
{/if}
</td><td colspan="3" nowrap="nowrap">
{/if}
{/if}

{* -------------------- system -------------------- *}
{if $fields[ix].type eq 's' and $fields[ix].name eq "Rating" and $tiki_p_tracker_vote_ratings eq 'y'}
	{section name=i loop=$fields[ix].options_array}
		<input name="{$fields[ix].ins_id}" type="radio" value="{$fields[ix].options_array[i]|escape}" />{$fields[ix].options_array[i]}
	{/section}
{/if}

{* -------------------- user selector -------------------- *}
{if $fields[ix].type eq 'u'}
{if !$fields[ix].options or ($fields[ix].options eq '1' and $tiki_p_admin_trackers eq 'y')}
<select name="{$fields[ix].ins_id}">
<option value="">{tr}None{/tr}</option>
{foreach key=id item=one from=$users}
{if $fields[ix].value}
<option value="{$one|escape}"{if $one eq $fields[ix].value} selected="selected"{/if}>{$one}</option>
{elseif $user}
<option value="{$one|escape}"{if $one eq $user} selected="selected"{/if}>{$one}</option>
{else}
<option value="{$one|escape}">{$one}</option>
{/if}
{/foreach}
</select>
{elseif $fields[ix].options eq 1 and $user}
{$user}
{/if}

{* -------------------- group selector -------------------- *}
{elseif $fields[ix].type eq 'g'}
{if !$fields[ix].options or ($fields[ix].options eq '1' and $tiki_p_admin_trackers eq 'y')}
<select name="{$fields[ix].ins_id}">
<option value="">{tr}None{/tr}</option>
{section name=ux loop=$groups}
<option value="{$groups[ux]|escape}" {if $input_err and $fields[ix].value eq $groups[ux]} selected="selected"{/if}>{$groups[ux]}</option>
{/section}
</select>
{elseif $fields[ix].options eq 1 and $group}
{$group}
{/if}

{* -------------------- category -------------------- *}
{elseif $fields[ix].type eq 'e'}
{assign var=fca value=$fields[ix].options}
<table width="100%"><tr>{cycle name=2_$fca values=",</tr><tr>" advance=false print=false}
{foreach key=ku item=iu from=$fields[ix].categories}
{assign var=fcat value=$iu.categId }
<td width="50%" nowrap="nowrap"><input type="checkbox" name="ins_cat_{$fields[ix].fieldId}[]" value="{$iu.categId}" id="cat{$iu.categId}" {if $fields[ix].cat.$fcat eq 'y'}checked="checked"{/if}/><label for="cat{$i.categId}">{$iu.name}</label></td>{cycle name=2_$fca}
{/foreach}
</tr></table>

{* -------------------- image -------------------- *}
{elseif $fields[ix].type eq 'i'}
<input type="file" name="{$fields[ix].ins_id}" {if $input_err}value="{$fields[ix].value}"{/if}/>

{* -------------------- text field / email -------------------- *}
{elseif $fields[ix].type eq 't' || $fields[ix].type eq 'm'}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}

{* -------------------- numeric field -------------------- *}
{elseif $fields[ix].type eq 'n'}
{if $fields[ix].options_array[2]}<span class="formunit">{$fields[ix].options_array[2]}&nbsp;</span>{/if}
<input type="text" name="{$fields[ix].ins_id}" {if $fields[ix].options_array[1]}size="{$fields[ix].options_array[1]}" maxlength="{$fields[ix].options_array[1]}"{/if} value="{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}" />
{if $fields[ix].options_array[3]}<span class="formunit">&nbsp;{$fields[ix].options_array[3]}</span>{/if}

{* -------------------- textarea -------------------- *}
{elseif $fields[ix].type eq 'a'}
<textarea id="{$fields[ix].ins_id}" name="{$fields[ix].ins_id}" cols="{if $fields[ix].options_array[1] gt 1}{$fields[ix].options_array[1]}{else}50{/if}" 
rows="{if $fields[ix].options_array[2] gt 1}{$fields[ix].options_array[2]}{else}4{/if}">{if $input_err}{$fields[ix].value}{else}{$defaultvalues.$fid|escape}{/if}</textarea>

{* -------------------- date and time -------------------- *}
{elseif $fields[ix].type eq 'f'}
{html_select_date prefix=$fields[ix].ins_id time=$fields[ix].value start_year="-4" end_year="+4"} {tr}at{/tr} {html_select_time prefix=$fields[ix].ins_id time=$fields[ix].value display_seconds=false}

{* -------------------- drop down -------------------- *}
{elseif $fields[ix].type eq 'd'}
<select name="{$fields[ix].ins_id}">
{if $fields[ix].isMandatory ne 'y'}<option value="" />{/if}
{section name=jx loop=$fields[ix].options_array}
<option value="{$fields[ix].options_array[jx]|escape}" {if $input_err}{if $fields[ix].value eq $fields[ix].options_array[jx]}selected="selected"{/if}{elseif $defaultvalues.$fid eq $fields[ix].options_array[jx]}selected="selected"{/if}>{$fields[ix].options_array[jx]}</option>
{/section}
</select>

{* -------------------- checkbox -------------------- *}
{elseif $fields[ix].type eq 'c'}
<input type="checkbox" name="{$fields[ix].ins_id}" {if $input_err}{if $fields[ix].value eq 'y'}checked="checked"{/if}{elseif $defaultvalues.$fid eq 'y'}checked="checked"{/if}/>

{* -------------------- jscalendar ------------------- *}
{elseif $fields[ix].type eq 'j'}
<input type="hidden" name="ins_{$fields[ix].ins_id}" value="" id="{$fields[ix].ins_id}" />
<span id="disp_{$fields[ix].ins_id}" class="daterow">{$fields[ix].value|default:$smarty.now|tiki_long_date}</span>
<script type="text/javascript">
{literal}Calendar.setup( { {/literal}
date        : "{$fields[ix].value|default:$now|date_format:"%B %e, %Y %H:%M"}",      // initial date
inputField  : "{$fields[ix].ins_id}",      // ID of the input field
ifFormat    : "%s",    // the date format
displayArea : "disp_{$fields[ix].ins_id}",       // ID of the span where the date is to be shown
daFormat    : "{$tiki_long_date}",  // format of the displayed date
showsTime   : true,
singleClick : true,
align       : "bR"
{literal} } );{/literal}
</script>

{* -------------------- item link -------------------- *}
{elseif $fields[ix].type eq 'r'}
<select name="{$fields[ix].ins_id}">
{if $fields[ix].isMandatory ne 'y'}<option value="" />{/if}
{foreach key=id item=label from=$fields[ix].list}
<option value="{$label|escape}" {if $input_err}{if $fields[ix].value eq $label}selected="selected"{/if}{elseif $defaultvalue eq $label}selected="selected"{/if}>{$label}</option>
{/foreach}
</select>

{* -------------------- country selector -------------------- *}
{elseif $fields[ix].type eq 'y'}
<select name="{$fields[ix].ins_id}">
{sortlinks}
{foreach item=flag from=$fields[ix].flags}
<option value="{$flag|escape}" {if $input_err}{if $fields[ix].value eq $flag}selected="selected"{/if}{elseif $flag eq $fields[ix].defaultvalue}selected="selected"{/if}
style="background-image:url('img/flags/{$flag}.gif');background-repeat:no-repeat;padding-left:25px;padding-bottom:3px;">{tr}{$flag}{/tr}</option>
{/foreach}
{/sortlinks}
</select>

{/if}

{if (($fields[ix].type eq 'c' or $fields[ix].type eq 't' or $fields[ix].type eq 'n') and $fields[ix].options_array[0]) eq '1' and $stick ne 'y'}
</td>{assign var=stick value="y"}
{else}
</td></tr>{assign var=stick value="n"}
{/if}
{/if}
{/section}
<tr class="formcolor"><td>&nbsp;</td><td colspan="3"><input type="submit" name="save" value="{tr}save{/tr}" /> <input type="checkbox" name="viewitem"/> {tr}View inserted item{/tr}</td></tr>
</table>
</form>
<br /><em>{tr}fields marked with a * are mandatory{/tr}</em>
</div>
{/if}
