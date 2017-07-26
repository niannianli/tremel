<?php
/*************************************************************************************
 * query.php - query results showing page
 * 
 * Functionality:
 * 		1. Get the keywords from the form,
 * 		2. Perform the search,
 * 		3. Render the results in a 3D chart.
 * 
 * Author:
 * 		Daqing Yun <dyun@memphis.edu> @ CS
 *
 * Created: Apr 25, 2013
 * Last updated: Aug 25, 2014
 * 
 * Change log:
 * 		Dec 28, 2013:
 * 			1. Re-import the cluster data for both stemmed/unstemmed.
 * 			2. Add another search option stemmed/unstemmed.
 * 			3. Update the UI, looks great now?
 * 
 * 		Jan 01, 2014:
 * 			1. Fix the bug that result records show not smoothly and
 * 			   fill in the content holder, currently the size of each
 * 			   recored adjusts the size of content in it.
 * 
 *      Apr 01, 2014 (for the paper submission):
 *          1. Change the word 'cluster' to 'module' everywhere
 *          2. Start numbering from 1 instead of 0
 *          3. More details, refer to TREMEL issues under the ./doc/
 *
 *      May 09, 2014 (after finals, for the paper submission):
 *          1. Please refer to TREMEL issues 2 ./doc/ directory
 *
 *		Aug 18, 2014 (before submission):
 *			1. Use Scatter 3D chart to present the results.
 *      
 *      Aug 20, 2014:
 *			After a little survey, I decided to use a JavaScript library 
 *          "canvasXpress", @ http://canvasxpress.org/
 *          An email address is required to download.
 *          This lib provide a Scatter 3D Chart to us;
 *          Unfortunately, the chart does not include the function for
 *          TREMEL to get the information of the selected data.
 *          I solved this by hacking into the source code of the library and
 *          adding the "getSelectedDataID()" function; so to deploy TREMEL,
 *          the file ./js/canvasXpress.min.js should not be overwritten.
 *          Please check out the latest code from the svn repository at:
 *          http://dragon.cs.memphis.edu/svn/tremel/
 *          
 *      Aug 22, 2014:
 *          By using a session, the performance (responding time) is
 *          significantly improved.
 *
 *      Detailed change history can be found at ./doc directory.
 *
 * Copyright (c) 2013-2014 All Rights Reserved
 * Bioinformatics Program, The University of Memphis
 * 
 *************************************************************************************/

include_once("sql.php");
include_once("stem.php");
include_once("reorder.php");
include_once("constants.php");
include_once("util.php");

// Starts a session
session_start();

/*************************************************************************
 * Local variables definitions for references
 * 
 * $key1, $type1: keyword and its corresponding type;
 * $key2, $type2: same as above;
 * $key3, $type3: same as above;
 * $query_num: the search is based on how many keywords/types;
 * $raw_result_array: raw format data of the results polled from database;
 * $cpy_result_array: duplicate of rawResArray;
 * $num_records: number of records in the result array;
 * $num_pages: depends on the number of result records;
 * $max_rank: maximal rank in the searched results;
 * $baseid: based on who to calculate similarity;
 * $pageid;
 * $page_size;
 * $curr_page;
 * $selectid: the selected point in the chart;
 * $k_value: ;
 * $rank: ;
 * $mark: ;
 * $mod_num: ;
 * $expandable_content_list: ;
 * $core_data: actual numerical data of the scatter 3D chart;
 * $vars_data: ;
 * $chart_data: chart data which includes coreData;
 *************************************************************************/

//
// If there is new info input, replace that of corresponding in cookies,
// and then perform the search based on the updated information.
//
$query_num = 1; // by default

if($_POST['keyword1'])
{
	// Get the info
	$key1 = $_POST['keyword1'];
	$type1 = $_POST['searchType1'];

	// Set cookies
	setcookie("cookie[keyword1]", $key1);
	setcookie("cookie[searchType1]", $type1);
	
	// Clear others
	$key2 = "";
	$type2 = "";
	$key3 = "";
	$type3 = "";
}


if ($_POST['keyword2'])
{
	$query_num = 2;
	
    // Get the info
	$key2 = PorterStemmer::Stem($_POST['keyword2']);
	$type2 = $_POST['searchType2'];
	
	// Set cookies
	setcookie("cookie[keyword2]", $key2);
	setcookie("cookie[searchType2]", $type2);
	
	// Clear others
	$key3 = "";
	$type3 = "";
}

