<!DOCTYPE html>
<html>
<head>
<link href="style.css" rel="stylesheet" type="text/css"  />
<script src="http://code.jquery.com/jquery-latest.min.js"></script>
<script type="text/javascript" src="http://static.fusioncharts.com/code/latest/fusioncharts.js"></script>
<script src="http://static.fusioncharts.com/code/latest/fusioncharts.charts.js"></script>
<script src="http://static.fusioncharts.com/code/latest/themes/fusioncharts.theme.zune.js"></script>


<style>

.code-block-holder pre {
      max-height: 188px;  
      min-height: 188px; 
      overflow: auto;
      border: 1px solid #ccc;
      border-radius: 5px;
}


.tab-btn-holder {
	width: 100%;
	margin: 20px 0 0;
	border-bottom: 1px solid #dfe3e4;
	min-height: 30px;
}

.tab-btn-holder a {
	background-color: #fff;
	font-size: 14px;
	text-transform: uppercase;
	color: #006bb8;
	text-decoration: none;
	display: inline-block;
	*zoom:1; *display:inline;


}

.tab-btn-holder a.active {
	color: #858585;
    padding: 9px 10px 8px;
    border: 1px solid #dfe3e4;
    border-bottom: 1px solid #fff;
    margin-bottom: -1px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    position: relative;
    z-index: 300;
}

</style>

<title>NBA Player Progression</title>
<h1 id="title"> <center>NBA Player Progression</center> </h1>

</head>
<body>
<br>
<form id="search_form" method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
<select id="nbateamid" name="nbateamid">
<!-- 	<option value="">Select Team</option> -->
	
	<?php echo add_teams();?>
</select>
<select id="player" name="player">
	<option value="">Select Player</option>
	
</select>

<br><br>
<input type="submit" name="formSubmit" value="Submit">
</form>

<br>

<script>
$(document).ready(function()
{
	$('#nbateamid').ready(function(){
		var team_id = $(this).val();
		$.ajax({
			url: "player_all_drop.php",
			method:"POST",
			data:{team_id:team_id},
			dataType: "text",
			success:function(data){
				//alert(team_id);
				
				$('#player').html(data);
				
				
			}
		
		});
		
	});
	$('#nbateamid').change(function(){
		var team_id = $(this).val();
		$.ajax({
			url: (team_id != "All Teams") ? "player_drop.php" : "player_all_drop.php",
			method:"POST",
			data:{team_id:team_id},
			dataType: "text",
			success:function(data){
				//alert(team_id);
				//if()
				$('#player').html(data);
				
				
			}
		
		});
		
	});
});
</script>

<?php
function add_teams(){

$hostdb = "classmysql:3306";  // MySQl host
$userdb = "cs340_rameshv";    // MySQL username
$passdb = "6238";             // MySQL password
$namedb = "cs340_rameshv";    // MySQL database name

// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

$sql = "SELECT TeamName FROM team";
$set_teams = $dbhandle -> query($sql);
$teams_array = array();
	$all_players = 0;

	//echo "<select name='team' id='selected_team'>";
	while($entry = $set_teams -> fetch_assoc()){
		if($all_players == 0){
			$output .= "<option value = 'All Teams'> All Teams </option>";
			$all_players = 1;
		}
		$output .= "<option value ='" . $entry["TeamName"] . "'>" . $entry['TeamName'] . "</option>";
	}
	//echo "</select>";
	return $output;
}
?>

<?php
// Including the wrapper file in the page
include("fusioncharts.php");

$hostdb = "classmysql:3306";  // MySQl host
$userdb = "cs340_rameshv";  // MySQL username
$passdb = "6238";  // MySQL password
$namedb = "cs340_rameshv";  // MySQL database name

// Establish a connection to the database
$dbhandle = new mysqli($hostdb, $userdb, $passdb, $namedb);

/*Render an error message, to avoid abrupt failure, if the database connection parameters are incorrect */
if ($dbhandle->connect_error) {
  exit("There was an error with your connection: ".$dbhandle->connect_error);
}

if(isset($_POST['formSubmit']))
{
  /*Set name for caption*/
  $strQuery = "SELECT * FROM player WHERE playerid = ?";
  $result = $dbhandle -> prepare($strQuery);
  $result -> bind_param("s",$_POST["player"]);
  $result -> execute();
  $result = $result -> get_result();
  while($entry = $result -> fetch_assoc()){
		$selected_first_name = $entry["FirstName"];
		$selected_last_name = $entry["LastName"];
  }
  $full_name = $selected_first_name . ' ' . $selected_last_name;
}

