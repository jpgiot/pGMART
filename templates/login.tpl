
{include file="overall_header.tpl"}
<style type="text/css">
{literal}
html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}

a {
	text-decoration: none;
}
::selection {
 	color: #fff;
 	text-shadow: none;
 	background: #444;
}

::-moz-selection {
 	color: #fff;
 	text-shadow: none;
 	background: #444;
}

/* Basics */

html, body {
	width: 100%;
	height: 100%;
	font-family: "Helvetica Neue", Helvetica, sans-serif;
	color: #444;
	-webkit-font-smoothing: antialiased;
	background: #f0f0f0;
	
}

#container {
	position: fixed;
	width: 340px;
	height: 340px;
	top: 50%;
	left: 50%;
	margin-top: -140px;
	margin-left: -170px;
	background: #fff;
	border-radius: 3px;
	border: 1px solid #ccc;
	box-shadow: 0 1px 2px rgba(0, 0, 0, .1);
	-webkit-animation-name: bounceIn;
	-webkit-animation-fill-mode: both;
	-webkit-animation-duration: 1s;
	-webkit-animation-iteration-count: 1;
	-webkit-animation-timing-function: linear;
	-moz-animation-name: bounceIn;
	-moz-animation-fill-mode: both;
	-moz-animation-duration: 1s;
	-moz-animation-iteration-count: 1;
	-moz-animation-timing-function: linear;
	animation-name: bounceIn;
	animation-fill-mode: both;
	animation-duration: 1s;
	animation-iteration-count: 1;
	animation-timing-function: linear;
}

form {
	margin: 0 auto;
	margin-top: 20px;
}

label {
	color: #555;
	display: inline-block;
	margin-left: 18px;
	padding-top: 10px;
	font-size: 14px;
}

p a {
	font-size: 11px;
	color: #aaa;
	float: right;
	margin-top: -13px;
	margin-right: 20px;
	-webkit-transition: all .4s ease;
	-moz-transition: all .4s ease;
	transition: all .4s ease;
}

p a:hover {
	color: #555;
}



#lower {
	background: #ecf2f5;
	width: 100%;
	height: 69px;
	margin-top: 20px;
	box-shadow: inset 0 1px 1px #fff;
	border-top: 1px solid #ccc;
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
}

input[type=checkbox] {
	margin-left: 20px;
	margin-top: 30px;
}

.check {
	margin-left: 3px;
	font-size: 11px;
	color: #444;
	text-shadow: 0 1px 0 #fff;
}



{/literal}
</style>

<div id="container">
	
	<form method="POST" action="login.php">
	
		<div style="height:60px">
			<img src="images/logo_pgmart.png" alt="pGMART logo" style="width:60px;height:60px;float:left"/>	
			<div style="display:table-cell;height:60px;vertical-align:middle;text-align:center">
			PHP Generalized Method for Adaptive Randomization in Trials
			</div>

		</div>
		
		<label for="username">Username:</label>
		
		<input type="text" id="username" name="username">
		
		<label for="password">Password:</label>
		
		<p><a href="#">Forgot your password?</a></p>
		
		<input type="password" id="password" name="password">
		
		<div id="lower">
		
			<!--<input type="checkbox"><label class="check" for="checkbox">Keep me logged in</label>-->
			
			<input type="submit" value="Login">
		
		</div><!--/ lower-->
	
	</form>
	
</div><!--/ container-->
		
{include file="overall_footer.tpl"}		