if ($_POST['keyword3'])
{
	$query_num = 3;
	
    // Get the info
	$key3 = PorterStemmer::Stem($_POST['keyword3']);
	$type3 = $_POST['searchType3'];
	
	// Set cookies
	setcookie("cookie[keyword3]", $key3);
	setcookie("cookie[searchType3]", $type3);
}

//
// If there is no new info (keywords) input, that means the page is
// re-loaded that is caused by user clicking refresh button i.e. point in
// figure or web browser
// !!! In this case, do we really need to get results from database again? use session
//
if(!$_POST['keyword1'] && !$_POST['keyword2'] && !$_POST['keyword3'])
{
	// Get cookies
	if(isset($_COOKIE['cookie'])){		
		$key1 = $_COOKIE['cookie']['keyword1'];
		$type1 = $_COOKIE['cookie']['searchType1'];
		
		$key2 = $_COOKIE['cookie']['keyword2'];
		$type2 = $_COOKIE['cookie']['searchType2'];
		
		$key3 = $_COOKIE['cookie']['keyword3'];
		$type3 = $_COOKIE['cookie']['searchType3'];
	}
	else{
		// Do nothing, the results will be empty.
		// JavaScript part will handle the notifications.
		;
	}
}

//
// Get ID of the selected point in the chart
//
$selectid = -1;
if(isset($_GET['q']))
{
	$selectid = intval($_GET['q']);
}
else
{
	$selectid = -1;
}

//
// Start a session for better performance (response time)
if($selectid < 0)
{
	// if it is a new search based on newly specified keyword
	// Fetch raw results
	// Function search_in_db() is defined in sql.php
	$raw_result_array = search_in_db($key1, $type1, $key2, $type2, $key3, $type3);
	$num_records  = count($raw_result_array);
	$_SESSION['raw'] = $raw_result_array;
}
else if(isset($_SESSION['raw']))
{
	// if it is a reloaded page caused by cliking the point in the chart
	// we get data from session without accessing the database
	$raw_result_array = $_SESSION['raw'];
	$num_records = count($raw_result_array);
}
else
{
	//
	// Fetch raw results
	// Function search_in_db() is defined in sql.php
	//
	$raw_result_array = search_in_db($key1, $type1, $key2, $type2, $key3, $type3);
	$num_records  = count($raw_result_array);
}


//
// Calculate rank, save the maximal rank, as "maxRank"
//
$max_rank = -1;
for($i = 0; $i < $num_records; $i ++)
{
	$items = array_map('strtolower', explode(',', $raw_result_array[$i][$type1]));
	
	for($j = 0; $j < count($items); $j++)
	{
		if(0 == strcasecmp($key1, $items[$j]))
		{
			if($j+1 > $max_rank)
				$max_rank = $j+1;
			
			$raw_result_array[$i]['rank'] = $j + 1;
		}
	}
}

//
// Calculate the base ID that is with smallest k Value
//
$baseid = select_baseid($raw_result_array);

//
// Get page ID
//
if(isset($_GET['page']))
	$pageid = intval($_GET['page']);
else
	$pageid = 1;

$page_size = PAGE_SIZE;

//
// Compute the similarity based on the selected point's ID ($selectID)
//
if($selectid >= 0)
{
	$raw_result_array = calc_sim($raw_result_array, $selectid);
	$mark = $selectid;
}
else
{
	$raw_result_array = calc_sim($raw_result_array, $baseid);
	$mark = $baseid;
}


//
// Data format: 'data' : [[k, rank, mod#, sim], [k, rank, mod#, sim], ...]
$vars_data = "'vars' : [";
$core_data = "'data' : [";
for($i = 0; $i < $num_records; $i ++)
{
	$k_value    = intval($raw_result_array[$i]['k']);
	$rank       = intval($raw_result_array[$i]['rank']);	
	$mod_num    = intval($raw_result_array[$i]['module']);
	$sim        = $raw_result_array[$i]['sim'];	
	$core_data  = $core_data . "[" . $k_value . "," . $rank. "," .$mod_num. "," .$sim. "]";
	$vars_data  = $vars_data . "'" . $i . "'"; 
	if($i < $num_records-1)
	{
		$core_data = $core_data . ",";
		$vars_data = $vars_data . ",";
	}
}
$core_data = $core_data . "]";
$vars_data = $vars_data . "]";

//
// Calculate number of pages
// according to the total number of searched result records
//
if($num_records)
{
	if($num_records < $page_size)
		$num_pages = 1;
	else if($num_records % $page_size)
		$num_pages = (int)($num_records/$page_size)+1;
	else
		$num_pages = $num_records/$page_size;
}
else
	$num_pages = 0;

