{literal}

body{
margin:0px;
padding:0px;
}

.maincontainer{
width:500px;
margin-left:auto;
margin-top:50px;
margin-right:auto;
}

.title{
font-family:Arial;
font-size:2em;
color:black;
top:10px;
left:100px;
width:550px;
position:absolute;
}

.banner{
margin-top:0px;
margin-left:0px;
}

.banner a img{
border:0px;
}

.menu{
height:50px;
background: linear-gradient(rgb(255, 255, 255), rgb(234, 237, 241)) repeat scroll 0% 0% transparent;
background: -webkit-linear-gradient(rgb(255, 255, 255), rgb(234, 237, 241)) repeat scroll 0% 0% transparent;
}

.menu ul{
list-style:none;
padding:0px;
}

.menu ul li{
display:inline;
height:30px;
}

.menubutton{
margin:10px;
padding:5px;
border:1px solid transparent;
}

.menubutton:hover{
background-color:#fdeeb3;
border:1px solid #f1c43f;
border-radius:2px;
}

.menubutton a{
text-decoration:none;
font-family: "Segoe UI",Verdana,Tahoma,Helvetica,sans-serif;
font-size:1em;
word-wrap: break-word;
}

.menubutton img{
border:0px;
width:30px;
height:30px;
}

.formbutton{
background-color:#dedede;
border:1px solid #898989;
font-family: "Segoe UI",Verdana,Tahoma,Helvetica,sans-serif;
border-radius:2px;
height:3em;
}

.formbutton:hover{
box-shadow: 0px 3px 6px #6f6e6e;
}

.date_container{
width:48px;
height:48px;
margin:auto;
}

.date_container .monthyear{
background-color:#330099;
text-align: center;
font:0.5em arial;
color:white;
border-left:1px solid #999999;
border-right:1px solid #999999;
border-top:1px solid #999999;
border-top-left-radius: 5px;
border-top-right-radius: 5px;
}

.date_container .day{
background-color:#EFEFEF;
text-align: center;
font:1em arial;
border-left:1px solid #999999;
border-right:1px solid #999999;
}

.date_container .hour{
background-color:#CCCCCC;
text-align: center;
font:0.6em arial;
color:black;
border-left:1px solid #999999;
border-right:1px solid #999999;
border-bottom:1px solid #999999;
border-bottom-left-radius: 5px;
border-bottom-right-radius: 5px;
}


input[type=submit] {
	float: right;
	margin-right: 20px;
	margin-top: 20px;
	height: 30px;
	font-size: 14px;
	font-weight: bold;
	color: #fff;
	background-color: #acd6ef; /*IE fallback*/
	background-image: -webkit-gradient(linear, left top, left bottom, from(#acd6ef), to(#6ec2e8));
	background-image: -moz-linear-gradient(top left 90deg, #acd6ef 0%, #6ec2e8 100%);
	background-image: linear-gradient(top left 90deg, #acd6ef 0%, #6ec2e8 100%);
	border-radius: 30px;
	border: 1px solid #66add6;
	box-shadow: 0 1px 2px rgba(0, 0, 0, .3), inset 0 1px 0 rgba(255, 255, 255, .5);
	cursor: pointer;
}

input[type=submit]:hover {
	background-image: -webkit-gradient(linear, left top, left bottom, from(#b6e2ff), to(#6ec2e8));
	background-image: -moz-linear-gradient(top left 90deg, #b6e2ff 0%, #6ec2e8 100%);
	background-image: linear-gradient(top left 90deg, #b6e2ff 0%, #6ec2e8 100%);
}

input[type=submit]:active {
	background-image: -webkit-gradient(linear, left top, left bottom, from(#6ec2e8), to(#b6e2ff));
	background-image: -moz-linear-gradient(top left 90deg, #6ec2e8 0%, #b6e2ff 100%);
	background-image: linear-gradient(top left 90deg, #6ec2e8 0%, #b6e2ff 100%);
}

input {
	font-family: "Helvetica Neue", Helvetica, sans-serif;
	font-size: 12px;
	outline: none;
}

input[type=text],
input[type=password] {
	color: #777;
	padding-left: 10px;
	margin: 10px;
	margin-top: 12px;
	margin-left: 18px;
	width: 290px;
	height: 35px;
	border: 1px solid #c7d0d2;
	border-radius: 2px;
	box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .4), 0 0 0 5px #f5f7f8;
	-webkit-transition: all .4s ease;
	-moz-transition: all .4s ease;
	transition: all .4s ease;
}

input[type=text]:hover,
input[type=password]:hover {
	border: 1px solid #b6bfc0;
	box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .7), 0 0 0 5px #f5f7f8;
}

input[type=text]:focus,
input[type=password]:focus {
	border: 1px solid #a8c9e4;
	box-shadow: inset 0 1.5px 3px rgba(190, 190, 190, .4), 0 0 0 5px #e6f2f9;
}

.roundbox {
	margin: 20px;
	padding:15px;
	background-color: #E8E8FF; /*IE fallback*/
	background-image: -webkit-gradient(linear, left top, left bottom, from(#E8E8FF), to(#D7E1E5));
	background-image: -moz-linear-gradient(top left 90deg, #E8E8FF 0%, #D7E1E5 100%);
	background-image: linear-gradient(top left 90deg, #E8E8FF 0%, #D7E1E5 100%);
	border-radius: 20px;
	border: 1px solid #66add6;
	box-shadow: 0 1px 2px rgba(0, 0, 0, .3), inset 0 1px 0 rgba(255, 255, 255, .5);
	cursor: pointer;
}

.study_acronym{
	font-family: "Segoe UI",Verdana,Tahoma,Helvetica,sans-serif;
	font-size:1.8em;
	text-transform:uppercase;
}

.table_lines {
border:1px solid black;border-collapse:collapse
}
.table_lines tr {
border:1px solid black;
}

{/literal}