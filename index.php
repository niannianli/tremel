<?php 
include_once("constants.php");

//
// Clear up all cookies
//
setcookie("cookie[keyword1]",    "", time()-3600);
setcookie("cookie[searchType1]", "", time()-3600);
setcookie("cookie[keyword2]",    "", time()-3600);
setcookie("cookie[searchType2]", "", time()-3600);
setcookie("cookie[keyword3]",    "", time()-3600);
setcookie("cookie[searchType3]", "", time()-3600);
?>
<html>
<head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link href="./style.css" rel="stylesheet" type="text/css" media="screen"/>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="./lib/jquery-1.9.1.js"></script>
	<script src="./lib/jquery-ui.js"></script>
	<link href="./img/paw.gif" rel="SHORTCUT ICON"/>
	<title>TREMEL</title>
	<script>$(document).ready(function(){$("input").blur(function(){;});});</script>
	<script>$(document).ready(function()
	{
		// by default, entity 2 and entity 3 are hidden
		$(".panel2").hide(); $(".panel3").hide();
		
		// at the initial time only one search box
		// n is the number of the search keywords
		var n = 1;
        $(".flip").click(function()
		{
			// if only entity 1 is shown
			if(n == 1)
			{
				$(".submit").fadeOut(-1000);
				$(".panel2").fadeIn(1000);// show entity 2
				$(".submit").fadeIn(1000);
				n = 2; // now there are entity 1 and entity 2
			}
			// if entity 1 and entity 2 are shown
			else if(n == 2)
			{
				$(".submit").fadeOut(-1000);
				$(".panel3").fadeIn(1000); // shown entity 3
				$(".submit").fadeIn(1000);
				n = 3; // now there are 3
				
				// change the icon to be the hide icon
				var imgNameIndex = add.src.lastIndexOf("/") + 1;
				var imgName = add.src.substr(imgNameIndex);
				add.src="./img/hide.gif";
			}
			// if there are three, only can hide
			else if (n == 3)
			{
				$(".submit").fadeOut();
				$(".panel2").fadeOut(500);
				$(".panel3").fadeOut(500);
				$(".submit").fadeIn(1000);
				n = 1; // now there is only entity 1
				// the show icon should be displayed
				var imgNameIndex = add.src.lastIndexOf("/") + 1;
				var imgName = add.src.substr(imgNameIndex);
				add.src="./img/show.gif";
			}
		});
	});
    </script>
</head>
<body>
	<div id="tremelhdr">
		<div style="padding: 0 0 10 0">
		<a href=<?php $baseURL=BASE_URL; echo $baseURL;?> style="height: auto; float:right; margin-top: 5;">
			<img style="margin-left: 15; margin-right: 15;" src="./img/UMLogo280.gif" width="300" height="70" border="0px"></a>
		<span><a href=<?php $baseURL=BASE_URL; echo $baseURL;?>><img style="margin-top: 5; margin-bottom: 3;" src="./img/tremel_logo_CourierNew.gif" width="177" height="33" border="0px"></a><br>
			<span id="fontselect">
				Transcription
					  REgulatory
					  Modules 
					  Extracted from
					  Literature
					  <!--<b class="cap">(TREMEL)</b>-->
			</span>
		</span>
		</div>
		<!--<div id="div_line" style="height: 2;"></div>-->
		<div style="margin-top: 20; margin-bottom: 20;">
		<ul>
			<li>Click [<b>+</b>/<b>-</b>] button below to show/hide multiple search keywords.</li>
			<li>Chrome/Firefox are recommended.</li>
		</ul>
		</div>
		
		<div id="search">
        <form action="query.php" method="post" accept-charset="UTF-8">
		<table style="margin-top: 5; float:center; padding: 0em 0em 0em 2em; -webkit-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);-moz-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);text-shadow: 1px 1px 2px rgba(0,0,0,0.2);  color: black;">
		<tr><td><span style="margin-left: -13; margin-top: 20;"><img src="./img/show.gif" id="add" class="flip" style="cursor:pointer;"  width="16" height="15"/></span>
	       Entity 1: 
				<input type="text" name="keyword1" value=""/>
				<select name="searchType1">
		        <option value="gene">Gene</option>
		        <option value="TF">TF</option>
				<option value="term">Term</option>
		        </select>
				</td>
				<td rowspan="3">
		        <input class="submit" type="submit" name="op" value="TREMEL SEARCH"/>
				</td></tr>
		<tr><td class="panel2">
	       &nbsp;
	       Entity 2: <input type="text" name="keyword2" value=""/>
			    <select name="searchType2">
		        <option value="gene">Gene</option>
		        <option value="TF">TF</option>
				<option value="term">Term</option>
		        </select>
		 </td></tr>
		 <tr><td class="panel3">
	       &nbsp;
	       Entity 3: <input type="text" name="keyword3" value=""/>
			    <select name="searchType3">
		        <option value="gene">Gene</option>
		        <option value="TF">TF</option>
				<option value="term">Term</option>
		        </select>
		 </td></tr>
		</table>
	    </form>
        </div>
		<div style="height: 1; margin-top:-5;"></div><!-- make some space to look good -->
	</div>
	
	<div id="content" class="firstpage">
		
	</div>
	
	<div id="div_line" style="height: 2;"></div>
	<div class="footer"
		style="font-family: Open Sans, Lato, Courier New;
		font-size: 10pt;
		text-align: center;">
			Designed by Daqing Yun &copy; 2014 &middot;
			<a href="http://www.memphis.edu/binf/">Bioinformatics</a> &middot;
			<a href="http://www.memphis.edu/">University of Memphis</a> &middot;
			<a href="mailto:rhomayon@memphis.edu;sujoyroy@memphis.edu;dyun@memphis.edu;">Contact</a>
	</div>
</body>
</html>
