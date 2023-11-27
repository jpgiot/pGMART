{include file="overall_header.tpl"}

<h1>{$study_data->study_acronym}</h1>
<div style="font-weight:bold;font-size:1.5em;padding-left:40px">{$study_data->study_name}</div>

{$study_data->study_design}

<h2>{$mode}</h2>
<div class="roundbox">

<div style="width:500px">
<form method="POST" action="login.php">
<input type="hidden" id="study_id" name="study_id" value="{$study_data->study_id}">
<input type="text" id="new_strata_name" name="new_strata_name">
	<div style="padding-left:50px">
	<label for="new_strata_option_0">0:</label>
	<input type="text" id="new_strata_option_0" name="new_strata_option_0">
	<br />
	<label for="new_strata_option_1">1:</label>
	<input type="text" id="new_strata_option_1" name="new_strata_option_1">
	</div>

<input type="submit" value="Add">
</form>

</div>

</div>


{include file="overall_footer.tpl"}
