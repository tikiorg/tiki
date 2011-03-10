{if $prefs.feature_page_title eq 'y' && !$is_slideshow eq 'y'}<h1><a  href="tiki-backlinks.php?page={$page}" title="{tr}backlinks to{/tr} {$page}" class="pagetitle">{$page}</a></h1>{/if}
<div class="wikitext">{$parsed}</div>
{if !isset($smarty.request.clean)}
  {if isset($prefs.wiki_authors_style) && $prefs.wiki_authors_style eq 'business'}
  <p class="editdate">
    {tr}Last edited by{/tr} {$lastUser}
    {section name=author loop=$contributors}
    {if $smarty.section.author.first}, {tr}based on work by{/tr}
    {else}
      {if !$smarty.section.author.last},
      {else} {tr}and{/tr}
      {/if}
    {/if}
    {$contributors[author]}
    {/section}.<br />                                         
    {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
  </p>
  {elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'collaborative'}
  <p class="editdate">
    {tr}Contributors to this page:{/tr} {$lastUser}
    {section name=author loop=$contributors}
    {if !$smarty.section.author.last},
    {else} {tr}and{/tr}
    {/if}
    {$contributors[author]}
    {/section}.<br />
    {tr}Page last modified on{/tr} {$lastModif|tiki_long_datetime}.
  </p>
  {elseif isset($prefs.wiki_authors_style) &&  $prefs.wiki_authors_style eq 'none'}
  {else}
  <p class="editdate">
    {tr}Created by:{/tr} {$creator}
    {tr}Last Modification:{/tr} {$lastModif|tiki_long_datetime} {tr}by{/tr} {$lastUser|userlink}
  </p>
  {/if}

  {if (!$prefs.page_bar_position or $prefs.page_bar_position eq 'bottom' or $prefs.page_bar_position eq 'both') and $machine_translate_to_lang == ''}
	  {include file='tiki-page_bar.tpl'}
  {/if}
  
  {if $is_slideshow eq 'y'}
  	<div id="tiki_slideshow_buttons" style="display: none;">
		<a href="#" onclick="$.s5.first(); return false;" title="{tr}First{/tr}">
			<img src="lib/jquery/jquery.s5/images/resultset_first.png" alt="{tr}First{/tr}" /></a> 
		<a href="#" onclick="$.s5.prev(); return false;" title="{tr}Prev{/tr}">
			<img src="lib/jquery/jquery.s5/images/resultset_previous.png" alt="{tr}Prev{/tr}" /></a> 
		<a href="#" onclick="$.s5.next(); return false;" title="{tr}Next{/tr}">
			<img src="lib/jquery/jquery.s5/images/resultset_next.png" alt="{tr}Next{/tr}" /></a> 
		<a href="#" onclick="$.s5.last(); return false;" title="{tr}Last{/tr}">
			<img src="lib/jquery/jquery.s5/images/resultset_last.png" alt="{tr}Last{/tr}" /></a>
		<a href="#" onclick="$.s5.listSlideTitles(this); return false;" title="{tr}Jump To Slide{/tr}">
			<img src="lib/jquery/jquery.s5/images/layers.png" alt="{tr}Jump To Slide{/tr}" /></a>
		<a href="#" onclick="$.s5.play(); return false;" title="{tr}Play{/tr}">
			<img src="lib/jquery/jquery.s5/images/control_play_blue.png" alt="{tr}Play{/tr}" /></a>
		<a href="#" onclick="$.s5.pause(); return false;" title="{tr}Pause{/tr}">
			<img src="lib/jquery/jquery.s5/images/control_pause_blue.png" alt="{tr}Pause{/tr}" /></a>
		<a href="#" onclick="$.s5.stop(); return false;" title="{tr}Stop{/tr}">
			<img src="lib/jquery/jquery.s5/images/control_stop_blue.png" alt="{tr}Stop{/tr}" /></a>
		<a href="#" onclick="$.s5.getNote(); return false;" title="{tr}Notes{/tr}">
			<img src="lib/jquery/jquery.s5/images/note.png" alt="{tr}Notes{/tr}" /></a>
		<a href="#" onclick="$.s5.toggleLoop(); return false;" title="{tr}Toggle Loop{/tr}">
			<img src="lib/jquery/jquery.s5/images/arrow_rotate_clockwise.png" alt="{tr}Toggle Loop{/tr}" /></a>
		<a href="tiki-index.php?page={$page}" title="{tr}Exit{/tr}">
			<img src="pics/icons/cross.png" alt="{tr}Exit{/tr}" /></a>
	</div>
	<div id="tiki_slideshowNote_buttons" style="display: none;">
		<a href="#" onclick="window.opener.$.s5.first(); return false;" title="{tr}First{/tr}">{tr}First{/tr}</a> 
		<a href="#" onclick="window.opener.$.s5.prev(); return false;" title="{tr}Prev{/tr}">{tr}Prev{/tr}</a> 
		<a href="#" onclick="window.opener.$.s5.next(); return false;" title="{tr}Next{/tr}">{tr}Next{/tr}</a> 
		<a href="#" onclick="window.opener.$.s5.last(); return false;" title="{tr}Last{/tr}">{tr}Last{/tr}</a>
		<a href="#" onclick="window.opener.$.s5.listSlideTitles(this, document.body); return false;" title="{tr}Jump To Slide{/tr}">{tr}Jump To Slide{/tr}</a>
		<a href="#" onclick="window.opener.$.s5.play(); return false;" title="{tr}Play{/tr}">{tr}Play{/tr}</a>
		<a href="#" onclick="window.opener.$.s5.pause(); return false;" title="{tr}Pause{/tr}">{tr}Pause{/tr}</a>
		<a href="#" onclick="window.opener.$.s5.stop(); return false;" title="{tr}Stop{/tr}">{tr}Stop{/tr}</a>
		<a href="#" onclick="window.opener.$.s5.toggleLoop(); return false;" title="{tr}Toggle Loop{/tr}">{tr}Toggle Loop{/tr}</a>
		<a href="#" onclick="window.opener.window.location = 'tiki-index.php?page={$page}'" title="{tr}Exit{/tr}">{tr}Exit{/tr}</a>
	</div>
  {/if}
{/if}
