<page zip="{$info.zip}">
<name><![CDATA[{$info.pageName}]]></name>
{if $info.description}<description><![CDATA[{$info.description}]]></description>{/if}
{if $info.comment}<comment><![CDATA[{$info.comment}]]></comment>{/if}
<creator><![CDATA[{$info.creator}]]></creator>
<user><![CDATA[{$info.user|escape:"html"}]]></user>
{if $info.lang}<lang>{$info.lang}</lang>{/if}
<is_html>{$info.is_html}</is_html>
<wysiwyg>{$info.wysiwyg}</wysiwyg>
{*<data><![CDATA[{$info.data}]]></data>*}
{if $config.comments and !empty($comments)}
<comments>
{foreach from=$comments item=comment}
	{include file='tiki-export_comment_xml.tpl'}
{/foreach}
</comments>
{/if}
{if $config.images and !empty($images)}
<images>
{foreach from=$images item=img}
	<image zip="{$img.zip}"
	{if $img.filename} filename="{$img.filename}"{/if}
	{if $img.name} name="{$img.name}"{/if}
	{if $img.galleryId} galleryId="{$img.galleryId}"{/if}
	{if $img.id} id="{$img.id}"{/if}
	{if $img.where} where="{$img.where}"{/if}
	><wiki><![CDATA[{$img.wiki}]]></wiki></image>
{/foreach}
</images>
{/if}
{if $config.attachments and !empty($attachments)}
<attachments>
{foreach from=$attachments item=att}
	<attachment filename="{$att.filename}" attId="{$att.attId}" zip="{$att.zip}">
		<filesize>{$att.filesize}</filesize>
		<filetype>{$att.filetype}</filetype>
		<user><![CDATA[{$att.user}]]></user>
		{if $att.comment}<comment><![CDATA[{$att.comment}]]></comment>{/if}
	</attachment>
{/foreach}
</attachments>
{/if}
{if $config.history and !empty($history)}
<history>
{foreach from=$history item=hist}
	<version version="{$hist.version}" zip="{$hist.zip}">
		  <user><![CDATA[{$hist.user}]]></user>
		  {if $hist.description}<description><![CDATA[{$hist.description}]]></description>{/if}
		  {if $hist.comment}<comment><![CDATA[{$hist.comment}]]></comment>{/if}
	</version>
{/foreach}
</history>
{/if}
</page>
