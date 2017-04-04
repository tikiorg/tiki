{* $Id$ *}
{title help="Webservices" admpage="webservices"}{tr}Webservices{/tr}{/title}

<form action="tiki-admin_webservices.php" method="post">
	<div class="t_navbar margin-bottom-md">
		{foreach from=$webservices item=name}
			{button href="tiki-admin_webservices.php?name=$name" class="btn btn-default" _text=$name}
		{/foreach}
		{if $storedName}
			{button href="tiki-admin_webservices.php" class="btn btn-default" _text="{tr}Create New{/tr}"}
		{/if}
	</div>

	{if $storedName and not $edit}
		<h2>{$storedName|escape}:</h2>
		<div class="row">
			<div class="form-group clearfix">
				<label class="col-sm-4"> {tr}URL:{/tr}</label>
				<div class="col-sm-8">
					<code>{$url|escape}</code>
				</div>
			</div>
			{if $postbody}
				<div class="form-group clearfix">
					<label class="col-sm-4"> {tr}Body of POST request:{/tr}</label>
					<div class="col-sm-8">
						<pre style="max-height: 40em; overflow: auto; white-space: pre-wrap">{$postbody|escape}</pre>
					</div>
				</div>
			{/if}
			<div class="col-sm-8 col-sm-offset-4 clearfix">
				<input type="hidden" name="name" value="{$storedName|escape}">
				{button _icon_name='edit' _text="{tr}Edit{/tr}" _script="tiki-admin_webservices.php?name={$storedName|escape}&edit" _class='btn btn-primary btn-sm'}
				{button _icon_name='delete' _text="{tr}Delete{/tr}" _script="tiki-admin_webservices.php?name={$storedName|escape}&delete" _class='btn btn-danger btn-sm'}
			</div>
		</div>
	{else}
		{remarksbox type="tip" title="{tr}Tip{/tr}"}
		{tr}Enter the URL of a web services returning either JSON or YAML. Parameters can be specified by enclosing a name between percentage signs. For example: %name%. %service% and %template% are reserved keywords and cannot be used.{/tr}
		{/remarksbox}
		<p>{tr}URL:{/tr}<input type="text" name="url" size="75" value="{$url|escape}" class="form-control"/></p>
		<p>
			{tr}Type:{/tr}
			<select name="wstype">
				{foreach from=$webservicesTypes item=_type}
					<option value="{$_type}"{if $wstype eq $_type} selected="selected"{/if}>{$_type}</option>
				{/foreach}
			</select>
		</p>
		<div id="ws_postbody" class="row">
			<label class="col-sm-4"> {tr}Body of POST request{/tr}</label>
			<div class="col-sm-8">
				<textarea name="postbody" class="form-control">{$postbody|escape}</textarea><br>
				{tr}Parameters (%name%):{/tr}
			</div>
			<div class="col-sm-8 col-sm-offset-4">
				<p id="ws_operation" style="display: none;">{tr}Operation:{/tr}
					<input type="text" name="operation" size="30" value="{$operation|escape}" class="form-control"/>
				</p>
				<p><input type="submit" class="btn btn-default btn-sm" name="parse" value="{tr}Lookup{/tr}"/></p>
			</div>
		</div>
		{if $edit}
			<input type="hidden" name="edit" value="1">
			<input type="hidden" name="name" value="{$storedName|escape}">
		{/if}
	{/if}
	{if $url}
		<div class="row">
			<h3>{tr}Parameters{/tr}</h3>
			{if $params|@count}
				{foreach from=$params key=name item=value}
					<div class="form-group">
						<label class="col-sm-4 control-label" for="params[{$name|escape}]">{$name|escape}</label>
						<div class="col-sm-8">
							<input type="text" name="params[{$name|escape}]" id="params[{$name|escape}]" value="{$value|escape}" class="form-control">
						</div>
					</div>
				{/foreach}
			{else}
				<div class="col-sm-8 col-sm-offset-4">{tr _0=$storedName|escape}%0 requires no parameter.{/tr}</div>
			{/if}
			<div class="col-sm-8 col-sm-offset-4">
				<div class="form-group">
					<input type="submit" class="btn btn-default btn-sm col-sm-2" name="test" value="{tr}Test Input{/tr}">
					<label class="col-sm-10"> <input type="checkbox" checked="checked" name="nocache">
						{tr}Bypass cache{/tr}
					</label>
				</div>
			</div>
		</div>
	{/if}
	{if $data}
		<div class="row">
			<div class="col-sm-12">
				<h3>{tr}Response Information{/tr}</h3>
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th>{tr}OIntegrate Version{/tr}</th>
							<td>{if $response->version}{$response->version|escape}{else}<em>{tr}Not supported{/tr}</em>{/if}
						</tr>
						<tr>
							<th>{tr}Schema Version{/tr}</th>
							<td>{if $response->schemaVersion}{$response->schemaVersion|escape}{else}
									<em>{tr}Not supported{/tr}</em>{/if}
						</tr>
						<tr>
							<th>{tr}Schema Documentation{/tr}</th>
							<td>{if $response->schemaDocumentation}
									<a href="{$response->schemaDocumentation|escape}">{tr}Available{/tr}</a>{else}
									<em>{tr}Not supported{/tr}</em>{/if}
						</tr>
						<tr>
							<th>{tr}Cache{/tr}</th>
							<td>{if $response->cacheControl}{$response->cacheControl->getFieldValue()|escape}{else}
									<em>{tr}Not specified, default used{/tr}</em>{/if}
						</tr>
						<tr>
							<th>{tr}Content Type{/tr}</th>
							<td>{if $response->contentType}{$response->contentType->getMediaType()|escape} ({$response->contentType->getCharset()|escape}){else}
									<strong>{tr}Not specified{/tr}</strong>{/if}
						</tr>
						<tr>
							<th colspan="2">{tr}Returned Data{/tr}</th>
						</tr>
						<tr>
							<td colspan="2">
								<pre style="max-height: 40em; overflow: auto; white-space: pre-wrap">{$data|truncate:100000:"\n[{tr}tuncated at approx. 1MB{/tr}]"|escape}</pre>
							</td>
						</tr>
						<tr>
							<th colspan="2">{tr}Proposed Templates{/tr}</th>
						</tr>
						{foreach from=$templates item=template key=number}
							<tr>
								<th>
									{$template.engine|escape}/{$template.output|escape}
									<input type="submit" class="btn btn-default btn-sm" name="add[{$number}]" value="{tr}Add{/tr}"/>
								</th>
								<td>
									<pre>{$template.content|escape}</pre>
								</td>
							</tr>
							{foreachelse}
							<tr>
								<th>{tr}None{/tr}</th>
							</tr>
						{/foreach}
					</table>
				</div>
				{if empty($storedName) or $edit}
					<p>{tr}Register this web service. It will be possible to register the templates afterwards. Service name must only contain letters.{/tr}</p>
					<p>
						{if $edit}
							<input type="hidden" name="old_name" class="form-control" value="{$storedName}">
							<input type="text" name="new_name" class="form-control" value="{$storedName}">
						{else}
							<input type="text" name="new_name" class="form-control">
						{/if}
						<input type="submit" class="btn btn-default btn-sm" name="register" value="{tr}Register Service{/tr}"/>
					</p>
				{else}
					<h3>{tr}Registered Templates{/tr}</h3>
					<div class="table-responsive">
						<table>
							<tr>
								<th style="width: 25%">{tr}Name{/tr}</th>
								<th style="width: 25%">{tr}Engine{/tr}</th>
								<th style="width: 25%">{tr}Output{/tr}</th>
								<th style="width: 25%">{tr}Preview{/tr}</th>
							</tr>
							{foreach from=$storedTemplates item=template}
								<tr>
									<td>
										<input type="submit" class="btn btn-default btn-sm" name="loadtemplate" value="{$template->name|escape}" title="{tr}Edit{/tr}">
										{icon name='delete' title='{tr}Delete{/tr}' href='tiki-admin_webservices.php?name='|cat:($storedName|escape)|cat:'&deletetemplate='|cat:($template->name|escape)}
									</td>
									<td>{$template->engine|escape}</td>
									<td>{$template->output|escape}</td>
									<td>
										<input type="submit" class="btn btn-default btn-sm" name="preview" value="{$template->name|escape}"/>
									</td>
									<td>
										<a class="btn btn-link" role="button" data-toggle="collapse" href="#template_{$template->name|escape}"
												aria-expanded="false" aria-controls="template_{$template->name|escape}" title="{tr}Toggle template source{/tr}">
											{icon name='caret-down'}
										</a>
									</td>
								</tr>
								<tr>
									<td colspan="5">
										<pre style="max-height: 30em; overflow: auto; white-space: pre-wrap" id="template_{$template->name|escape}" class="collapse">
											{$template->content|escape}</pre>
									</td>
								</tr>
								{if $preview eq $template->name}
									<tr>
										<td colspan="5">{$preview_output}</td>
									</tr>
								{/if}
							{/foreach}
							<tr>
								<td colspan="5">
									<hr>
								</td>
							</tr>
							<tr>
								<td style="padding: 0 .5em">
									<input type="text" name="nt_name" value="{$nt_name|escape}" class="form-control"/></td>
								<td style="padding: 0 .5em">
									<select id="nt_engine" name="nt_engine" class="form-control">
										<option value=""></option>
										<option value="javascript" {if $nt_engine eq 'javascript'} selected="selected"{/if}>
											JavaScript
										</option>
										<option value="smarty"{if $nt_engine eq 'smarty'} selected="selected"{/if}>Smarty
										</option>
										<option value="index"{if $nt_engine eq 'index'} selected="selected"{/if}>Index</option>
									</select>
								</td>
								<td style="padding: 0 .5em">
									<select id="nt_output" name="nt_output" class="form-control">
										<option value=""></option>
										<option value="html" {if $nt_output eq 'html'} selected="selected"{/if}>HTML</option>
										<option value="tikiwiki"{if $nt_output eq 'tikiwiki'} selected="selected"{/if}>Wiki
										</option>
										<option value="index"{if $nt_output eq 'index'} selected="selected"{/if}>Index</option>
										<option value="mindex"{if $nt_output eq 'mindex'} selected="selected"{/if}>Multi-Index
										</option>
									</select>
								</td>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td colspan="4">
									<textarea name="nt_content" rows="10" class="form-control">{$nt_content|escape}</textarea>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<input type="submit" class="btn btn-default btn-sm" name="create_template" value="{tr}Register Template{/tr}"/>
								</td>
							</tr>
						</table>
					</div>
				{/if}
			</div>
		</div>
	{/if}
</form>