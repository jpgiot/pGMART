{include file="overall_header.tpl"}

<h1>{$study_data->study_acronym}</h1>
<div style="font-weight:bold;font-size:1.5em;padding-left:40px">{$study_data->study_name}</div>

<h2>Study globals</h2>

<div class="roundbox">
<div style="width:500px">
<form method="POST" action="study_design.php">
<input type="hidden" id="study_id" name="study_id" value="{$study_data->study_id}">
<p><b>Allocation odds</b></p>
<label for="group0">Relative size of treatment group 0</label>
<input type="text" id="group0" name="group0" value="{$study_globals.group0}">
<br />
<label for="group1">Relative size of treatment group 1</label>
<input type="text" id="group1" name="group1" value="{$study_globals.group1}">

<p>Actually, the allocation odds for treatment 0 is {$study_globals.allocation_odds}</p>

<p><b>Stratum weight</b></p>
<label for="group1">Weight</label>
<input type="text" id="stratum_weight" name="stratum_weight" value="{$study_globals.stratum_weight}">

</div>

<input type="submit" value="Update">
</form>

</div>

</div>


<h2>Stratification</h2>

{*
<div class="roundbox">
<p>Add new stratum</p>
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
*}


<a class="menubutton" href="study_design_new_stratification.php?study_id={$study->study_id}">
Add stratum
</a>
<p>The current design of the study has these strata :</p>
<table class="table_lines">
<tr>
<th>Stratification name</th>
<th>Weight</th>
<th>Values</th>
<th>Edit</th>
</tr>

{foreach $stratification as $name => $options}
<tr>
<td>
{$name}
</td>
<td>
{$options.weight}
</td>
<td>
{foreach $options.values as $value_id => $value_label} {$value_id}: {$value_label}<br />{/foreach}
</td>
<td>
<a class="menubutton" href="study_design_edit_stratum.php?study_id={$study->study_id}">
Edit this stratum
</a>
</td>
</tr>
{/foreach}
</table>



{include file="overall_footer.tpl"}
