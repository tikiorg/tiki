{* $Id$ *}<!DOCTYPE html>
{if ! $plain}
	{block name=title}{/block}
{/if}
{block name=content}{/block}
{if $headerlib}
	{$headerlib->output_js_config()}
	{$headerlib->output_js_files()}
	{$headerlib->output_js()}
{/if}
{if !empty($smarty.request.show_smarty_debug)}
	{debug}
{/if}