/**
 * Construct page number display string
 */
$curr_pageid = $pageid;
$go2_pgstr = '';
$go2_pgstr = get_go2_pgstr($selectid, $num_pages, $curr_pageid);
?>

<html>
  <head>
	<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
	<link href="./style.css" rel="stylesheet" type="text/css" media="screen"/>
	<link href='http://fonts.googleapis.com/css?family=Lato:300,400' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<link rel="stylesheet" href="./lib/jquery-ui.css">
	<script src="./lib/jquery-1.9.1.js"></script>
	<script src="./lib/jquery-ui.js"></script>
	<script src="./lib/canvasXpress.hacked.min.js"></script>
	<link href="./img/paw.gif" rel="ICON"/>
	<title>TREMEL</title>
	<script>$(function(){$( "#expandList" ).accordion({heightStyle: "content"});});</script>
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
	<script type="text/javascript">
		var showScat3DCht = function (){
			var num = <?php echo $num_records ?>;
			if(num <= 0){
				document.getElementById('ErrorMessage').innerHTML='No records found based on the provided information.';
				return;
			}
			var pp = <?php echo $pageid; ?>;
			var cht = new CanvasXpress('chtPos', {
				'y':{
					<?php echo $vars_data; ?>,
					'smps' : ['\'k\'', 'rank', 'mod#', 'Similarity'],
					<?php echo $core_data; ?>
					},},
				{'colorBy': 'Similarity',
				 'graphType': 'Scatter3D',
				 'xAxis': ['Sample1'],
				 'yAxis': ['Sample2'],
				 'zAxis': ['Sample3'],
				 'codeType' : 'pretty',
				 'selectDataMode' : 'name'},
				{
					'click' : function(o, e, t) {
						var id = cht.getSelectedDataID();
						var link = "./query.php?page="+pp+"&q="+id;
						window.location.assign(link);
					},
				}
			);
		}
	</script>
</head>
  
  <body onload="showScat3DCht();">
    <div id="tremelhdr">
		<div style="padding: 0 0 10 0">
		<a href=<?php $baseURL=BASE_URL; echo $baseURL;?> style="height: auto; float:right; margin-top: 5;">
			<img style="margin-left: 15; margin-right: 15;" src="./img/UMLogo280.gif" width="300" height="70" border="0px"></a>
		<span><a href=<?php $baseURL=BASE_URL; echo $baseURL;?>>
			<img style="margin-top: 5; margin-bottom: 3;" src="./img/tremel_logo_CourierNew.gif" width="177" height="33" border="0px"></a><br>
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
		<div style="margin-top: 20; margin-bottom: 20">
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
	
	<div id="ErrorMessage"
		style="font-family: 'Lato', Times;
			   text-align: center;
			   color: black;
			   -webkit-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
			   -moz-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
			   text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
			   color: black;">
	</div>
	
    <div style="font-family: 'Lato', Times;
			    text-align: center;
			    color: black;
			    -webkit-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
			    -moz-text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
			    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
            Total <?php echo "<b>".$num_records."</b>"; ?> results.
            Search Query:  <?php echo "<b>" . $key1 ."</b> {". $type1 . "}"; ?>
			<?php
				if($queryNum == 2){
					echo " AND <b>" . $key2 . "</b> {" . $type2 ."}";
				}
				elseif($queryNum == 3){
					echo " AND <b>" . $key2 . "</b> {" . $type2 ."} AND <b>" . $key3 . "</b> {" . $type3 . "}";
				}
			?>
    </div>
	
	<?php if($num_records <= 0) echo "<div>"; else echo "<div id='threed'>"; ?>
	<p style="text-align: center; margin-top: 0; margin-bottom: 0">
		<canvas id='chtPos' width='590' height='590'>
			Your browser does not support the HTML5 canvas tag.
		</canvas>
	</p>
	</div>
	
	<?php if($num_records <= 0) echo "<div>"; else echo "<div id='result'>"; ?>
    <div id="main">
		<!--<div id="chart_div" align="center" style="width: 1000px; height: 650px;"></div>-->
		<div id="expandList">
		<?php
		$cpy_result_array = $raw_result_array;
		$expandable_content_list = get_main_content_result_str($pageid, $page_size, $num_records, $cpy_result_array, $mark);
		echo $expandable_content_list;
		?>
		</div>
		<div style="font-family: Lato, Courier New; font-weight: bold; text-align: center;">
		<?php if($num_records <= 0) ; else echo $go2_pgstr; ?> </div>
    </div>
	</div>
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
