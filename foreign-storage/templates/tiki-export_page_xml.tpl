<page zip="{$info.zip}">
<name><![CDATA[{$info.pageName|escape:'html'}]]></name>
{if $info.description}<description><![CDATA[{$info.description|escape:'html'}]]></description>{/if}
{if $info.comment}<comment><![CDATA[{$info.comment|escape:'html'}]]></comment>{/if}
<creator><![CDATA[{$info.creator|escape:'html'}]]></creator>
<user><![CDATA[{$info.user|escape:"html"}]]></user>
{if $info.lang}<lang>{$info.lang}</lang>{/if}
<is_html>{$info.is_html}</is_html>
<wysiwyg>{$info.wysiwyg}</wysiwyg>
<created>{$info.created}</created>
<lastModif>{$info.lastModif}</lastModif>
{*<data><![CDATA[{$info.data|escape:'html'}]]></data>*}
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
	{if $img.name} name="{$img.name|escape:'html'}"{/if}
	{if $img.galleryId} galleryId="{$img.galleryId}"{/if}
	{if $img.id} id="{$img.id}"{/if}
	{if $img.where} where="{$img.where}"{/if}
	><wiki><![CDATA[{$img.wiki|escape:'html'}]]></wiki></image>
{/foreach}
</images>
{/if}
{if $config.attachments and !empty($attachments)}
<attachments>
{foreach from=$attachments item=att}
	<attachment filename="{$att.filename|escape:'html'}" attId="{$att.attId}" zip="{$att.zip}">
		<filesize>{$att.filesize}</filesize>
		<filetype>{$att.filetype}</filetype>
		<user><![CDATA[{$att.user|escape:'html'}]]></user>
		{if $att.comment}<comment><![CDATA[{$att.comment|escape:'html'}]]></comment>{/if}
		<created>{$att.created}</created>
	</attachment>
{/foreach}
</attachments>
{/if}
{if $config.history and !empty($history)}
<history>
{foreach from=$history item=hist}
	<version version="{$hist.version}" zip="{$hist.zip}">
		  <user><![CDATA[{$hist.user|escape:'html'}]]></user>
		  {if $hist.description}<description><![CDATA[{$hist.description|escape:'html'}]]></description>{/if}
		  {if $hist.comment}<comment><![CDATA[{$hist.comment|escape:'html'}]]></comment>{/if}
		  <lastModif>{$hist.lastModif}</lastModif>
	</version>
{/foreach}
</history>
{/if}
</page>
