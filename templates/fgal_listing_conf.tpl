{* $Id$ *}

{if is_array($fgal_options) and count($fgal_options) gt 0}
	{foreach key=key item=item from=$fgal_options}
		{if $key eq 'default_view'}
			<div class="form-group">
				<label class="col-sm-4 control-label" for="fgal_{$key}">
					{$item.name}
				</label>
				<div class="col-sm-8">
					<select id="fgal_{$key}" name="fgal_{$key}" class="form-control">
						<option value="list"{if $item.value eq 'list'} selected="selected"{/if}>
							{tr}List{/tr}
						</option>
						<option value="browse"{if $item.value eq 'browse'} selected="selected"{/if}>
							{tr}Browse{/tr}
						</option>
						<option value="page"{if $item.value eq 'page'} selected="selected"{/if}>
							{tr}Page{/tr}
						</option>
						{if $prefs.fgal_elfinder_feature eq 'y'}
							<option value="finder"{if $item.value eq 'finder'} selected="selected"{/if}>
								{tr}Finder View{/tr}
							</option>
						{/if}
					</select>
				</div>
			</div>
		{elseif $key eq 'icon_fileId'}
			<div class="form-group">
				<label class="col-sm-4 control-label" for="fgal_{$key}">
					{$item.name}
				</label>
				<div class="col-sm-8">
					<input
						type="text"
						id="fgal_{$key}"
						name="fgal_{$key}"
						value="{$item.value}"
						class="form-control"
						placeholder="{tr}File{/tr}..."
					>
					<span class="help-block">
						{tr}Enter the ID of any file in any gallery to be used as the icon for this gallery in browse view{/tr}
					</span>
				</div>
			</div>
		{else}
			<div class="checkbox col-sm-8 col-sm-offset-4">
				<label for="fgal_{$key}{if isset($fgal_ext)}{$fgal_ext}{/if}">
					{assign var='pref_name' value="fgal_$key"}
					<input
						type="checkbox"
						id="fgal_{$key}"
						name="fgal_{$key}"
						{if $item.value eq 'y'}
							checked="checked"
						{/if}
						{if isset($edit_mode) and $edit_mode eq 'y' and $prefs.$pref_name neq 'y'}
						 disabled="disabled"
						{/if}
					>
					{$item.name}
					{if isset($edit_mode) and $edit_mode eq 'y' and $prefs.$pref_name neq 'y'}
						<span class="help-block">
							{tr}The checkbox is disabled because this preference is disabled globally.{/tr}
						</span>
						{if $tiki_p_admin eq 'y' or $tiki_p_admin_file_galleries eq 'y'}
							<span class="help-block">
								<a href="tiki-admin.php?page=fgal">
									{tr}Please enable the preference globally first.{/tr}
								</a>
							</span>
						{else}
							<span class="help-block">
								{tr}Site administrators can enable the preference.{/tr}
							</span>
						{/if}
					{/if}
				</label>
			</div>
		{/if}
	{/foreach}
{/if}

{if is_array($fgal_listing_conf) and count($fgal_listing_conf) gt 0}
	{foreach key=key item=item from=$fgal_listing_conf}
		<div class="form-group">
			<label class="col-sm-4 control-label margin-bottom-md" for="fgal_list_{$key}">
				{$item.name}
			</label>
			<div class="col-sm-8 margin-bottom-md">
				<select id="fgal_list_{$key}" name="fgal_list_{$key}" class="form-control">
					{if $key eq 'name' or $key eq 'name_admin'}
						<option value="a"{if isset($item.value) and $item.value eq 'a'} selected="selected"{/if}>
							{tr}Name-filename{/tr}
						</option>
						<option value="n"{if isset($item.value) and $item.value eq 'n'} selected="selected"{/if}>
							{tr}Name{/tr}
						</option>
						<option value="f"{if isset($item.value) and $item.value eq 'f'} selected="selected"{/if}>
							{tr}Filename only{/tr}
						</option>
					{elseif $key neq 'deleteAfter'}
						<option value='n'{if isset($item.value) and $item.value eq 'n'} selected="selected"{/if}>
							{tr}Hide{/tr}
						</option>
						<option value='y'{if isset($item.value) and $item.value eq 'y'} selected="selected"{/if}>
							{tr}Show as a column{/tr}
						</option>
						<option value='o'{if isset($item.value) and $item.value eq 'o'} selected="selected"{/if}>
							{tr}Show in popup box{/tr}
						</option>
						<option value='a'{if isset($item.value) and $item.value eq 'a'} selected="selected"{/if}>
							{tr}Both{/tr}
						</option>
					{/if}
					{if $key eq 'lockedby' or $key eq 'lockedby_admin'}
						<option value='i'{if isset($item.value) and $item.value eq 'i'} selected="selected"{/if}>
							{tr}Show an icon in a column{/tr}
						</option>
					{/if}
					{if $key eq 'deleteAfter'}
						<option value='n'{if isset($item.value) and $item.value eq 'n'} selected="selected"{/if}>
							{tr}Hide{/tr}
						</option>
						<option value='y'{if isset($item.value) and $item.value eq 'y'} selected="selected"{/if}>
							{tr}Show as a column{/tr}
						</option>
					{/if}
				</select>
			</div>
		</div>
	{/foreach}
{/if}
