{title help="Admin DSN"}{tr}Admin Content Sources{/tr}{/title}

{remarksbox type="tip" title="{tr}Tip{/tr}"}
	{tr}Use Admin DSN to define the database to be used by the SQL plugin.{/tr}
{/remarksbox}

<h2>{tr}Create/edit DSN{/tr}</h2>
<form action="tiki-admin_dsn.php" method="post" class="form-horizontal" role="form">
	<input type="hidden" name="dsnId" value="{$dsnId|escape}">
	<div class="form-group">
		<label class="col-sm-3 control-label" for="name">{tr}Name{/tr}</label>
		<div class="col-sm-9">
			<input type="text" maxlength="255" name="name" id="name" class="form-control" value="{$info.name|escape}">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label" for="dsn">{tr}DSN{/tr}</label>
		<div class="col-sm-9">
			<input type="text" maxlength="255" class="form-control" name="dsn" id="dsn" value="{$info.dsn|escape}">
		</div>
	</div>
	<div class="form-group text-center">
		<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
	</div>
</form>
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
<h2>{tr}DSN{/tr}</h2>
<div class="{if $js === 'y'}table-responsive{/if}"> {* table-responsive class cuts off css drop-down menus *}
	<table class="table table-striped table-hover">
		<tr>
			<th>
				<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}Name{/tr}</a>
			</th>
			<th>
				<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'dsn_desc'}dsn_asc{else}dsn_desc{/if}">{tr}DSN{/tr}</a>
			</th>
			<th></th>
		</tr>

		<tr>
			<td class="text">{tr}Local (Tiki database){/tr}</td>
			<td class="text">{tr}See db/local.php{/tr}</td>
			<td class="action">
				&nbsp;&nbsp;
				{permission_link mode=icon type=dsn id=local title=local}
			</td>
		</tr>
		{section name=user loop=$channels}
			<tr>
				<td class="text">{$channels[user].name}</td>
				<td class="text">{$channels[user].dsn}</td>
				<td class="action">
					{capture name=dsn_actions}
						{strip}
							{$libeg}<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;dsnId={$channels[user].dsnId}">
								{icon name='edit' _menu_text='y' _menu_icon='y' alt="{tr}Edit{/tr}"}
							</a>{$liend}
							{$libeg}{permission_link mode=text type=dsn id=$channels[user].name title=$channels[user].name}{$liend}
							{$libeg}<a href="tiki-admin_dsn.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].dsnId}">
								{icon name='remove' _menu_text='y' _menu_icon='y' alt="{tr}Remove{/tr}"}
							</a>{$liend}
						{/strip}
					{/capture}
					{if $js === 'n'}<ul class="cssmenu_horiz"><li>{/if}
					<a
						class="tips"
						title="{tr}Actions{/tr}"
						href="#"
						{if $js === 'y'}{popup fullhtml="1" center=true text=$smarty.capture.dsn_actions|escape:"javascript"|escape:"html"}{/if}
						style="padding:0; margin:0; border:0"
					>
						{icon name='wrench'}
					</a>
					{if $js === 'n'}
						<ul class="dropdown-menu" role="menu">{$smarty.capture.dsn_actions}</ul></li></ul>
					{/if}
				</td>
			</tr>
		{/section}
	</table>
</div>

{pagination_links cant=$cant_pages step=$prefs.maxRecords offset=$offset}{/pagination_links}

<h2>{tr}Content Authentication{/tr}</h2>
<form id="source-form" method="post" action="{service controller=auth_source}" class="form-horizontal" role="form">
	<fieldset>
		<legend>{tr}Identification{/tr}</legend>
		<div class="form-group">
			<label class="col-sm-3 control-label">{tr}Identifier{/tr}</label>
			<div class="col-sm-3">
				<select name="existing" class="form-control">
					<option value="">{tr}New{/tr}</option>
				</select>
			</div>
			<div class="col-sm-4">
				<input type="text" name="identifier" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="url">{tr}URL{/tr}</label>
			<div class="col-sm-4">
				<input type="url" name="url" id="url" class="form-control" />
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="method">{tr}Type{/tr}</label>
			<div class="col-sm-4">
				<select name="method" id="method">
					<option value="basic">{tr}HTTP Basic{/tr}</option>
					<option value="post">{tr}HTTP Session / Login{/tr}</option>
					<option value="get">{tr}HTTP Session / Visit{/tr}</option>
				</select>
			</div>
		</div>
	</fieldset>
	<fieldset class="method basic">
		<legend>{tr}HTTP Basic{/tr}</legend>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="basic_username">{tr}Username{/tr}</label>
			<div class="col-sm-9">
				<input type="text" name="basic_username" id="basic_username" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label" for="basic_password">{tr}Password{/tr}</label>
			<div class="col-sm-9">
				<input type="password" name="basic_password" id="basic_password" class="form-control">
			</div>
		</div>
	</fieldset>
	<fieldset class="method post">
		<legend>{tr}HTTP Session / Login{/tr}</legend>
		<label>{tr}URL{/tr} <input type="url" name="post_url"></label>
		<table>
			<thead>
				<tr><th>{tr}Name{/tr}</th><th>{tr}Value{/tr}</th></tr>
			</thead>
			<tfoot>
				<tr>
					<td><input type="text" name="post_new_field"></td>
					<td><input type="text" name="post_new_value"></td>
					<td><input type="submit" class="btn btn-default btn-sm" name="post_new_add" value="{tr}Add{/tr}"></td>
				</tr>
			</tfoot>
			<tbody>
			</tbody>
		</table>
	</fieldset>
	<fieldset class="method get">
		<legend>{tr}HTTP Session / Visit{/tr}</legend>
		<label>{tr}URL{/tr} <input type="url" name="get_url"></label>
	</fieldset>
	<fieldset>
		<div class="form-group text-center">
			<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
			<input type="submit" class="btn btn-default btn-sm" name="delete" value="{tr}Delete{/tr}">
		</div>
	</fieldset>
