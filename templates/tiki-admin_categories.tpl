{* $Id$ *}
{title help="Categories" admpage="category"}{tr}Admin Categories{/tr}{/title}

<div class="navbar">
	{button href="tiki-browse_categories.php?parentId=$parentId" _text="{tr}Browse Category{/tr}" _title="{tr}Browse the category system{/tr}"}
	{button href="tiki-edit_categories.php" _text="{tr}Organize Objects{/tr}" _title="{tr}Organize Objects{/tr}"}
</div>

{if !empty($errors)}
	<div class="simplebox highlight">{section name=ix loop=$errors}{$errors[ix]}{/section}</div>
{/if}

<div class="tree" id="top">
	<div class="treetitle">{tr}Current category:{/tr} 
		<a href="tiki-admin_categories.php?parentId=0" class="categpath">{tr}Top{/tr}</a>
		{if $parentId != 0}
		{foreach $path as $id=>$name}
			&nbsp;::&nbsp;
			<a class="categpath" href="tiki-admin_categories.php?parentId={$id}">{$name|escape}</a>
		{/foreach}
		<br>
		{tr}Current Category ID:{/tr} {$parentId}
		{/if}
	</div>
</div>

{$tree}

{tabset}
	{tab name="{tr}Create/Edit category{/tr}"}
		{if $categId > 0}
			<h2>{tr}Edit this category:{/tr} <b>{$categoryName|escape}</b> </h2>
			{button href="tiki-admin_categories.php?parentId=$parentId#editcreate" _text="{tr}Create New{/tr}" _title="{tr}Create New{/tr}"}
		{else}
			<h2>{tr}Add new category{/tr}</h2>
		{/if}
		<form action="tiki-admin_categories.php" method="post">
			<input type="hidden" name="categId" value="{$categId|escape}">
			<table class="formcolor">
				<tr>
					<td>{tr}Parent:{/tr}</td>
					<td>
						<select name="parentId">
							{if $tiki_p_admin_categories eq 'y'}<option value="0">{tr}Top{/tr}</option>{/if}
								{foreach $categories as $category}
								<option value="{$category.categId}" {if $category.categId eq $parentId}selected="selected"{/if}>{$category.categpath|escape}</option>
								{/foreach}
						</select>
					</td>
				</tr>
				<tr>
					<td>{tr}Name:{/tr}</td>
					<td><input type="text" size="40" name="name" value="{$categoryName|escape}"></td>
				</tr>
				<tr>
					<td>{tr}Description:{/tr}</td>
					<td><textarea rows="2" cols="40" name="description">{$description|escape}</textarea></td>
				</tr>
				{if $tiki_p_admin_categories == 'y'}
				<tr>
					<td>{tr}Apply parent category permissions{/tr}</td>
					<td><input type="checkbox" name="parentPerms" {if empty($categId)}checked="checked"{/if}></td>
				</tr>
				{/if}
				<tr>
					<td align="center" colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}"></td>
				</tr>
			</table>
		</form>
	{/tab}

	{if $categId <= 0}
		{tab name="{tr}Batch upload{/tr}"}
			<h2>{tr}Batch upload (CSV file){/tr}<a {popup text='category,description,parent&lt;br /&gt;vegetable,vegetable&lt;br /&gt;potato,,vegetable'}>{icon _id='help'}</a></h2>
			<form action="tiki-admin_categories.php" method="post" enctype="multipart/form-data">
				<input type="file" name="csvlist"><br>
				<input type="submit" name="import" value="{tr}Add{/tr}">
			</form>
		{/tab}
	{/if}
	{if $parentId != 0}
	{tab name="{tr}Objects in category{/tr}"}
		<h2>{tr}Objects in category:{/tr} {$categ_name|escape}</h2>
		{if $objects}
			<form method="get" action="tiki-admin_categories.php">
				<label>{tr}Find:{/tr}<input type="text" name="find"></label>
				<input type="hidden" name="parentId" value="{$parentId|escape}">
				<input type="submit" value="{tr}Filter{/tr}" name="search">
				<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
				<input type="hidden" name="find_objects" value="{$find_objects|escape}">
			</form>
		{/if}
		<table class="normal">
			<tr>
				<th>&nbsp;</th>
				<th>
					<a href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}#objects">{tr}Name{/tr}</a>
				</th>
				<th>
					<a href="tiki-admin_categories.php?parentId={$parentId}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'type_desc'}type_asc{else}type_desc{/if}#objects">{tr}Type{/tr}</a>
				</th>
			</tr>
			{cycle values="even,odd" print=false}
			{section name=ix loop=$objects}
				<tr class="{cycle}">
					<td class="icon">
						<a href="tiki-admin_categories.php?parentId={$parentId}&amp;removeObject={$objects[ix].catObjectId}&amp;fromCateg={$parentId}" title="{tr}Remove from this Category{/tr}">{icon _id='link_delete' alt="{tr}Remove from this Category{/tr}"}</a>
					</td>
					<td class="text">
						<a href="{$objects[ix].href}" title="{$objects[ix].name}">{$objects[ix].name|truncate:80:"(...)":true|escape}</a>
					</td>
					<td class="text">{tr}{$objects[ix].type}{/tr}</td>
				</tr>
			{sectionelse}
				{norecords _colspan=3}
			{/section}
		</table>

		{pagination_links cant=$cant_objects step=$prefs.maxRecords offset=$offset}{/pagination_links}
	{/tab}
	
		{tab name="{tr}Moving objects between categories{/tr}"}
			<h2>{tr}Moving objects between categories{/tr}</h2>
			<form method="get" action="tiki-admin_categories.php" name="move">
				<input type="hidden" name="parentId" value="{$parentId|escape}">
				<input type="submit" name="unassign" value="{tr}Unassign all objects from this category{/tr}">
				<hr>
				<select name="toId">
				{foreach $categories as $category}
					<option value="{$category.categId}" {if $category.categId eq $parentId}selected="selected"{/if}>{$category.categpath|escape}</option>
				{/foreach}
				</select>
				<input type="submit" name="move_to" value="{tr}Move all the objects from this category to this one{/tr}">
				<hr>
				<select name="to">
				{foreach $categories as $category}
					<option value="{$category.categId}" {if $category.categId eq $parentId}selected="selected"{/if}>{$category.categpath|escape}</option>
				{/foreach}				</select>
				<input type="submit" name="copy_from" value="{tr}Assign all objects of this category to this one{/tr}">
			</form>
		{/tab}
				
		{tab name="{tr}Add objects to category{/tr}"}
			<h2>{tr}Add objects to category:{/tr} <b>{$categ_name|escape}</b></h2>
			<form method="get" action="tiki-admin_categories.php">
				<label>{tr}Find:{/tr}<input type="text" name="find_objects"></label>
				<input type="hidden" name="parentId" value="{$parentId|escape}">
				<input type="submit" value="{tr}Filter{/tr}" name="search_objects">
				<input type="hidden" name="sort_mode" value="{$sort_mode|escape}">
				<input type="hidden" name="offset" value="{$offset|escape}">
				<input type="hidden" name="find" value="{$find|escape}">
			</form>
			{pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
			<form action="tiki-admin_categories.php" method="post">
				<input type="hidden" name="parentId" value="{$parentId|escape}">
				<table class="formcolor">
					{if $prefs.feature_wiki eq 'y' and $pages}
						<tr>
							<td>{tr}Page:{/tr}</td>
							<td>
								<select name="pageName[]" multiple="multiple" size="5">
									{section name=ix loop=$pages}
										<option value="{$pages[ix].pageName|escape}">{$pages[ix].pageName|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td>
								<input type="submit" name="addpage" value="{tr}Add{/tr}">
							</td>
						</tr>
					{/if}
					
					{if $prefs.feature_articles eq 'y' and $articles}
						<tr>
							<td>{tr}Article:{/tr}</td>
							<td>
								<select name="articleId">
									{section name=ix loop=$articles}
										<option value="{$articles[ix].articleId|escape}">{$articles[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addarticle" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
						
					{if $prefs.feature_blogs eq 'y' and $blogs}
						<tr>
							<td>{tr}Blog:{/tr}</td>
							<td>
								<select name="blogId">
									{section name=ix loop=$blogs}
										<option value="{$blogs[ix].blogId|escape}">{$blogs[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addblog" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
					
					{if $prefs.feature_directory eq 'y'and $directories}
						<tr>
							<td>{tr}Directory:{/tr}</td>
							<td>
								<select name="directoryId">
									{section name=ix loop=$directories}
										<option value="{$directories[ix].categId|escape}">{$directories[ix].name|truncate:40:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="adddirectory" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}

					{if $prefs.feature_galleries eq 'y' and $galleries}
						<tr>
							<td>{tr}image gal:{/tr}</td>
							<td>
								<select name="galleryId">
									{section name=ix loop=$galleries}
										<option value="{$galleries[ix].galleryId|escape}">{$galleries[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addgallery" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
			
					{if $prefs.feature_file_galleries eq 'y' and $file_galleries}
						<tr>
							<td>{tr}File gal:{/tr}</td>
							<td>
								<select name="file_galleryId">
									{section name=ix loop=$file_galleries}
										<option value="{$file_galleries[ix].id|escape}">{$file_galleries[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addfilegallery" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
			
					{if $prefs.feature_forums eq 'y' and $forums}
						<tr>
							<td>{tr}Forum:{/tr}</td>
							<td>
								<select name="forumId">
									{section name=ix loop=$forums}
										<option value="{$forums[ix].forumId|escape}">{$forums[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addforum" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
				
					{if $prefs.feature_polls eq 'y' and $polls}
						<tr>
							<td>{tr}Poll:{/tr}</td>
							<td>
								<select name="pollId">
									{section name=ix loop=$polls}
										<option value="{$polls[ix].pollId|escape}">{$polls[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addpoll" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
					
					{if $prefs.feature_faqs eq 'y and $faqs'}
						<tr>
							<td>{tr}FAQ:{/tr}</td>
							<td>
								<select name="faqId">
									{section name=ix loop=$faqs}
										<option value="{$faqs[ix].faqId|escape}">{$faqs[ix].title|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addfaq" value="{tr}Add{/tr}"></td>
						</tr> 
					{/if}
				
					{if $prefs.feature_trackers eq 'y' and $trackers}
						<tr>
							<td>{tr}Tracker:{/tr}</td>
							<td>
								<select name="trackerId">
									{section name=ix loop=$trackers}
										<option value="{$trackers[ix].trackerId|escape}">{$trackers[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addtracker" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
						
					{if $prefs.feature_quizzes eq 'y' and $quizzes}
						<tr>
							<td>{tr}quiz:{/tr}</td>
							<td>
								<select name="quizId">
									{section name=ix loop=$quizzes}
										<option value="{$quizzes[ix].quizId|escape}">{$quizzes[ix].name|truncate:80:"(...)":true|escape}</option>
									{/section}
								</select>
							</td>
							<td><input type="submit" name="addquiz" value="{tr}Add{/tr}"></td>
						</tr>
					{/if}
				</table>
			</form>
			{pagination_links cant=$maximum step=$maxRecords offset=$offset}{/pagination_links}
		{/tab}
	{/if}
{/tabset}
