{if $save eq 'y'}
	<h2>{tr}Tiki site registered{/tr}</h2>
	{tr}The following site was added and validation by admin may be needed before appearing on the lists{/tr}
	<div class="table-responsive">
		<table class="table">
			<tr>
				<td>{tr}Name:{/tr}</td>
				<td>{$info.name}</td>
			</tr>
			<tr>
				<td>{tr}Description:{/tr}</td>
				<td>{$info.description}</td>
			</tr>
			<tr>
				<td>{tr}URL:{/tr}</td>
				<td>{$info.url}</td>
			</tr>
			<tr>
				<td>{tr}Country:{/tr}</td>
				<td>{$info.country}</td>
			</tr>
		</table>
	</div>
{else}
	<div class="panel panel-default">
		{if $tiki_p_admin ne 'y'}
			<div class="panel-heading">
				{tr}Error{/tr}
			</div>
			<div class="panel-body">
				{tr}You don't have permission to use this feature.{/tr}
				{tr}Please register.{/tr}
			</div>
		{else}
			<div class="panel-heading">
				{tr}Register this site at Tiki.org{/tr}
			</div>
			<div class="panel-body">
				<table>
					<tr>
						<td>
							<div class="panel panel-default">
								<div class="panel-body">
									<b>{tr}Read this first!{/tr}</b>
									<br><br>
									{tr}On this page you can make your tiki site known to Tiki.org. It will get shown there in a list of known tiki sites.{/tr}
									<ul>
										<li>{tr}Registering is voluntary.{/tr}</li>
										<li>{tr}Registering does not give you any benefits except one more link to your site.{/tr}</li>
										<li>{tr}You don't get any emails, we don't sell the data about your site.{/tr}</li>
										<li>{tr}Registering is just for us to get an overview of Tiki's usage.{/tr}</li>
									</ul>
									<b>{tr}If your site is private or inside your intranet, you should not register!{/tr}</b>
									<br><br>
								</div>
							</div>
						</td>
						<td align="center" width="30%">
							<br><br><br>
							<a href="http://tiki.org/" target="_tikiwiki"><img src="img/tiki/Tiki_WCG.png"></a><br>
							<br>
							{tr}tiki.org{/tr}
						</td>
					</tr>
				</table>

				<br><br>
				<b>{tr}Information about your site:{/tr}</b>
				<br><br>
				<form action="http://tiki.org/tiki-directory_add_tiki_site.php" method="post" class="form-horizontal">
					<input type="hidden" name="registertiki" value="true">
					<div class="form-group">
						<label class="control-label col-sm-3">{tr}Name:{/tr}</label>
						<div class="col-sm-7">
							<input type="text" name="name" class="form-control" size="60" value="{$info.name|escape}">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{tr}Description:{/tr}</label>
						<div class="col-sm-7">
							<textarea rows="5" cols="60" name="description" class="form-control">{$info.description|escape}</textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{tr}URL:{/tr}</label>
						<div class="col-sm-7 form-control-static">
							<input type="hidden" name="url" value="{$info.url|escape}" class="form-control">{$info.url|escape}
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-sm-3">{tr}Country:{/tr}</label>
						<div class="col-sm-7">
							<select name="country" class="form-control">
								{section name=ux loop=$countries}
									<option value="{$countries[ux]|escape}" {if $info.country eq $countries[ux]}selected="selected"{/if}>{$countries[ux]}</option>
								{/section}
							</select>
						</div>
					</div>
					<input name="isValid" type="hidden" value="">
					<div class="form-group">
						<label class="control-label col-sm-3"></label>
						<div class="col-sm-7">
							<input type="submit" class="btn btn-primary btn-sm" name="save" value="{tr}Save{/tr}">
						</div>
					</div>
				</form>
			</div>
		{/if}
	</div>
{/if}
