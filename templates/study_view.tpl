{include file="overall_header.tpl"}

<h1>{$study_data->study_acronym}</h1>
<div style="font-weight:bold;font-size:1.5em;padding-left:40px">{$study_data->study_name}</div>

<h2>Inclusions</h2>
<p><a class="menubutton" href="study_inclusion_add.php?study_id={$study_data->study_id}">
New inclusion
</a></p>


<h2>Design</h2>
{if $study->design_warning_treatment}
<p><a class="menubutton" href="study_design_new_stratification.php?study_id={$study_data->study_id}&default=1">
Add treatment stratum
</a></p>
{/if}

<p><a  class="menubutton" href="study_design.php?study_id={$study_data->study_id}">
Edit study design
</a></p>

Current stratification
<table>
<tr>
<th>Stratification name</th>
<th>Weight</th>
<th>Values</th>
</tr>

{foreach $stratification as $stratification_name => $options}
<tr>
<td>
{$stratification_name}
</td>
<td>
{$options.weight}
</td>
<td>
{foreach $options.values as $i => $text}
	{$i}: {$text}<br />
{/foreach}
</td>
</tr>
{/foreach}
</table>

<h2>Inclusions per strata</h2>

<h2>Simulation</h2>
<p><a href="simulation_edit.php?study_id={$study_data->study_id}">New simulation</a></p>

{foreach $simulations as $sim_id => $simulation}

<p><b>{$simulation->sim_name}</b></p>
<div style="padding-left:50px">
<a href="simulation_edit.php?sim_id={$sim_id}">Edit parameters</a> - 
<a href="simulation_run.php?sim_id={$sim_id}">Run</a></p>
{if $simulation->sim_run_date}
<a href="simulation_results.php?sim_id={$sim_id}">View results</a></p>
{/if}
</div>
{/foreach}
<h2>Log</h2>
{$log}

{include file="overall_footer.tpl"}
