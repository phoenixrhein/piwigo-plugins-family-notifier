<!-- Show the title of the plugin -->
<div class="titlePage">
 <h2>{'FamilyNotifier'|@translate}</h2>
</div>
 
<p>&nbsp;</p>
<div>
<p>Der FamilyNotifier benachrichtigt die Famile oder Freunde über neue Alben. Einfach Alben auswählen und wer informiert werden soll.</p>
</div>
<p>&nbsp;</p>

<form action="" method="post">

<div style="width:100%;padding-right:15px;padding-left:15px;margin-right:auto;margin-left:auto;">
  <div style="display:flex;flex-wrap:wrap;margin-right:-15px;margin-left:-15px;">
    <div style="flex-basis:0;flex-grow:1;max-width:100%;">
      <div>
		{foreach from=$albums item=album}
		{strip}
		<div style="padding-left:150px;">
				<input type="checkbox" id="album{$album.id}" value="{$album.id}" name="album[]"/>
				<label for="album{$album.id}">{$album.name} ({$album.date})</label>
			
		</div>
		{/strip}
		{/foreach}
	</div>
   </div>
   {counter start=0 skip=1 print=false assign=number}
    <div style="flex-basis:0;flex-grow:1;max-width:100%;">
    	{foreach from=$receivers item=receiver}
    	    {if $receiver.enable == false}
     			{continue}
    		{/if}
    	<div style="padding-left:150px;">
			<input type="checkbox" id="email{$number}" name="email[]" value="{$receiver.email}" />
			<label for="email{$number}">{$receiver.email}</label>
		</div>
		{counter print=false}
		{/foreach}

		<div style="padding-left:150px;">
			<input type="checkbox" id="email7" name="email_custom_checked" value="1"/>
			<input type="text" id="email7" name="email_custom"/>
		</div>
		
		<div style="padding-left:150px;padding-top:30px;">
			<label style="display:block;" for="text">Anmerkung</label>
      		<textarea id="text" name="notes" cols="35" rows="4"></textarea>
      	</div>
		
		<div style="padding-left:150px;">
			<p class="formButtons"><input type="submit" name="send_notifier" value="Senden"></p>
		</div>
		
    	
    </div>
  </div>
</div>

</form>