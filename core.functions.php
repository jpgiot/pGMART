<?php

function mydie($message)
{

    echo "<div style=\"\n
	border:2px solid red;\n
	border-radius:20px;\n
	background-color:ffd074;\n
	margin:20px;\n
	padding:20px;\n
	font: 2em arial,sans-serif;\n
	box-shadow: 5px 5px 10px #000;\n
	\">";
    echo "<table>\n";
    echo "<tr><td style=\"display:table-cell;
	width:40px;height:40px;
	vertical-align:middle;text-align:center;color:red;font-size:3em\">";
    echo "!";
    echo "</td><td style='vertical-align:center'>\n";
    echo $message;
    echo "\n</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</div>\n";
    die();
}

function sign($n){
	if ($n == 0) return 0;
	if ($n > 0) return 1;
	if ($n < 0) return -1;
}
 

// data structures
class study {
    public $study_id;
    public $study_acronym;
    public $study_name;
}
