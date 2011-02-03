{* $Id$ 
 *
 * MOD-SINCE_LAST_VISIT_NEW
 * Template for the module mod-since_last_visit_new. 
 *}
{if $user}
	{tikimodule error=$module_params.error title=$tpl_module_title name="since_last_visit_new" flip=$module_params.flip decorations=$module_params.decorations nobox=$module_params.nobox notitle=$module_params.notitle}
	<div style="margin-bottom: 5px; text-align:center;">
		{if $prefs.feature_calendar eq 'y' && $date_as_link eq 'y'}
			<a class="linkmodule" href="tiki-calendar.php?todate={$slvn_info.lastLogin}" title="{tr}click to edit{/tr}">
		{/if}
		<b>{$slvn_info.lastLogin|tiki_short_date}</b>
		{if $prefs.feature_calendar eq 'y'}
			</a>
		{/if}
	</div>
	{if $slvn_info.cant == 0}
		<div class="separator">{tr}Nothing has changed{/tr}</div>
	{else}
		{if $prefs.feature_jquery_ui eq "y" and $use_jquery_ui eq "y"}
			{assign var=fragment value=1}
			<div id="mytabs">
	  		<ul>
				{foreach key=pos item=slvn_item from=$slvn_info.items}
					{if $slvn_item.count > 0 }
						<li>
							<a href="#fragment-{$fragment}">
								{if $pos eq "blogs" or $pos eq "blogPosts"}
									<img src="pics/large/blogs.png" alt="{tr}Blogs{/tr}" title="{tr}Blogs{/tr}"/>
								{elseif $pos eq "articles"}
									<img src="pics/large/stock_bold.png" alt="{tr}Articles{/tr}" title="{tr}Articles{/tr}"/>
								{elseif $pos eq "posts"}
									<img src="pics/large/stock_index.png" alt="{tr}Forums{/tr}" title="{tr}Forums{/tr}"/>
								{elseif $pos eq "fileGalleries" or $pos eq "files" }
									<img src="pics/large/file-manager.png" alt="{tr}File Gallery{/tr}" title="{tr}File Gallery{/tr}"/>
								{elseif $pos eq "poll"}
									<img src="pics/large/stock_missing-image.png" alt="{tr}Poll{/tr}" title="{tr}Poll{/tr}"/>
								{elseif $pos eq "pages"}
									<img src="pics/large/wikipages.png" alt="{tr}Wiki{/tr}" title="{tr}Wiki{/tr}"/>
								{elseif $pos eq "comments"}
									<img src="pics/large/comments.png" alt="{tr}Comments{/tr}" title="{tr}Comments{/tr}"/>
								{elseif $pos eq "forums"}
									<img src="pics/large/stock_index.png" alt="{tr}Forums{/tr}" title="{tr}Forums{/tr}"/>
								{elseif $pos eq "trackers"}
									<img src="pics/large/gnome-settings-font.png" alt="{tr}Trackers{/tr}" title="{tr}Trackers{/tr}"/>
								{elseif $pos eq "users"}
									<img src="pics/large/vcard.png" alt="{tr}Users{/tr}" title="{tr}Users{/tr}"/>
								{else}
									{$pos}
								{/if}
							</a>
						</li>
					{assign var=fragment value=`$fragment+1`}
					{/if}
				{/foreach}
			</ul>
			{assign var=fragment value=1}
		{/if}
		{foreach key=pos item=slvn_item from=$slvn_info.items}
			{if $slvn_item.count > 0 }
				{if $prefs.feature_jquery_ui eq "y" and $use_jquery_ui eq "y"}<div id="fragment-{$fragment}">{/if}
				{assign var=cname value=$slvn_item.cname}
				{if $slvn_item.count eq $module_rows}
					<div class="separator"><a class="separator" href="javascript:flip('{$cname}');">{tr}Multiple{/tr} {$slvn_item.label}, {tr}including{/tr}</a></div>
				{else}
					<div class="separator"><a class="separator" href="javascript:flip('{$cname}');">{$slvn_item.count}&nbsp;{$slvn_item.label}</a></div>
				{/if}
				{assign var=showcname value=show_$cname}
	
	        	{if $pos eq 'trackers' or $pos eq 'utrackers'}
					<div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}{$default_folding}{else}{$opposite_folding}{/if};">
	
	        			{****** Parse out the trackers *****}
					 	{foreach key=tp item=tracker from=$slvn_item.tid}
					 		{assign var=tcname value=$tracker.cname}
					 		<div class="separator" style="margin-left: 10px; display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}{$default_folding}{else}{$opposite_folding}{/if};">
					 			{assign var=showtcname value=show_$tcname}
					 			<a class="separator" href="javascript:flip('{$tcname}');">{$tracker.count}&nbsp;{$tracker.label|escape}</a>
					 			<div id="{$tcname}" style="display:{if !isset($cookie.$showtcname) or $cookie.$showtcname eq 'y'}{$default_folding}{else}{$opposite_folding}{/if};">
					 				{if $nonums != 'y'}<ol>{else}<ul>{/if}
					 				{section name=xx loop=$tracker.list}
					 					<li><a  class="linkmodule"
					 								href="{$tracker.list[xx].href|escape}"
					 								title="{$tracker.list[xx].title|escape}">{if $tracker.list[xx].label == ''}-{else}{$tracker.list[xx].label|escape}{/if}
					 							</a>
					 					</li>
					 				{/section}
					 				{if $nonums != 'y'}</ol>{else}</ul>{/if}
					 			</div>
					 		</div>
					 	{/foreach}
	        		   {****** End tracker section *****}
					</div>
				{else}
					 <div id="{$cname}" style="display:{if !isset($cookie.$showcname) or $cookie.$showcname eq 'y'}{$default_folding}{else}{$opposite_folding}{/if};">
						{if $nonums != 'y'}<ol>{else}<ul>{/if}
						{section name=ix loop=$slvn_item.list}
							<li>
								<a  class="linkmodule" 
									href="{$slvn_item.list[ix].href|escape}"
									title="{$slvn_item.list[ix].title|escape}">
									{if $slvn_item.list[ix].label == ''}-{else}{$slvn_item.list[ix].label|escape}{/if}
								</a>
							</li>
						{/section}
						{if $nonums != 'y'}</ol>{else}</ul>{/if}
					</div>
				{/if}
				{if $prefs.feature_jquery_ui eq "y" and $use_jquery_ui eq "y"}
					</div>
	           {assign var=fragment value=`$fragment+1`}
				{/if}
			{/if}
		{/foreach}
		{if $prefs.feature_jquery_ui eq "y" and $use_jquery_ui eq "y"}</div>{/if}
	{/if}
	{if $prefs.feature_jquery_ui eq "y" and $use_jquery_ui eq "y"}{jq} $(function() {$("#mytabs").tabs({});}); {/jq}{/if}
	{/tikimodule}
{/if}
