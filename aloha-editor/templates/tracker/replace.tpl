<form method="post" class="simple" action="{service controller=tracker action=replace}">
	<div class="accordion">
		<h4>{tr}General{/tr}</h4>
		<div>
			<label>
				{tr}Name{/tr}
				<input type="text" name="name" value="{$info.name|escape}" required="required"/>
			</label>
			<label>
				{tr}Description{/tr}
				
				<textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea>
			</label>
			<label>
				<input type="checkbox" name="descriptionIsParsed" {if $info.descriptionIsParsed eq 'y'}checked="checked"{/if} value="1" />
				{tr}Description is wiki-parsed{/tr}
			</label>
		</div>
		<h4>{tr}Features{/tr}</h4>
		<div>
			<label>
				<input type="checkbox" name="useRatings" value="1"
					{if $info.useRatings eq 'y'} checked="checked"{/if}/>
				{tr}Allow ratings (deprecated, use rating field){/tr}
			</label>
			<label class="depends" data-on="useRatings">
				{tr}Rating options{/tr}
				<input type="text" name="ratingOptions" value="{if $info.ratingOptions}{$info.ratingOptions|escape}{else}-2,-1,0,1,2{/if}" />
			</label>
			<label class="depends" data-on="useRatings">
				<input type="checkbox" name="showRatings" value="1"
					{if $info.showRatings eq 'y'} checked="checked"{/if}/>
				{tr}Show ratings in listing{/tr}
			</label>
			<label>
				<input type="checkbox" name="useComments" value="1"
					{if $info.useComments eq 'y'} checked="checked"{/if}/>
				{tr}Allow comments{/tr}
			</label>
			<label class="depends" data-on="useComments">
				<input type="checkbox" name="showComments" value="1"
					{if $info.showComments eq 'y'} checked="checked"{/if}/>
				{tr}Show comments in listing{/tr}
			</label>
			<label class="depends" data-on="useComments">
				<input type="checkbox" name="showLastComment" value="1"
					{if $info.showLastComment eq 'y'} checked="checked"{/if}/>
				{tr}Display last comment author and date{/tr}
			</label>
			<label>
				<input type="checkbox" name="useAttachments" value="1"
					{if $info.useAttachments eq 'y'} checked="checked"{/if}/>
				{tr}Allow attachments{/tr}
			</label>
			<label class="depends" data-on="useAttachments">
				<input type="checkbox" name="showAttachments" value="1"
					{if $info.showAttachments eq 'y'} checked="checked"{/if}/>
				{tr}Display attachments in listing{/tr}
			</label>
			<fieldset class="depends sortable" data-on="useAttachments" data-selector="label">
				<legend>{tr}Attachment attributes (sortable){/tr}</legend>
				{foreach from=$attachmentAttributes key=name item=att}
					<label>
						<input type="checkbox" name="orderAttachments[]" value="{$name|escape}" {if $att.selected} checked="checked"{/if}/>
						{$att.label|escape}
					</label>
				{/foreach}
			</fieldset>
		</div>
		<h4>{tr}Display{/tr}</h4>
		<div>
			<label>
				{tr}Section format{/tr}
				<select name="sectionFormat">
					<option value="flat"{if $info.sectionFormat eq 'flat'} selected="selected"{/if}>{tr}Title{/tr}</option>
					<option value="tab"{if $info.sectionFormat eq 'tab'} selected="selected"{/if}>{tr}Tabs{/tr}</option>
				</select>
				<div class="description">
					{tr}Determines how headers will be rendered when using header fields as form section dividers.{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="showStatus" value="1"
					{if $info.showStatus eq 'y'} checked="checked"{/if}/>
				{tr}Show status{/tr}
			</label>
			<label>
				<input type="checkbox" name="showStatusAdminOnly" value="1"
					{if $info.showStatusAdminOnly eq 'y'} checked="checked"{/if}/>
				{tr}Show status to tracker administrator only{/tr}
			</label>
			<label>
				<input type="checkbox" name="showCreated" value="1"
					{if $info.showCreated eq 'y'} checked="checked"{/if}/>
				{tr}Show creation date when listing items{/tr}
			</label>
			<label class="depends" data-on="showCreated">
				{tr}Creation date format{/tr}
				<input type="text" name="showCreatedFormat" value="{$info.showCreatedFormat|escape}"/>
				<div class="description">
					<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</div>
			</label>
			<label class="depends" data-on="showCreated">
				<input type="checkbox" name="showCreatedBy" value="1"
					{if $info.showCreatedBy eq 'y'} checked="checked"{/if}/>
				{tr}Show item creator{/tr}
			</label>
			<label>
				<input type="checkbox" name="showCreatedView" value="1"
					{if $info.showCreatedView eq 'y'} checked="checked"{/if}/>
				{tr}Show creation date when viewing items{/tr}
			</label>
			<label>
				<input type="checkbox" name="showLastModif" value="1"
					{if $info.showLastModif eq 'y'} checked="checked"{/if}/>
				{tr}Show last modification date when listing items{/tr}
			</label>
			<label class="depends" data-on="showLastModif">
				<input type="checkbox" name="showLastModifBy" value="1"
					{if $info.showLastModifBy eq 'y'} checked="checked"{/if}/>
				{tr}Show item last modifier{/tr}
			</label>
			<label class="depends" data-on="showLastModif">
				{tr}Modification date format{/tr}
				<input type="text" name="showLastModifFormat" value="{$info.showLastModifFormat|escape}"/>
				<div class="description">
					<a class="link" target="strftime" href="http://www.php.net/manual/en/function.strftime.php">{tr}Date and Time Format Help{/tr}</a>
				</div>
			</label>
			<label>
				<input type="checkbox" name="showLastModifView" value="1"
					{if $info.showLastModifView eq 'y'} checked="checked"{/if}/>
				{tr}Show last modification date when viewing items{/tr}
			</label>
			<label>
				{tr}Default sort order{/tr}
				<select name="defaultOrderKey">
					{foreach from=$sortFields key=k item=label}
						<option value="{$k|escape}" {if $k eq $info.defaultOrderKey} selected="selected"{/if}>{$label|truncate:42:'...'|escape}</option>
					{/foreach}
				</select>
			</label>
			<label>
				{tr}Default sort direction{/tr}
				<select name="defaultOrderDir">
					<option value="asc" {if $info.defaultOrderDir eq 'asc'}selected="selected"{/if}>{tr}ascending{/tr}</option>
					<option value="desc" {if $info.defaultOrderDir eq 'desc'}selected="selected"{/if}>{tr}descending{/tr}</option>
				</select>
			</label>
			<label>
				<input type="checkbox" name="doNotShowEmptyField" value="1"
					{if $info.doNotShowEmptyField eq 'y'} checked="checked"{/if}/>
				{tr}Hide empty fields from item view{/tr}
			</label>
			<label>
				{tr}List detail pop-up{/tr}
				<input type="text" name="showPopup" value="{$info.showPopup|escape}"/>
				<div class="description">
					{tr}Comma-separated list of field IDs{/tr}
				</div>
			</label>
			<label>
				{tr}Template to display an item{/tr}
				<input type="text" name="viewItemPretty" value="{$info.viewItemPretty|escape}"/>
				<div class="description">
					{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}
				</div>
			</label>
			<label>
				{tr}Template to edit an item{/tr}
				<input type="text" name="editItemPretty" value="{$info.editItemPretty|escape}"/>
				<div class="description">
					{tr}wiki:pageName for a wiki page or tpl:tplName for a template{/tr}
				</div>
			</label>
		</div>
		<h4>{tr}Status{/tr}</h4>
		<div>
			<label>
				{tr}New item status{/tr}
				<select name="newItemStatus">
					{foreach key=st item=stdata from=$statusTypes}
						<option value="{$st|escape}"
							{if $st eq $info.newItemStatus} selected="selected"{/if}>
							{$stdata.label|escape}
						</option>
					{/foreach}
				</select>
			</label>
			<label>
				{tr}Modified item status{/tr}
				<select name="modItemStatus">
					<option value="">{tr}No change{/tr}</option>
					{foreach key=st item=stdata from=$statusTypes}
						<option value="{$st|escape}"
							{if $st eq $info.modItemStatus} selected="selected"{/if}>
							{$stdata.label|escape}
						</option>
					{/foreach}
				</select>
			</label>
			<fieldset>
				<legend>{tr}Default status displayed in list mode{/tr}</legend>
				{foreach key=st item=stdata from=$statusTypes}
					<label>
						<input type="checkbox" name="defaultStatus[]" value="{$st|escape}"{if in_array($st, $statusList)} checked="checked"{/if} />
						{$stdata.label|escape}
					</label>
				{/foreach}
			</fieldset>
		</div>
		<h4>{tr}Notifications{/tr}</h4>
		<div>
			<label>
				{tr}Copy activity to email{/tr}
				<input type="email" name="outboundEmail" value="{$info.outboundEmail|escape}"/>
				<div class="description">
					{tr}You can add several email addresses by separating them with commas.{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="simpleEmail" value="1"
					{if $info.simpleEmail eq 'y'} checked="checked"{/if}/>
				{tr}Use simplified e-mail format{/tr}
				<div class="description">
					{tr}The tracker will use the text field named Subject if any as subject and will use the user email or for anonymous the email field if any as sender{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="publishRSS" value="1"
					{if $prefs.feed_tracker neq 'y'}disabled="disabled"{/if}
					{if $info.publishRSS eq 'y'}checked="checked"{/if}/>
				{tr}Publish RSS feed for this tracker{/tr}
				<div class="description">
					{tr}Requires "RSS per tracker" to be set in Admin/RSS{/tr}
					{if $prefs.feed_tracker eq 'y'}
						{tr}(Currently set){/tr}
					{else}
						{tr}(Currently not set){/tr}
					{/if}
				</div>
			</label>

			{if $prefs.feature_groupalert eq 'y'}
				<label>
					{tr}Group alerted on item modification{/tr}
					<select name="groupforAlert">
						<option value=""></option>
						{foreach from=$groupList item=g}
							<option value="{$g|escape}" {if $g eq $groupforAlert}selected="selected"{/if}>{$g|escape}</option>
						{/foreach}
					</select>
				</label>
				<label>
					<input type="checkbox" name="showeachuser" value="1"
						{if $showeachuser eq 'y'}checked="checked"{/if}/>
					{tr}Allow user selection for small groups{/tr}
				</label>
			{/if}
		</div>
		<h4>{tr}Permissions{/tr}</h4>
		<div>
			<label>
				<input type="checkbox" name="writerCanModify" value="1"
					{if $info.writerCanModify eq 'y'}checked="checked"{/if}/>
				{tr}Item creator can modify his items{/tr}
				<div class="description">
					{tr}The tracker needs a user field with the auto-assign activated{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="writerCanRemove" value="1"
					{if $info.writerCanRemove eq 'y'}checked="checked"{/if}/>
				{tr}Item creator can remove his items{/tr}
				<div class="description">
					{tr}The tracker needs a user field with the auto-assign activated{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="userCanTakeOwnership" value="1"
					{if $info.userCanTakeOwnership eq 'y'}checked="checked"{/if}/>
				{tr}User can take ownership of item created by anonymous{/tr}
			</label>
			<label>
				<input type="checkbox" name="oneUserItem" value="1"
					{if $info.oneUserItem eq 'y'}checked="checked"{/if}/>
				{tr}Only one item per user or IP{/tr}
				<div class="description">
					{tr}The tracker needs a user or IP address field with the auto-assign set to Creator{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="writerGroupCanModify" value="1"
					{if $info.writerGroupCanModify eq 'y'}checked="checked"{/if}/>
				{tr}Members of the creator group can modify items{/tr}
				<div class="description">
					{tr}The tracker needs a group field with the auto-assign activated{/tr}
				</div>
			</label>
			<label>
				<input type="checkbox" name="writerGroupCanRemove" value="1"
					{if $info.writerGroupCanRemove eq 'y'}checked="checked"{/if}/>
				{tr}Members of the creator group can remove items{/tr}
				<div class="description">
					{tr}The tracker needs a group field with the auto-assign activated{/tr}
				</div>
			</label>
			<fieldset>
				<legend>{tr}Creation date constraint{/tr}</legend>
				<label>
					<input type="checkbox" name="start" value="1"
						{if $info.start}checked="checked"{/if}/>
					{tr}After{/tr}
				</label>
				<label class="depends" data-on="start">
					Date
					<input type="date" name="startDate" value="{$startDate|escape}"/>
				</label>
				<label class="depends" data-on="start">
					Time
					<input type="time" name="startTime" value="{$startTime|default:'00:00'|escape}"/>
				</label>
				<label>
					<input type="checkbox" name="end" value="1"
						{if $info.end}checked="checked"{/if}/>
					{tr}Before{/tr}
				</label>
				<label class="depends" data-on="end">
					Date
					<input type="date" name="endDate" value="{$endDate|escape}"/>
				</label>
				<label class="depends" data-on="end">
					Time
					<input type="time" name="endTime" value="{$endTime|default:'00:00'|escape}"/>
				</label>
			</fieldset>
		</div>
		{if $prefs.feature_categories eq 'y'}
			<h4>{tr}Categories{/tr}</h4>
			<div>
				{include file='categorize.tpl' colsCategorize=2 auto=y}
				<label>
					<input type="checkbox" name="autoCreateCategories" value="1"
						{if $info.autoCreateCategories eq 'y'}checked="checked"{/if}/>
					{tr}Auto-create corresponding categories{/tr}
				</label>
			</div>
		{/if}
		{if $prefs.groupTracker eq 'y'}
			<h4>{tr}Groups{/tr}</h4>
			<div>
				<label>
					<input type="checkbox" name="autoCreateGroup" value="1"
						{if $info.autoCreateGroup eq 'y'} checked="checked"{/if}/>
					{tr}Create a group for each item{/tr}
				</label>
				<label class="depends" data-on="autoCreateGroup">
					{tr}Groups will include{/tr}
					<select name="autoCreateGroupInc">
						<option value=""></option>
						{foreach from=$groupList item=g}
							<option value="{$g|escape}" {if $g eq $info.autoCreateGroupInc}selected="selected"{/if}>{$g|escape}</option>
						{/foreach}
					</select>
				</label>
				<label class="depends" data-on="autoCreateGroup">
					<input type="checkbox" name="autoAssignCreatorGroup" value="1"
						{if $info.autoAssignCreatorGroup eq 'y'} checked="checked"{/if}/>
					{tr}Creator is assigned to the group{/tr}
				</label>
				<label class="depends" data-on="autoCreateGroup">
					<input type="checkbox" name="autoAssignCreatorGroupDefault" value="1"
						{if $info.autoAssignCreatorGroupDefault eq 'y'} checked="checked"{/if}/>
					{tr}Will become the creator's default group{/tr}
				</label>
				<label class="depends" data-on="autoCreateGroup">
					<input type="checkbox" name="autoAssignGroupItem" value="1"
						{if $info.autoAssignGroupItem eq 'y'} checked="checked"{/if}/>
					{tr}Will become the new item's group creator{/tr}
				</label>
				<label class="depends" data-on="autoCreateGroup">
					<input type="checkbox" name="autoCopyGroup" value="1"
						{if $info.autoCopyGroup eq 'y'} checked="checked"{/if}/>
					{tr}Copy the default group in the field ID before updating the group{/tr}
				</label>
			</div>
		{/if}
	</div>
	<div class="submit">
		<input type="hidden" name="confirm" value="1"/>
		<input type="hidden" name="trackerId" value="{$trackerId|escape}"/>
		<input type="submit" value="{tr}Save{/tr}"/>
	</div>
</form>
{jq}
$('.accordion').removeClass('accordion').accordion({
	header: 'h4',
	autoHeight: false
});
$('.simple .depends:not(.done)').each(function () {
	var current = this;
	var primary = $(this).data('on');
	var field = $(this).closest('form')[0][primary];

	$(field).change(function () {
		$(current).toggle($(this).is(':checked'));
	}).change();
}).addClass('done');
$('.simple .sortable').each(function () {
	var selector = $(this).data('selector');
	$(this).sortable({
		items: '> ' + selector,
	});
}).removeClass('sortable');
$('input[type=date]').datepicker({
	dateFormat: 'yy-mm-dd'
});
{/jq}
