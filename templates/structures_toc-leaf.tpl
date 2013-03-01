{strip}
	<li class="{if $toc_type eq 'fancy'}fancy{elseif $toc_type eq 'admin'}ui-state-default admin{/if}toclevel"{if $toc_type eq 'admin'} id="node_{$structure_tree.page_ref_id}"{/if}>
		<div>
			{if $toc_type eq 'admin'}
				<div class="actions">
					<input type="text" class="page-alias-input" value="{$structure_tree.page_alias|escape}" placeholder="{tr}Page alias...{/tr}">
					{self_link _script='tiki-index.php' page=$structure_tree.pageName structure=$structure_name _title="{tr}View{/tr}" _noauto="y"}
						{icon _id='magnifier' alt="{tr}View{/tr}"}
					{/self_link}
					{if $tiki_p_watch_structure eq 'y'}
						{if !$structure_tree.event}
							{self_link page_ref_id=$structure_tree.page_ref_id watch_object=$structure_tree.page_ref_id watch_action=add page=$structure_tree.pageName}
								{icon _id='eye_arrow_down' alt="{tr}Monitor the Sub-Structure{/tr}"}
							{/self_link}
						{else}
							{self_link page_ref_id=$structure_tree.page_ref_id watch_object=$structure_tree.page_ref_id watch_action=remove}
								{icon _id='no_eye_arrow_down' alt="{tr}Stop Monitoring the Sub-Structure{/tr}"}
							{/self_link}
						{/if}
					{/if}
					{if $structure_tree.editable}
						{if $structure_tree.flag == 'L'}
							{capture assign=title}{tr _0=$structure_tree.user}locked by %0{/tr}{/capture}
							{icon _id='lock' alt="{tr}Locked{/tr}" title=$title}
						{else}
							{self_link _script='tiki-editpage.php' page=$structure_tree.pageName}
								{icon _id='page_edit' alt='{tr}Edit page{/tr}'}
							{/self_link}
						{/if}
						{self_link _onclick="addNewPage(this);return false;"}
							{icon _id='add' alt='{tr}Add new child page{/tr}'}
						{/self_link}
						{self_link _onclick="movePageToStructure(this);return false;"}
							{icon _id='arrow_right' alt='{tr}Move{/tr}'}
						{/self_link}
						{self_link page_ref_id=$structure_tree.page_ref_id remove=$structure_tree.page_ref_id}
							{icon _id='cross' alt='{tr}Delete{/tr}' page_ref_id=$structure_tree.page_ref_id remove=$structure_tree.page_ref_id}
						{/self_link}
					{/if}
				</div>
			{/if}
			{if $numbering}
				<span class="prefix">{$structure_tree.prefix}&nbsp;</span>
			{/if}
			{if $showdesc and $structure_tree.description and $toc_type neq 'admin'}
				<span class="description">{$structure_tree.description|escape} :&nbsp;</span>
			{/if}
			<a href={if $toc_type eq 'admin'}
						"{$smarty.server.PHP_SELF}?page_ref_id={$structure_tree.page_ref_id}"
					{else}
						"{sefurl page=$structure_tree.pageName structure=$structurePageName page_ref_id=$structure_tree.page_ref_id}"
					{/if}
					class="link" title="
				{if $showdesc}
					{if $structure_tree.page_alias}
						{$structure_tree.page_alias|escape}
					{else}
						{$structure_tree.pageName|escape}
					{/if}
				{else}
					{$structure_tree.description|escape}
				{/if}">
				{if $hilite}<strong>{/if}
				{if $structure_tree.page_alias and $toc_type neq 'admin'}
					{$structure_tree.page_alias|escape}
				{else}
					{$structure_tree.short_pageName|escape}
				{/if}
				{if $hilite}</strong>{/if}
			</a>
			{if (!$showdesc and $toc_type eq 'fancy') or ($showdesc and $toc_type eq 'admin' and !empty($structure_tree.description))} : <span class="description">{$structure_tree.description|escape}</span>{/if}

		</div>
	{* no </li> here *}
{/strip}