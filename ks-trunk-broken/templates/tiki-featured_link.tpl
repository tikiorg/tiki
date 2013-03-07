<br />
<iframe width="100%" name="{$title|escape}" src="{$url}" marginwidth="0" marginheight="0" height="700" scrolling="auto" frameborder="0"
{*
 * The line below is used to auto-resize the iframe height with javascript to match the content height and avoid vertical scrollbars.
 * It is only done if the url does not start with a protocol (e.g. http://, https://, ...) because web browsers generate a security alert
 *   when trying to do this with an iframe content that is not on the website (same domain name) of the page that includes the iframe.
 *}
{if ! ereg('^[a-z]+:\/\/', $url)} onload="this.height = this.contentWindow.document.body.scrollHeight"{/if}>
</iframe>
