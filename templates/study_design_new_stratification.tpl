{include file="overall_header.tpl"}

<div class="study_acronym">{$study_data->study_acronym}</div>
<div style="font-weight:bold;font-size:1.5em;padding-left:40px">{$study_data->study_name}</div>

{$study_data->study_design}

<div class="roundbox">

<div style="width:500px">
<form method="POST" action="study_design_new_stratification.php">
<input type="hidden" id="study_id" name="study_id" value="{$study_data->study_id}">
<label for="new_stratification_name">Stratification name</label><br />
<input type="text" id="new_stratification_name" name="new_stratification_name"{if $default} value="treatment"{/if}><br />
<label for="new_stratification_weight">Stratification weight</label><br />
<input type="text" id="new_stratification_weight" name="new_stratification_weight"><br />
<br />
Values: 
{for $i=0 to 10}
	<div style="padding-left:50px">
	<label for="new_stratification_option_{$i}">{$i}:</label>
	<input type="text" id="new_stratification_option_{$i}" name="new_stratification_option_{$i}"{if $default} value="treatment {$i}"{/if}>
	</div>
	<br />
{/for}

<input type="submit" value="Add this new stratification">
</form>


</div>




{include file="overall_footer.tpl"}