$strQuery = "SELECT * FROM statistics WHERE nba_player = '".$_POST["player"]."'ORDER BY year" ;
//$result = $dbhandle -> prepare($strQuery);
//$result -> bind_param("s",$_POST["player"]);
//$result -> execute();
//$result = $result -> get_result();
 $result = $dbhandle -> query($strQuery);

if ($result) {

  // initialize arrays to store stats
  $ppgArray=array();
  $apgArray=array();
  $rpgArray=array();
  $fgArray=array();
  $tovArray=array();
  $ftArray=array();
  $bpgArray=array();
  $tpArray=array();
  $perArray=array();
  $tsArray=array();
  $gpArray=array();
  $yearArray=array();

  $colorRange = initColorRange();

  $start = True;
  $barColor;
  $arrayVal = array();
  $i = 0;

  while($row = mysqli_fetch_array($result)) {
    // Collect all data
    if($start){
      $barColor = $colorRange[4];
      array_push($ppgArray, array("value" => $row["PPG"], "color"=>$barColor));
      array_push($apgArray, array("value" => $row["APG"], "color"=>$barColor));
      array_push($rpgArray, array("value" => $row["RPG"], "color"=>$barColor));
      array_push($fgArray, array("value" => $row["FG%"], "color"=>$barColor));
      array_push($tovArray, array("value" => $row["TOV"], "color"=>$barColor));
      array_push($ftArray, array("value" => $row["FT%"], "color"=>$barColor));
      array_push($bpgArray, array("value" => $row["BPG"], "color"=>$barColor));
      array_push($tpArray, array("value" => $row["3P%"], "color"=>$barColor));
      array_push($perArray, array("value" => $row["PER"], "color"=>$barColor));
      array_push($tsArray, array("value" => $row["TS%"], "color"=>$barColor));
      array_push($gpArray, array("value" => $row["GP"], "color"=>$barColor));
      array_push($yearArray, array("label" => $row["year"]));

      array_push($arrayVal, $row["PPG"]);
      array_push($arrayVal, $row["APG"]);
      array_push($arrayVal, $row["RPG"]);
      array_push($arrayVal, $row["FG%"]);
      array_push($arrayVal, $row["TOV"]);
      array_push($arrayVal, $row["FT%"]);
      array_push($arrayVal, $row["BPG"]);
      array_push($arrayVal, $row["3P%"]);
      array_push($arrayVal, $row["PER"]);
      array_push($arrayVal, $row["TS%"]);
      array_push($arrayVal, $row["GP"]);
      $start = False;
      continue;
    }
    else{
      $barColor = getColor($arrayVal[$i], $row["PPG"], $colorRange);
      array_push($ppgArray, array("value" => $row["PPG"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["APG"], $colorRange);
      array_push($apgArray, array("value" => $row["APG"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["RPG"], $colorRange);
      array_push($rpgArray, array("value" => $row["RPG"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["FG%"], $colorRange);
      array_push($fgArray, array("value" => $row["FG%"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["TOV"], $colorRange);
      $barColor = getOppositeColor($barColor, $colorRange);
      array_push($tovArray, array("value" => $row["TOV"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["FT%"], $colorRange);
      array_push($ftArray, array("value" => $row["FT%"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["BPG"], $colorRange);
      array_push($bpgArray, array("value" => $row["BPG"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["3P%"], $colorRange);
      array_push($tpArray, array("value" => $row["3P%"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["PER"], $colorRange);
      array_push($perArray, array("value" => $row["PER"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["TS%"], $colorRange);
      array_push($tsArray, array("value" => $row["TS%"], "color"=>$barColor));
      $i++;

      $barColor = getColor($arrayVal[$i], $row["GP"], $colorRange);
      array_push($gpArray, array("value" => $row["GP"], "color"=>$barColor));
      $i++;

      array_push($yearArray, array("label" => $row["year"]));
    }

    array_push($arrayVal, $row["PPG"]);
    array_push($arrayVal, $row["APG"]);
    array_push($arrayVal, $row["RPG"]);
    array_push($arrayVal, $row["FG%"]);
    array_push($arrayVal, $row["TOV"]);
    array_push($arrayVal, $row["FT%"]);
    array_push($arrayVal, $row["BPG"]);
    array_push($arrayVal, $row["3P%"]);
    array_push($arrayVal, $row["PER"]);
    array_push($arrayVal, $row["TS%"]);
    array_push($arrayVal, $row["GP"]);

  }

  // create charts
  $chartID = 0;
  generateChart($ppgArray, $yearArray, $full_name, "Points Per Game", "PPG", $chartID, "chart-ppg");
  generateChart($apgArray, $yearArray, $full_name, "Assist Per Game", "APG", $chartID+1, "chart-apg");
  generateChart($rpgArray, $yearArray, $full_name, "Rebounds Per Game", "RPG", $chartID+2, "chart-rpg");
  generateChart($fgArray, $yearArray, $full_name, "Field Goal Percentage Per Game", "FG%", $chartID+3, "chart-fg");
  generateChart($tovArray, $yearArray, $full_name, "Turnovers Per Game", "TOV", $chartID+4, "chart-tov");
  generateChart($ftArray, $yearArray, $full_name, "Free Throw Percentage Per Game", "FT%", $chartID+5, "chart-ft");
  generateChart($bpgArray, $yearArray, $full_name, "Blocks Per Game", "BPG", $chartID+6, "chart-bpg");
  generateChart($tpArray, $yearArray, $full_name, "Three Point Percentage Per Game", "3P%", $chartID+7, "chart-tp");
  generateChart($perArray, $yearArray, $full_name, "Player Efficiency Rating", "PER", $chartID+8, "chart-per");
  generateChart($tsArray, $yearArray, $full_name, "True Shooting Percentage Per Game", "TS%", $chartID+9, "chart-ts");
  generateChart($gpArray, $yearArray, $full_name, "Games Played Per Year", "GP", $chartID+10, "chart-gp");

  // closing db connection
  $dbhandle->close();
}

function getColor($prev, $cur, $colorRange){
  if($prev == 0)
    $prev = 0.1;
  $val = $cur / $prev;
  if($val <= 0.50)
    return $colorRange[0]; 
  else if($val > 0.5 && $val <= 0.65)
    return $colorRange[1];
  else if($val > 0.65 && $val <= 0.80)
    return $colorRange[2];
  else if($val > 0.80 && $val <= 0.95)
    return $colorRange[3];
  else if($val > 0.95 && $val <= 1.0)
    return $colorRange[4];
  else if($val > 1.0 && $val <= 1.15)
    return $colorRange[5];
  else if($val > 1.15 && $val <= 1.3)
    return $colorRange[6];
  else if($val > 1.3 && $val <= 1.45)
    return $colorRange[7];
  else if($val > 1.45)
    return $colorRange[8];
}

function getOppositeColor($barColor, $colorRange){
  $length = count($colorRange);
  for($i = 0; $i < $length; $i++){
    if($barColor == $colorRange[$i]){
      $opp = 8 - $i;
      return $colorRange[$opp];
    }
  }
}

function initColorRange(){
  $color = array(
    "#ff0003",  // RED
    "#ff3500",
    "#ff6f00",
    "#ffa800",
    "#ffe200",  // YELLOW
    "#e2ff00",
    "#a8ff00",
    "#6fff00",
    "#36ff00"   // GREEN
    );
  return $color;
}

function initChart($full_name, $caption, $genArray, $yearArray, $key){

	$arrData = array(
      "chart" => array(
      "caption"=> $full_name,
      "subCaption"=> $caption,
      "captionPadding"=> "15",
      "showvalues"=> "1",
      "valueFontColor"=> "#000000",
      "placevaluesInside"=> "1",
      "usePlotGradientColor"=> "0",
      "legendShadow"=> "0",
      "showXAxisLine"=> "1",
      "xAxisLineColor"=> "#999999",
      "xAxisname"=> "Year",
      "yAxisName"=> $key,
      "divlineColor"=> "#999999",
      "divLineIsDashed"=> "1",
      "showAlternateVGridColor"=> "0",
      "alignCaptionWithCanvas"=> "0",
      "legendPadding"=> "15",
      "showHoverEffect"=> "0",
      "theme"=> "fint"
    )

  );
    return $arrData;
}


function generateChart($genArray, $yearArray, $full_name, $title, $key, $id, $divID){
  $arrData = initChart($full_name, $title, $genArray, $yearArray, $key);
  $arrData["categories"]=array(array("category"=>$yearArray));
  $arrData["dataset"] = array(array("seriesName"=> $key, "data"=>$genArray));
  
  $jsonEncodedData = json_encode($arrData);
  

  // chart object
  $msChart = new FusionCharts("msbar2d", $id , "30%", "350px", $divID, "json", $jsonEncodedData);
  // Render the chart
  $msChart->render();
}

?>
<div class="box" id="chart-ppg"></div>
<div class="box" id="chart-apg"></div>
<div class="box" id="chart-rpg"></div>
<div class="box" id="chart-fg"></div>
<div class="box" id="chart-tov"></div>
<div class="box" id="chart-ft"></div>
<div class="box" id="chart-bpg"></div>
<div class="box" id="chart-tp"></div>
<div class="box" id="chart-per"></div>
<div class="box" id="chart-ts"></div>
<div class="box" id="chart-gp"></div>

</body>
</html>













