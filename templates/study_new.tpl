{include file="overall_header.tpl"}



<div class="roundbox">

<div style="width:500px">
<form method="POST" action="study_new.php">


<b>Study Name</b><br />

<input type="text" id="study_name" name="study_name" value=""><br />

<b>Study Acronym</b><br />
<div style="padding-left:50px">Study Acronym is composed of 2 to 15 letters or numbers without ponctuations.</div>

<input type="text" id="study_acronym" name="study_acronym" value=""> <br />

<input type="submit" value="New study">
</form>


</div>


{include file="overall_footer.tpl"}
