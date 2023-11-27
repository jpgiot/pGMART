{include file="overall_header.tpl"}

<h1>New inclusion for {$study->acronym}</h1>

<div class="roundbox">

<div style="width:500px">
<form method="POST" action="simulati.php">
<input type="hidden" id="study_id" name="study_id" value="{$study->study_id}">

{foreach $inputs as $name => $values}

Stratification {$name}

{foreach $values as $id => $text}
<input type="radio" id="stratification_{$name}" name="stratification_{$name}" value="{$id}">{$text}
{/foreach}

{/foreach}

<input type="submit" value="New inclusion using these parameters">
</form>


</div>


{include file="overall_footer.tpl"}