</form>
{jq}
$('#source-form').each(function () {
	var form = this,
		reload = function () {
			$('option.added', form).remove();
			$.getJSON($.service('auth_source', 'list'), function (entries) {
				$.each(entries, function (k, v) {
					$(form.existing).append($('<option class="added"/>').text(v));
				});

				$(form.existing).trigger('chosen:updated');
			});
		},
		addPostRow = function (name, value) {
			var row = $('<tr/>');
			row.append($('<td/>').text(name));
			row.append($('<td/>').text(value));
			row.append($('<td>{{icon name=remove}}</td>').css('cursor', 'pointer').click(function () {
				$(this).closest('tr').remove();
				return false;
			}));
			$('fieldset.method.post tbody', form).append(row);
		};

	$(form).submit(function () {
		return false;
	});

	$(form.existing).change(function () {
		var val = $(this).val();

		if (val.length) {
			$(form.identifier).hide().val(val);

			$.getJSON($.service('auth_source', 'fetch'), {
				identifier: $(form.existing).val()
			}, function (data) {
				$(form.method).val(data.method).change();
				$(form.url).val(data.url);

				switch (data.method) {
				case 'basic':
					$(form.basic_username).val(data.arguments.username);
					$(form.basic_password).val(data.arguments.password);
					break;
				case 'get':
					$(form.get_url).val(data.arguments.url);
					break;
				case 'post':
					$(form.post_url).val(data.arguments.post_url);
					$.each(data.arguments, function (key, value) {
						if (key !== 'post_url') {
							addPostRow(key, value);
						}
					});
					break;
				}
			});
		} else {
			$(form.identifier).show().val('').focus();
			$('input:not(:submit)', form).val('');
			$('fieldset.method.post tbody').empty();
		}
	});

	$(form.method).change(function () {
		$('fieldset.method', form).hide();
		$('fieldset.method.' + $(this).val(), form).show();
	}).change();

	reload();

	$(form.save).click(function () {
		var data = {
			action: 'save',
			identifier: $(form.identifier).val(),
			url: $(form.url).val(),
			method: $(form.method).val()
		}, isNew = $(form.existing).val() === '';

		switch (data.method) {
		case 'basic':
			data['arguments~username'] = $(form.basic_username).val();
			data['arguments~password'] = $(form.basic_password).val();
			break;
		case 'get':
			data['arguments~url'] = $(form.get_url).val();
			break;
		case 'post':
			data['arguments~post_url'] = $(form.post_url).val();

			$('fieldset.method.post tbody tr').each(function () {
				var name = this.childNodes[0], value = this.childNodes[1];
				data['arguments~' + $(name).text()] = $(value).text();
			});
		}

		$(form.existing).val('').change();

		$.post($(form).attr('action'), data, function () {
			if (isNew) {
				$(form.existing).append($('<option/>').text(data.identifier));
			}

			$(form.existing).val(data.identifier).change();
			$(form.existing).trigger('chosen:updated');
		});
		return false;
	});

	$(form.delete).click(function () {
		$.post($(form).attr('action'), {
			action: 'delete',
			identifier: $(form.existing).val()
		}, function () {
			$(form.existing).val('').change();
			reload();
		});
		return false;
	});

	$(form.post_new_add).click(function () {
		addPostRow($(form.post_new_field).val(), $(form.post_new_value).val());

		$(form.post_new_field).val('').focus();
		$(form.post_new_value).val('');
		return false;
	});
});
{/jq}
