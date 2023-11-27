{include file="overall_header.tpl"}

<p>Welcome to pGMART, the PHP implementation of PHP Generalized Method for Adaptive Randomization in Trials.</p>

<p><a class="menubutton" href="study_new.php">Create a new study</a></p>

<p>Choose a study :<p>
<ul>
{foreach $studies as $thestudy_id => $thestudy}
<li>
<a class="menubutton" href="study_view.php?study_id={$thestudy_id}">
{$thestudy->study_acronym} {$thestudy->study_name}
</a>
</li>
{/foreach}


{include file="overall_footer.tpl"}
