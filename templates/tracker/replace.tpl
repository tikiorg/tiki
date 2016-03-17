{extends 'layout_view.tpl'}

{block name="title"}
	{title}{$title|escape}{/title}
{/block}

{block name="content"}

<form method="post" action="{service controller=tracker action=replace}">
	{accordion}
		{accordion_group title="{tr}General{/tr}"}
			<div class="form-group">
				<label for="name">{tr}Name{/tr}</label>
				<input class="form-control" type="text" name="name" value="{$info.name|escape}" required="required">
			</div>
			<div class="form-group">
				<label for="description">{tr}Description{/tr}</label>
				<textarea class="form-control" name="description" rows="4" cols="40">{$info.description|escape}</textarea>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="descriptionIsParsed" {if $info.descriptionIsParsed eq 'y'}checked="checked"{/if} value="1">
					{tr}Description is wiki-parsed{/tr}
				</label>
			</div>
		{/accordion_group}
		{accordion_group title="{tr}Features{/tr}"}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="useRatings" value="1"
						{if $info.useRatings eq 'y'} checked="checked"{/if}>
					{tr}Allow ratings (deprecated, use rating field){/tr}
				</label>
			</div>
			<div class="form-group depends" data-on="useRatings">
				<label for="ratingOptions">{tr}Rating options{/tr}</label>
				<input class="form-controls" type="text" name="ratingOptions" value="{$info.ratingOptions|default:'-2,-1,0,1,2'|escape}">
			</div>
			<div class="checkbox depends" data-on="useRatings">
				<label>
					<input type="checkbox" name="showRatings" value="1"
						{if $info.showRatings eq 'y'} checked="checked"{/if}>
					{tr}Show ratings in listing{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="useComments" value="1"
						{if $info.useComments eq 'y'} checked="checked"{/if}>
					{tr}Allow comments{/tr}
				</label>
			</div>
			<div class="checkbox depends" data-on="useComments">
				<label>
					<input type="checkbox" name="showComments" value="1"
						{if $info.showComments eq 'y'} checked="checked"{/if}>
					{tr}Show comments in listing{/tr}
				</label>
			</div>
			<div class="checkbox depends" data-on="useComments">
				<label>
					<input type="checkbox" name="showLastComment" value="1"
						{if $info.showLastComment eq 'y'} checked="checked"{/if}>
					{tr}Display last comment author and date{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="useAttachments" value="1"
						{if $info.useAttachments eq 'y'} checked="checked"{/if}>
					{tr}Allow attachments (deprecated, use files field){/tr}
				</label>
			</div>
			<div class="checkbox depends" data-on="useAttachments">
				<label>
					<input type="checkbox" name="showAttachments" value="1"
						{if $info.showAttachments eq 'y'} checked="checked"{/if}>
					{tr}Display attachments in listing{/tr}
				</label>
			</div>
			<fieldset class="depends sortable" data-on="useAttachments" data-selector="div.checkbox">
				<legend>{tr}Attachment attributes (sortable){/tr}</legend>
				{foreach from=$attachmentAttributes key=name item=att}
					<div class="checkbox">
						<label>
							<input type="checkbox" name="orderAttachments[]" value="{$name|escape}" {if $att.selected} checked="checked"{/if}>
							{$att.label|escape}
						</label>
					</div>
				{/foreach}
			</fieldset>
		{/accordion_group}
		{accordion_group title="{tr}Display{/tr}"}
			<div class="form-group">
				<label class="control-label" for="logo">{tr}Logo{/tr}</label>
				<input class="form-control" type="text" name="logo" value="{$info.logo|escape}">
				<div class="help-block">
					{tr}Recommended size: 64x64px.{/tr}
				</div>
			</div>
			<div class="form-group">
				<label for="sectionFormat">{tr}Section format{/tr}</label>
				<select name="sectionFormat" class="form-control">
					{foreach $sectionFormats as $format => $label}
						<option value="{$format|escape}"{if $info.sectionFormat eq $format} selected="selected"{/if}>{$label|escape}</option>
					{/foreach}
				</select>
				<div class="help-block">
					<p>{tr}Determines how headers will be rendered when using header fields as form section dividers.{/tr}</p>
					<p>{tr}Set to <em>Configured</em> to use the two following fields.{/tr}</p>
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="useFormClasses" value="1"
							{if $info.useFormClasses eq 'y'} checked="checked"{/if}>
					{tr}Use Form Classes{/tr}
				</label>
			</div>
			<div class="form-group">
				<label for="formClasses">{tr}Input Form Classes{/tr}</label>
				<input class="form-control" type="text" name="formClasses" value="{$info.formClasses|escape}">
				<div class="help-block">
					<p>{tr}Sets classes for form to be used in Tracker Plugin (e.g. form-horizontal or col-md-9).{/tr}</p>
				</div>
			</div>
			<div class="form-group">
				<label for="viewItemPretty">{tr}Template to display an item{/tr}</label>
				<input class="form-control" type="text" name="viewItemPretty" value="{$info.viewItemPretty|escape}">
				<div class="help-block">
					{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}
				</div>
			</div>
			<div class="form-group">
				<label for="editItemPretty">{tr}Template to edit an item{/tr}</label>
				<input class="form-control" type="text" name="editItemPretty" value="{$info.editItemPretty|escape}">
				<div class="help-block">
					{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showStatus" value="1"
						{if $info.showStatus eq 'y'} checked="checked"{/if}>
					{tr}Show status{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showStatusAdminOnly" value="1"
						{if $info.showStatusAdminOnly eq 'y'} checked="checked"{/if}>
					{tr}Show status to tracker administrator only{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showCreated" value="1"
						{if $info.showCreated eq 'y'} checked="checked"{/if}>
					{tr}Show creation date when listing items{/tr}
				</label>
			</div>
			<div class="form-group depends" data-on="showCreated">
				<label for="showCreatedFormat">{tr}Creation date format{/tr}</label>
				<input type="text" name="showCreatedFormat" value="{$info.showCreatedFormat|escape}">
				<div class="help-block">
					<a rel="external" class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</div>
			</div>
			<div class="depends checkbox" data-on="showCreated">
				<label>
					<input type="checkbox" name="showCreatedBy" value="1"
						{if $info.showCreatedBy eq 'y'} checked="checked"{/if}>
					{tr}Show item creator{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showCreatedView" value="1"
						{if $info.showCreatedView eq 'y'} checked="checked"{/if}>
					{tr}Show creation date when viewing items{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showLastModif" value="1"
						{if $info.showLastModif eq 'y'} checked="checked"{/if}>
					{tr}Show last modification date when listing items{/tr}
				</label>
			</div>
			<div class="depends checkbox" data-on="showLastModif">
				<label>
					<input type="checkbox" name="showLastModifBy" value="1"
						{if $info.showLastModifBy eq 'y'} checked="checked"{/if}>
					{tr}Show item last modifier{/tr}
				</label>
			</div>
			<div class="form-group depends" data-on="showLastModif">
				<label for="showLastModifFormat">{tr}Modification date format{/tr}</label>
				<input class="form-control" type="text" name="showLastModifFormat" value="{$info.showLastModifFormat|escape}">
				<div class="help-block">
					<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="showLastModifView" value="1"
						{if $info.showLastModifView eq 'y'} checked="checked"{/if}>
					{tr}Show last modification date when viewing items{/tr}
				</label>
			</div>
			<div class="form-group">
				<label for="defaultOrderKey">{tr}Default sort order{/tr}</label>
				<select name="defaultOrderKey" class="form-control">
					{foreach from=$sortFields key=k item=label}
						<option value="{$k|escape}" {if $k eq $info.defaultOrderKey} selected="selected"{/if}>{$label|truncate:42:'...'|escape}</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label for="defaultOrderDir">{tr}Default sort direction{/tr}</label>
				<select name="defaultOrderDir" class="form-control">
					<option value="asc" {if $info.defaultOrderDir eq 'asc'}selected="selected"{/if}>{tr}ascending{/tr}</option>
					<option value="desc" {if $info.defaultOrderDir eq 'desc'}selected="selected"{/if}>{tr}descending{/tr}</option>
				</select>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="doNotShowEmptyField" value="1"
						{if $info.doNotShowEmptyField eq 'y'} checked="checked"{/if}>
					{tr}Hide empty fields from item view{/tr}
				</label>
			</div>
			<div class="form-group">
				<label for="showPopup">{tr}List detail popup{/tr}</label>
				{object_selector_multi type=trackerfield tracker_id=$info.trackerId _simplevalue=$info.showPopup _separator="," _simplename="showPopup"}
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="adminOnlyViewEditItem" value="1"
						{if $info.adminOnlyViewEditItem eq 'y'} checked="checked"{/if}>
					{tr}Restrict non admins to wiki page access only{/tr}
					<div class="help-block">
						{tr}Only users with admin tracker permission (tiki_p_admin_trackers) can use the built-in tracker interfaces (tiki-view_tracker.php and tiki-view_tracker_item.php). This is useful if you want the users of these trackers to only access them via wiki pages, where you can use the various tracker plugins to embed forms and reports.{/tr}
					</div>
				</label>
			</div>
		{/accordion_group}
		{accordion_group title="{tr}Status{/tr}"}
			<div class="form-group">
				<label for="newItemStatus">{tr}New item status{/tr}</label>
				<select name="newItemStatus" class="form-control">
					{foreach key=st item=stdata from=$statusTypes}
						<option value="{$st|escape}"
							{if $st eq $info.newItemStatus} selected="selected"{/if}>
							{$stdata.label|escape}
						</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label for="modItemStatus">{tr}Modified item status{/tr}</label>
				<select name="modItemStatus" class="form-control">
					<option value="">{tr}No change{/tr}</option>
					{foreach key=st item=stdata from=$statusTypes}
						<option value="{$st|escape}"
							{if $st eq $info.modItemStatus} selected="selected"{/if}>
							{$stdata.label|escape}
						</option>
					{/foreach}
				</select>
			</div>
			<div class="form-group">
				<label>{tr}Default status displayed in list mode{/tr}</label>
				<div>
					{foreach key=st item=stdata from=$statusTypes}
						<label class="checkbox-inline">
							<input type="checkbox" name="defaultStatus[]" value="{$st|escape}"{if in_array($st, $statusList)} checked="checked"{/if}>
							{$stdata.label|escape}
						</label>
					{/foreach}
				</div>
			</div>
		{/accordion_group}
		{accordion_group title="{tr}Notifications{/tr}"}
			<div class="form-group">
				<label for="outboundEmail">{tr}Copy activity to email{/tr}</label>
				<input name="outboundEmail" value="{$info.outboundEmail|escape}" class="email_multi form-control" size="60">
				<div class="help-block">
					{tr}You can add several email addresses by separating them with commas.{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="simpleEmail" value="1"
						{if $info.simpleEmail eq 'y'} checked="checked"{/if}>
					{tr}Use simplified email format{/tr}
				</label>
				<div class="help-block">
					{tr}The tracker will use the text field named Subject if any as subject and will use the user email or for anonymous the email field if any as sender{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="publishRSS" value="1"
						{if $prefs.feed_tracker neq 'y'}disabled="disabled"{/if}
						{if $info.publishRSS eq 'y'}checked="checked"{/if}>
					{tr}Publish RSS feed for this tracker{/tr}
				</label>
				<div class="help-block">
					{tr}Requires "RSS per tracker" to be set in Admin/RSS{/tr}
					{if $prefs.feed_tracker eq 'y'}
						{tr}(Currently set){/tr}
					{else}
						{tr}(Currently not set){/tr}
					{/if}
				</div>
			</div>

			{if $prefs.feature_groupalert eq 'y'}
				<div class="form-group">
					{tr}Group alerted on item modification{/tr}
					<select name="groupforAlert">
						<option value=""></option>
						{foreach from=$groupList item=g}
							<option value="{$g|escape}" {if $g eq $groupforAlert}selected="selected"{/if}>{$g|escape}</option>
						{/foreach}
					</select>
				</div>
				<div class="checkbox">
					<input type="checkbox" name="showeachuser" value="1"
						{if $showeachuser eq 'y'}checked="checked"{/if}>
					{tr}Allow user selection for small groups{/tr}
				</div>
			{/if}
		{/accordion_group}
		{accordion_group title="{tr}Permissions{/tr}"}
			<div class="checkbox">
				<label>
					<input type="checkbox" name="userCanSeeOwn" value="1"
						{if $info.userCanSeeOwn eq 'y'}checked="checked"{/if}>
					{tr}User can see his own items{/tr}
				</label>
				<div class="description">
					{tr}The tracker needs a user field with the auto-assign activated{/tr}.
					{tr}No extra permission is needed at the tracker permissions level to allow a user to see just his own items through Plugin TrackerList with the param view=user{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="writerCanModify" value="1"
						{if $info.writerCanModify eq 'y'}checked="checked"{/if}>
					{tr}Item creator can modify his items{/tr}
				</label>
				<div class="description help-block">
					{tr}The tracker needs a user field with the auto-assign activated{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="writerCanRemove" value="1"
						{if $info.writerCanRemove eq 'y'}checked="checked"{/if}>
					{tr}Item creator can remove his items{/tr}
				</label>
				<div class="description help-block">
					{tr}The tracker needs a user field with the auto-assign activated{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="userCanTakeOwnership" value="1"
						{if $info.userCanTakeOwnership eq 'y'}checked="checked"{/if}>
					{tr}User can take ownership of item created by anonymous{/tr}
				</label>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="oneUserItem" value="1"
						{if $info.oneUserItem eq 'y'}checked="checked"{/if}>
					{tr}Only one item per user or IP{/tr}
				</label>
				<div class="description help-block">
					{tr}The tracker needs a user or IP address field with the auto-assign set to Creator{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="writerGroupCanModify" value="1"
						{if $info.writerGroupCanModify eq 'y'}checked="checked"{/if}>
					{tr}Members of the creator group can modify items{/tr}
				</label>
				<div class="description help-block">
					{tr}The tracker needs a group field with the auto-assign activated{/tr}
				</div>
			</div>
			<div class="checkbox">
				<label>
					<input type="checkbox" name="writerGroupCanRemove" value="1"
						{if $info.writerGroupCanRemove eq 'y'}checked="checked"{/if}>
					{tr}Members of the creator group can remove items{/tr}
				</label>
				<div class="description help-block">
					{tr}The tracker needs a group field with the auto-assign activated{/tr}
				</div>
			</div>
			<fieldset>
				<legend>{tr}Creation date constraint{/tr}</legend>
				<div class="description">
					{tr}The tracker will be <strong>open</strong> for non-admin users through wiki pages with PluginTracker <strong>only</strong> during the period 'After' the start date and/or 'Before' the end date set below{/tr}.
				</div>
				<div class="form-group depends" data-on="start">
					<label for="startDate">{tr}Date{/tr}</label>
					<input type="date" name="startDate" value="{$startDate|escape}" class="form-control">
				</div>
				<div class="form-group depends" data-on="start">
					<label for="startTime">{tr}Time{/tr}</label>
					<input type="time" name="startTime" value="{$startTime|default:'00:00'|escape}" class="form-control">
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="end" value="1"
							{if $info.end}checked="checked"{/if}>
						{tr}Before{/tr}
					</label>
				</div>
				<div class="form-group depends" data-on="end">
					<label for="endDate">{tr}Date{/tr}</label>
					<input type="date" name="endDate" value="{$endDate|escape}" class="form-control">
				</div>
				<div class="form-group depends" data-on="end">
					<label for="endTime">{tr}Time{/tr}</label>
					<input type="time" name="endTime" value="{$endTime|default:'00:00'|escape}" class="form-control">
				</div>
			</fieldset>
		{/accordion_group}
		{if $prefs.feature_categories eq 'y'}
			{accordion_group title="{tr}Categories{/tr}"}
				<div class="form-group">
					{include file='categorize.tpl' notable=y auto=y}
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" name="autoCreateCategories" value="1"
							{if $info.autoCreateCategories eq 'y'}checked="checked"{/if}>
						{tr}Auto-create corresponding categories{/tr}
					</label>
				</div>
			{/accordion_group}
		{/if}
		{if $prefs.groupTracker eq 'y'}
			{accordion_group title="{tr}Groups{/tr}"}
				<div class="checkbox">
					<label>
						<input type="checkbox" name="autoCreateGroup" value="1"
							{if $info.autoCreateGroup eq 'y'} checked="checked"{/if}>
						{tr}Create a group for each item{/tr}
					</label>
				</div>
				<div class="form-group depends" data-on="autoCreateGroup">
					<label for="autoCreateGroupInc">{tr}Groups will include{/tr}</label>
					<select name="autoCreateGroupInc" class="form-control">
						<option value=""></option>
						{foreach from=$groupList item=g}
							<option value="{$g|escape}" {if $g eq $info.autoCreateGroupInc}selected="selected"{/if}>{$g|escape}</option>
						{/foreach}
					</select>
				</div>
				<div class="checkbox depends" data-on="autoCreateGroup">
					<label>
						<input type="checkbox" name="autoAssignCreatorGroup" value="1"
							{if $info.autoAssignCreatorGroup eq 'y'} checked="checked"{/if}>
						{tr}Creator is assigned to the group{/tr}
					</label>
				</div>
				<div class="checkbox depends" data-on="autoCreateGroup">
					<label>
						<input type="checkbox" name="autoAssignCreatorGroupDefault" value="1"
							{if $info.autoAssignCreatorGroupDefault eq 'y'} checked="checked"{/if}>
						{tr}Will become the creator's default group{/tr}
					</label>
				</div>
				<div class="checkbox depends" data-on="autoCreateGroup">
					<label>
						<input type="checkbox" name="autoAssignGroupItem" value="1"
							{if $info.autoAssignGroupItem eq 'y'} checked="checked"{/if}>
						{tr}Will become the new item's group creator{/tr}
					</label>
				</div>
				<div class="checkbox depends" data-on="autoCreateGroup">
					<label>
						<input type="checkbox" name="autoCopyGroup" value="1"
							{if $info.autoCopyGroup eq 'y'} checked="checked"{/if}>
						{tr}Copy the default group in the field ID before updating the group{/tr}
					</label>
				</div>
			{/accordion_group}
		{/if}
	{/accordion}
	<div class="form-group submit">
		<input type="hidden" name="confirm" value="1">
		<input type="hidden" name="trackerId" value="{$trackerId|escape}">
		<input type="submit" class="btn btn-primary" value="{tr}Save{/tr}">
	</div>
</form>
	{/block}
