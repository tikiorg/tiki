{* $Id$ *}

<div class="adminWizardIconleft"><img src="img/icons/large/wizard_profiles48x48.png" alt="{tr}Check out some profiles{/tr}" title="{tr}Profiles Wizard{/tr}" /></div>
{tr}Each of these profiles create a working instance of some features, such as trackers and wiki pages customized for specific purposes, for example{/tr}. 
{tr}They are initially intended for testing environments, so that, after you have played with the feature, you don't have to deal with removing the created objects, nor with restoring the potentially changed settings in your site{/tr}. 
{tr}Once you know what they do, you can also apply them in your production site, in order to have working templates of the underlying features, that you can further adapt to your site later on{/tr}.</br></br>
<div class="adminWizardContent">
<fieldset>
	<legend>{tr}Demo Profiles{/tr}</legend>
	<table style="width:100%">
	<tr>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_bug_tracker48x48.png" alt="{tr}Bug Tracker{/tr}" /></div>
	<b>{tr}Bug Tracker{/tr}</b> (<a href="tiki-admin.php?profile=Bug_Tracker&categories%5B%5D=12.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile allows you to see a tracker in action with some demo data, and a custom interface in a wiki page to add new items, as well as having them listed for you below.{/tr}
	<ul>
	<li>{tr}Uses PluginTracker in a wiki page to add items{/tr}</li>
    <li>{tr}Create some custom feedback for message to the user after item insertion{/tr}</li>
    <li>{tr}Uses PluginTrackerList to display inserted items{/tr}</li>
	<br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Trackers" target="_blank">{tr}Trackers in doc.tiki.org{/tr}</a></em>
	</ul>
	</td>
	<td style="width:4%"> 
	&nbsp;
	</td>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_dynamic_items_list48x48.png" alt="{tr}Dynamic Items List{/tr}" /></div>
	<b>{tr}Dynamic Items List{/tr}</b> (<a href="tiki-admin.php?profile=Dynamic_items_list_demo&categories%5B%5D=12.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)<br/>
	<br>
	{tr}This profile creates two linked trackers. One that allow pre-selecting the items in a drop down list based on the items in a previous drop down field. And a second tracker that holds the options displayed in the drop down fields.{/tr}
	<ul>
	<li>{tr}Useful for Geographic data (State, Country/Province, ...){/tr}</li>
    <li>{tr}Useful for Types and Subtypes{/tr}</li>
    <li>{tr}Useful for Program Names and Versions{/tr}</li>
    <li>{tr}Easily manage the options in the second tracker without editing the dropdown in the first tracker{/tr} </li> 
	<br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/Dynamic+items+list" target="_blank">{tr}Dynamic items list in doc.tiki.org{/tr}</a></em>
	</ul>
	</td>
	</tr>
	<tr>
	<td style="width:48%">
	<div class="adminWizardIconright"><img src="img/icons/large/profile_tracker_as_calendar48x48.png" alt="{tr}Tracker as Calendar{/tr}" /></div>
	<b>{tr}Tracker as Calendar{/tr}</b>  (<a href="tiki-admin.php?profile=Tracker_as_Calendar_10&categories%5B%5D=12.x&repository=http%3a%2f%2fprofiles.tiki.org%2fprofiles&page=profiles&preloadlist=y&list=List#step2" target="_blank">{tr}apply profile now{/tr}</a>)
	<br>
	{tr}This profile creates a tracker with some demo data and wiki interface that will be used to display and manage a Calendar of events in a fancy visual way.{/tr} 
	<ul>
	<li>{tr}Advanced use of Plugin TrackerList{/tr}</li>
	<li>{tr}Working example of Plugin TrackerCalendar{/tr}</li>
    <li>{tr}Drag & Drop to resize or move events{/tr}</li>
    <li>{tr}Several display modes, useful for Project & Resource Management{/tr}</li>
	<br/><em>{tr}See also{/tr} <a href="https://doc.tiki.org/PluginTrackerCalendar" target="_blank">{tr}Plugin TrackerCalendar in doc.tiki.org{/tr}</a></em>
	</ul>
	</td>
	<td style="width:4%"> 
	&nbsp;
	</td>
	<td style="width:48%">
	</td>
	</tr>
	</table>
	<br>
	<em>{tr}See also{/tr} <a href="tiki-admin.php?page=profiles&alt=Profiles" target="_blank">{tr}Profiles admin panel{/tr}</a></em>
</fieldset>
<br>
</div>

