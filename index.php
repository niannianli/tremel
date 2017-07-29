<?php 
include_once("constants.php");

//
// Clear up all cookies
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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link href="./style.css" rel="stylesheet" type="text/css" media="screen"/>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="./lib/jquery-1.9.1.js"></script>
	<script src="./lib/jquery-ui.js"></script>
	<link href="./img/paw.gif" rel="SHORTCUT ICON"/>
	<title>TREMEL: Transcription REgulatory Modules Extracted from Literature</title>
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
				$(".panel2").fadeIn(1000);// show entity 2
				$(".submit").fadeIn(1000);
				n = 2; // now there are entity 1 and entity 2
			}
			// if entity 1 and entity 2 are shown
			else if(n == 2)
			{
				$(".panel3").fadeIn(1000); // shown entity 3
				$(".submit").fadeIn(1000);
				n = 3; // now there are 3
				
				// change the icon to be the hide icon
				var imgNameIndex = add.src.lastIndexOf("/") + 1;
				var imgName = add.src.substr(imgNameIndex);
				add.src="./img/minus.png";
			}
			// if there are three, only can hide
			else if (n == 3)
			{
				$(".panel2").fadeOut(500);
				$(".panel3").fadeOut(500);
				$(".submit").fadeIn(1000);
				n = 1; // now there is only entity 1
				// the show icon should be displayed
				var imgNameIndex = add.src.lastIndexOf("/") + 1;
				var imgName = add.src.substr(imgNameIndex);
				add.src="./img/add.png";
			}
		});
	});
    </script>
</head>
<body>
	<div style="height: 1em;"></div>
	<div id="tremelhdr">
		<a href="http://www.memphis.edu/" style="float:right;">
			<img src="./img/UMLogo280.gif" width="150" height="45" border="0px"></a>
		<a href=<?php $baseURL=BASE_URL; echo $baseURL;?>>
			<img src="./img/tremel_logo_CourierNew.gif" width="200" height="45" border="0px"></a>
			<br>
			<span id="tremelhdr_font">Transcription REgulatory Modules Extracted from Literature</span>
	</div>
	
	<!--<div id="navbar">
		<a href=<?php $homeURL=BASE_URL . "index.php"; echo $homeURL;?>>Home</a>
		<a href=<?php $aboutURL=BASE_URL . "about.php"; echo $aboutURL;?>>About</a>
		<a href=<?php $docURL=BASE_URL . "doc.php"; echo $docURL;?>>Documentation</a>
		<a href=<?php $helpURL=BASE_URL . "help.php"; echo $helpURL;?>>Help/FAQ</a>
		<a href=<?php $contactURL=BASE_URL . "contact.php"; echo $contactURL;?>>Contact</a>
	</div>-->
	
	<div id="searchbar">
        <form action="query.php" method="post" accept-charset="UTF-8">
		<table border = "0" id="searchbar_table">
			<tr><td id="td_add_hide"><img src="./img/add.png" id="add" class="flip" style="cursor:pointer;"  width="25" height="25" data-toggle="tooltip" data-placement="bottom" title="Click [+/-] button to show/hide multiple search fields"/></td>
				<td id="td_search_input">
				Entity 1:
					<input class="input_text" type="text" name="keyword1" value="" data-toggle="tooltip" data-placement="bottom" title="Enter official Gene Symbol or keyword"/>
					<select name="searchType1" data-toggle="tooltip" data-placement="bottom" title="Select entity type: Gene, TF or term">
						<option value="gene">Gene</option>
						<option value="TF">TF</option>
						<option value="term">Term</option>
					</select></td>
				<td><input class="submit" type="submit" name="op" value="Tremel Search" data-toggle="tooltip" data-placement="bottom" title="Click to submit query"/></td>
				<td id="td_help_hover"><img src="./img/question.png" width="25" height="25" data-toggle="modal" data-target="#myModal" data-toggle="tooltip" data-placement="bottom" title="display help information"/></td></tr>
					
		<tr><td class="panel2"></td><td id="td_search_input" class="panel2">
	       Entity 2: <input class="input_text" type="text" name="keyword2" value="" data-toggle="tooltip" data-placement="bottom" title="Enter official Gene Symbol or keyword"/>
			    <select name="searchType2" data-toggle="tooltip" data-placement="bottom" title="Select entity type: Gene, TF or term">
		        <option value="gene">Gene</option>
		        <option value="TF">TF</option>
				<option value="term">Term</option>
		        </select>
		 </td><td class="panel2"></td></tr>
		 <tr><td class="panel3"></td><td id="td_search_input" class="panel3">
	       Entity 3: <input class="input_text" type="text" name="keyword3" value="" data-toggle="tooltip" data-placement="bottom" title="Enter official Gene Symbol or keyword"/>
			    <select name="searchType3" data-toggle="tooltip" data-placement="bottom" title="Select entity type: Gene, TF or term">
		        <option value="gene">Gene</option>
		        <option value="TF">TF</option>
				<option value="term">Term</option>
		        </select>
		 </td><td class="panel3"></td></tr>
		</table>
	    </form>
    </div>
	
	<!-- Modal -->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h3 class="modal-title">Tremel Help</h3>
				</div>
				<div class="modal-body">
					<p style="font-family: Arial; font-weight: bold;">Search:</p>
					<ol style="font-family: Georgia; font-size: 10pt;">
						<li>Select entity type from pulldown menu: Gene, TF, or Term.</li>
						<li>Enter gene/TF  symbol or any keyword.</li>
						<li>Click 'Tremel Search' to submit.</li>
						<li>Click [+/-] to add additional search fields.</li>
					</ol>
					<p style="font-family: Arial; font-weight: bold;">Visualization:</p>
					<ol style="font-family: Georgia; font-size: 10pt;">
						<li>Click on <span style="color:blue; font-weight: bold;">blue</span> point in the plot to display the terms, genes and TFs associated with that module.</li>
						<li>The selected dot is displayed in <span style="color:red; font-weight: bold;">red</span>. Other dots are colored based on the similarity to the selected module.</li>
						<li>Rotate the plot by left-click and dragging the mouse.</li>
						<li>Customize the plot using right-click menu options.</li>
					</ol>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	
	<div id="div_line" style="height: 1;"></div>
	
	<div id="content" class="firstpage">
	</div>
	
	<div id="div_line" style="height: 1;"></div>
	<div class="footer"
		style="font-family: Arial, Open Sans, Lato, Courier New;
		font-size: 9pt;
		text-align: center;">
			All Rights Reserved &copy; 2014-2017 &middot;
			<a href="http://www.memphis.edu/binf/">Bioinformatics</a> &middot;
			<a href="http://www.memphis.edu/">University of Memphis</a> &middot;
			<a href="mailto:rhomayon@memphis.edu;sujoyroy@memphis.edu;daqingyun@gmail.com;">Contact</a>
	</div>
</body>
</html>
