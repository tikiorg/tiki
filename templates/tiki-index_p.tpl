{* Index we display a wiki page here *}
{include file="header.tpl"}
<div id="tiki-main">
  <div id="tiki-mid">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td id="centercolumn"><div id="tiki-center">

      
      {if $feature_page_title eq 'y'}<h1><a  href="tiki-index_p.php?page={$page}" class="pagetitle">{$page}</a>
{if $lock}
<img src="img/icons/lock_topic.gif" alt="{tr}locked{/tr}" title="{tr}locked by{/tr} {$page_user}" />
{/if}
</h1>{/if}
<table width="100%">
<tr>
<td>
{if $feature_wiki_description}
<small>{$description}</small>
{/if}
{if $cached_page eq 'y'}
<small>(cached)</small>
{/if}
</td>
<td style="text-align:right;">



{if $cached_page eq 'y'}
<a title="{tr}refresh{/tr}" href="tiki-index_p.php?page={$page}&amp;refresh=1"><img border="0" src="img/icons/ico_redo.gif" alt='{tr}refresh{/tr}' /></a>
{/if}

{if $user and $feature_wiki_notepad eq 'y' and $feature_notepad eq 'y' and $tiki_p_notepad eq 'y'}
<a title="{tr}Save to notepad{/tr}" href="tiki-index_p.php?page={$page}&amp;savenotepad=1"><img border="0" src="img/icons/ico_save.gif" alt="{tr}save{/tr}" /></a>
{/if}

</td>
</tr>
</table>
<div class="wikitext">{if $structure eq 'y'}
<div class="tocnav">
<table width='100%'><tr><td width='33%'>{if $struct_prev}<a class="tocnavlink" href="tiki-index_p.php?page={$struct_prev}">&lt;&lt; {$struct_prev}</a>{else}&nbsp;{/if}</td><td align='center' width='33%'><a class="tocnavlink" href="tiki-index_p.php?page={$struct_struct}">{$struct_struct}</a></td><td align='right' width='33%'>{if $struct_next}<a class="tocnavlink" href="tiki-index_p.php?page={$struct_next}">{$struct_next} &gt;&gt;</a>{else}&nbsp;{/if}</td></tr></table>
</div>
{/if}{$parsed}
{if $pages > 1}
	<div align="center">
		<a href="tiki-index.php?page={$page}&amp;pagenum={$first_page}"><img src='img/icons2/nav_first.gif' border='0' alt='{tr}First page{/tr}' title='{tr}First page{/tr}' /></a>

		<a href="tiki-index.php?page={$page}&amp;pagenum={$prev_page}"><img src='img/icons2/nav_dot_right.gif' border='0' alt='{tr}Previous page{/tr}' title='{tr}Previous page{/tr}' /></a>

		<small>{tr}page{/tr}:{$pagenum}/{$pages}</small>

		<a href="tiki-index.php?page={$page}&amp;pagenum={$next_page}"><img src='img/icons2/nav_dot_left.gif' border='0' alt='{tr}Next page{/tr}' title='{tr}Next page{/tr}' /></a>


		<a href="tiki-index.php?page={$page}&amp;pagenum={$last_page}"><img src='img/icons2/nav_last.gif' border='0' alt='{tr}Last page{/tr}' title='{tr}Last page{/tr}' /></a>
	</div>
{/if}
</div>


<p class="editdate">{tr}Last modification date{/tr}: {$lastModif|tiki_long_datetime} {tr}by{/tr} <a class="link" href="tiki-user_information.php?view_user={$lastUser}">{$lastUser}</a></p>

      
      </div>
      </td>
      
    </tr>
    </table>
  </div>
</div>
{include file="footer.tpl"}