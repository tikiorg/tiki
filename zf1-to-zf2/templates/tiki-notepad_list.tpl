{title help="notepad"}{tr}Notes{/tr}{/title}

	{include file='tiki-mytiki_bar.tpl'}

<div class="t_navbar">
	{button href="tiki-notepad_write.php" _class="btn btn-default" _text="{tr}Write a note{/tr}"}
</div>

<div style="text-align:center;">
	<div style="height:20px; width:200px; border:1px solid black; background-color:#666666; text-align:left; margin:0 auto;">
		<div style="background-color:red; height:100%; width:{$cellsize}px;"></div>
	</div>
	<small>{tr}quota{/tr}&nbsp;{$percentage}%</small>
</div>

{* Use css menus as fallback for item dropdown action menu if javascript is not being used *}
{if $prefs.javascript_enabled !== 'y'}
	{$js = 'n'}
	{$libeg = '<li>'}
	{$liend = '</li>'}
{else}
	{$js = 'y'}
	{$libeg = ''}
	{$liend = ''}
{/if}

{if count($channels) > 0 or $find ne ''}
	{include file='find.tpl'}
	<form action="tiki-notepad_list.php" method="post">
		<div class="{if $js === 'y'}table-responsive{/if}"> {*the table-responsive class cuts off dropdown menus *}
			<table class="table table-striped table-hover">
				<tr>
					<th style="text-align:center;">
						<input type="submit" class="btn btn-default btn-sm" name="delete" value="{tr}x{/tr} ">
					</th>
					<th>
						<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
					</th>
					<th>
						<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'parse_mode_desc'}parse_mode_asc{else}parse_mode_desc{/if}">{tr}Type{/tr}</a>
					</th>
					<th>
						<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'created_desc'}created_asc{else}created_desc{/if}">{tr}Created{/tr}</a>
					</th>
					<th>
						<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'lastModif_desc'}lastModif_asc{else}lastModif_desc{/if}">{tr}Last Modified{/tr}</a>
					</th>
					<th style="text-align:right;">
						<a href="tiki-notepad_list.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'size_desc'}size_asc{else}size_desc{/if}">{tr}Size{/tr}</a>
					</th>
					<th></th>
				</tr>

				{section name=user loop=$channels}
					<tr>
						<td class="id">
							<input type="checkbox" name="note[{$channels[user].noteId}]">
						</td>
						<td class="text">
							<a class="link" href="tiki-notepad_read.php?noteId={$channels[user].noteId}">{$channels[user].name|escape}</a>
						</td>
						<td class="text">{$channels[user].parse_mode}</td>
						<td class="date">{$channels[user].created|tiki_short_datetime}</td>
						<td class="date">{$channels[user].lastModif|tiki_short_datetime}</td>
						<td class="integer">{$channels[user].size|kbsize}</td>
						<td class="action">
							{capture name=notepad_actions}
								{strip}
									{$libeg}<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}">
										{icon name='view' _menu_text='y' _menu_icon='y' alt="{tr}View{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-notepad_get.php?noteId={$channels[user].noteId}&amp;save=1">
										{icon name='floppy' _menu_text='y' _menu_icon='y' alt="{tr}Save{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-notepad_write.php?noteId={$channels[user].noteId}">
										{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
									</a>{$liend}
									{$libeg}<a href="tiki-notepad_read.php?noteId={$channels[user].noteId}&amp;remove=1">
										{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
									</a>{$liend}
								{/strip}
							{/capture}
							{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
							<a
								class="tips"
								title="{tr}Actions{/tr}"
								href="#"
								{if $js === 'y'}{popup delay="0|2000" fullhtml="1" center=true text=$smarty.capture.notepad_actions|escape:"javascript"|escape:"html"}{/if}
								style="padding:0; margin:0; border:0"
							>
								{icon name='wrench'}
							</a>
							{if $js === 'n'}
								<ul class="dropdown-menu" role="menu">{$smarty.capture.notepad_actions}</ul></li></ul>
							{/if}
						</td>
					</tr>
				{sectionelse}
					<tr>
						<td colspan="4">{tr}No notes yet{/tr}</td>
					</tr>
				{/section}
				<tr>
					<td colspan="4">
						<input type="submit" class="btn btn-default btn-sm" name="merge" value="{tr}Merge selected notes into{/tr}">
						<input type="text" name="merge_name" size="20">
					</td>
				</tr>
			</table>
		</div>
	</form>

	{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}
{/if}

<h2>{tr}Upload file{/tr}</h2>
<form enctype="multipart/form-data" action="tiki-notepad_list.php" method="post" class="form-horizontal">
	<div class="form-group">
		<label class="col-sm-3 control-label">{tr}Upload file:{/tr}</label>
		<div class="col-sm-7">
	      	<input type="hidden" name="MAX_FILE_SIZE" value="10000000000000">
			<input size="16" name="userfile1" type="file">
	    </div>
    </div>
    <div class="form-group">
		<label class="col-sm-3 control-label"></label>
		<div class="col-sm-7">
			<input type="submit" class="btn btn-primary btn-sm" name="upload" value="{tr}Upload{/tr}">
	    </div>
    </div>
</form>
