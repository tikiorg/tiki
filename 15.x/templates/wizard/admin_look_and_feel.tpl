{* $Id$ *}
<div class="media">
	<span class="pull-left fa-stack fa-lg margin-right-18em" alt="{tr}Configuration Wizard{/tr}" title="Configuration Wizard">
		<i class="fa fa-gear fa-stack-2x"></i>
		<i class="fa fa-rotate-270 fa-magic fa-stack-2x margin-left-9em"></i>
	</span>
	{tr}Configure the Tiki theme and other look & feel preferences{/tr}.</br></br></br>
	<div class="media-body">
		{icon name="admin_look" size=3 iclass="adminWizardIconright"}
		<fieldset>
			<legend>{tr}Look & Feel options{/tr}</legend>
				<div class="row">
					<div class="col-md-3 col-md-push-9">
{*
						<div class="thumbnail">
							{if $thumbfile}
								<img src="{$thumbfile}" alt="{tr}Theme Screenshot{/tr}" id="theme_thumb">
							{else}
								<span>{icon name="image"}</span>
							{/if}
						</div>
*}
					</div>
					<div class="col-md-9 col-md-pull-3 adminoptionbox">
						{preference name=theme}
						<div class="adminoptionbox theme_childcontainer custom_url">
							{preference name=theme_custom_url}
						</div>
						{preference name=theme_option}
						<div class="adminoptionbox theme_childcontainer legacy">
							{preference name=style}
							{preference name=style_option}
							{preference name=style_admin}
							{preference name=style_admin_option}
							{preference name=site_layout_admin}
						</div>
						{preference name=site_layout}
						{preference name=site_layout_per_object}
					</div>
				</div>
<!--
			<div style="position:relative;">
				<div class="adminoptionbox">
					{preference name=feature_fixed_width}
					<div class="adminoptionboxchild" id="feature_fixed_width_childcontainer">
						{preference name=layout_fixed_width}
					</div>
				</div>
			</div>
-->
			<br>
			<em>{tr}See also{/tr} <a href="tiki-admin.php?page=look&amp;alt=Look+%26+Feel" target="_blank">{tr}Look & Feel admin panel{/tr}</a></em>
			</br></br>
		</fieldset>

		<fieldset>
			<legend>{tr}Title{/tr}</legend>
			{preference name=sitetitle}
			{preference name=sitesubtitle}
		</fieldset>
		<fieldset>
			<legend>{tr}Logo{/tr}</legend>
			{preference name=sitelogo_src}
		</fieldset>
		<fieldset>
			<legend>{tr}Favicon{/tr}</legend>
			{preference name=site_favicon}
			{preference name=site_favicon_type}
		</fieldset>
	</div>
</div>
