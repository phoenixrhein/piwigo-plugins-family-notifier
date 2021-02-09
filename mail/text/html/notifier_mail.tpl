<p>Hallo</p>

<p>es ist wieder soweit. Neue Fotos sind da!!!</p>
<p>&nbsp;</p>

{foreach from=$albums item=album}
{assign var=derivative value=$pwg->derivative($derivative_params, $album.src_image)}
{strip}
<p style="padding-top:15px;"><a href="{$url}{$album.url}">{$album.name} von {$album.date}</a></p>
<p style="padding-top:0px;"><a href="{$url}{$album.url}"><img src="{$derivative->get_url()}" alt="{$album.name}" title="{$album.name}"></a></p>
{/strip}
{/foreach}

{if $notes != ""}
<p>&nbsp;</p>
<p>{$notes}</p>
{/if}
<p>&nbsp;</p>
<p>Viel Spaß beim Anschauen der Bilder.</p>

<p>Bastian</p>