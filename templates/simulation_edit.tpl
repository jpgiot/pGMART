{include file="overall_header.tpl"}

{if $new_simulation}
<h1>New simulation for {$study->acronym}</h1>
{else}
<h1>Edit simulation parameters for {$study->acronym}</h1>
{/if}

<div class="roundbox">

<div style="width:500px">
<form method="POST" action="simulation_edit.php">

{if $new_simulation}
<input type="hidden" id="study_id" name="study_id" value="{$study->current_study_id}">
{else}
<input type="hidden" id="sim_id" name="sim_id" value="{$simulation->current_sim_id}">
{/if}

<p><b>Simulation Name</b></p>

<input type="text" id="simulation_name" name="simulation_name" value="{$p.simulation_name}"><br />

<p><b>Aleatory patients per run</b></p>

<input type="text" id="simulation_patients" name="simulation_patients" value="{$p.simulation_patients}"><br />

<p><b>Runs</b></p>

<input type="text" id="simulation_runs" name="simulation_runs" value="{$p.simulation_runs}"><br />

<p><b>Inherit weights from parent study</b></p>

<input type="text" id="simulation_weights_inherit" name="simulation_weights_inherit" value="{$p.simulation_weights_inherit}">If value is 0 (number), following weights will be used<br />

<p><b>Weights</b></p>

<input type="text" id="simulation_overall_treatment_weight" name="simulation_overall_treatment_weight" value="{$p.simulation_overall_treatment_weight}">Overall<br />

{foreach $inputs as $name => $values}
{assign var="formvar" value="simulation_stratification_weight_{$name}"}
<input type="text" id="{$formvar}" name="{$formvar}" value="{$p.$formvar}">Stratification {$name}<br />
{/foreach}

<input type="text" id="simulation_stratum_weight" name="simulation_stratum_weight" value="{$p.simulation_stratum_weight}">Stratum<br />


<p><b>Input frequency</b></p>

<p>You must assume that all stratification variables are independent. Provide the frequency for each input value. For a stratifiction, the sum of all frequencies must be 1 (equaling 100%).</p>

{foreach $inputs as $name => $values}

<p><i>Stratification {$name}</i></p>


{foreach $values as $id => $text}
{assign var="formvar" value="simulation_stratification_{$name}_{$id}"}
<input type="text" id="{$formvar}" name="{$formvar}" value="{$p.$formvar}">{$text}<br />
{/foreach}

{/foreach}

<input type="submit" value="Update simulation parameters">
</form>


</div>


{include file="overall_footer.tpl"}
