<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <title>BA$KET$</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

<body>
<!-- PHP HEADER -->


 <div class="description">
 <h1 style="position:relative;"><span>Ba$ket$</span></h1>
 <p>A basketball shot analytics website.</p>
 <p> We scraped ESPN and created a database of NCAA Division I men's basketball shots from the past 
 six seasons. Our goal was to create a tool that could query the database and analyze the direction 
 of shot selection in college basketball. </p>
 <p>Has the analytics craze that has consumed the NBA affected college basketball? Has 
 shot selection diverted from the mid-range to the three-point line and the area close to the basket?</p>
 <p> <i> LM, AH, MW, AH <i> </p> 
 </div>

<br>
<br>




<br>
<br>






<div class="selector">
<p style="font-weight:bold">Filter shot database:</p>

<?php 

require 'vendor/autoload.php';
$conn = new MongoDB\Client('mongodb://localhost');
$db = $conn->baskets;

$team = $db->team;
$shot = $db->shot;

$current_season = '2018-2019';
$current_team = 'DAVIDSON';
$current_lama = $current_assist = $current_home = $current_drafted = "both";

$all_teams = $team->find();
$teams = array();
foreach($all_teams as $t){
  if (in_array($t["school"], $teams) == false){
    
    array_push($teams,$t["school"]);

  }
  
}
sort($teams);


if(isset($_POST['submit'])){
  // As output of $_POST['Season'] is an array we have to use foreach Loop to display individual value
  foreach ($_POST['Season'] as $select)
  {
    $current_season = $select; // Displaying Selected Value
  }

  foreach ($_POST['teams'] as $select)
  {
    $current_team = $select; // Displaying Selected Value
  }
}

if($current_team == 'ALL TEAMS'){
  $team_selection = $team->find(array('season' => $current_season));
}
elseif ($current_team == "TOURNAMENT TEAMS"){
  $team_selection = $team->find(array('tournament' => true, 'season' => $current_season));
}
elseif ($current_team == "NON-TOURNAMENT TEAMS"){
  $team_selection = $team->find(array('tournament' => false,'season' => $current_season));
}
else{
  $team_selection = $team->find(array('school' => $current_team,'season' => $current_season));
}

$makes = 0;
$misses = 0;
$points = 0;
$assists = 0;
$LAMA = 0;

echo  "<form action='#' method='post'>
<select name='Season[]'  class = 'customSelect'>";

// Initializing Name With An Array
$all_years = array('2013-2014','2014-2015', '2015-2016','2016-2017', '2017-2018', '2018-2019');

foreach($all_years as $yr){
  if($current_season == $yr){
    echo "<option value=$yr selected>$yr</option>";
  }
  else{
    echo "<option value=$yr >$yr</option>";
  }
 
}

echo "</select> <br><br>";



echo "<form action=\"#\" method=\"post\">
<select name=\"teams[]\" class = \"customSelect\">
<option value=ALL TEAMS>ALL TEAMS</option>
<option value=TOURNAMENT TEAMS>TOURNAMENT TEAMS</option>
<option value=NON-TOURNAMENT TEAMS>NON-TOURNAMENT TEAMS</option>";
foreach($teams as $t){  
  if($t == $current_team){
    echo "<option value=$t selected>$t</option>";
  }
  else{
    echo "<option value=$t>$t</option>";
  }
 

}
echo "</select> <br><br>"; 




if ($_SERVER["REQUEST_METHOD"] == "POST") {

  if (empty($_POST["lama_radio"])) {
    $current_lama = "both";
  } else {
    $current_lama = test_input($_POST["lama_radio"]);
  }

  if (empty($_POST["assist_radio"])) {
    $current_assist = "both";
  } else {
    $current_assist = test_input($_POST["assist_radio"]);
  }

  if (empty($_POST["home_radio"])) {
    $current_home = "both";
    $current_home_string = $current_home;
  } else {
    $current_home = test_input($_POST["home_radio"]);
    $current_home_string = $current_home;
  }

  if (empty($_POST["drafted_radio"])) {
    $current_drafted = "both";
    $current_drafted_string = $current_drafted;
  } else {
    $current_drafted = test_input($_POST["drafted_radio"]);
    $current_drafted_string = $current_drafted;
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>


<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

 
  <br>
  <label class="radio_group"><input type="radio" name="lama_radio" class="radio"<?php if (isset($lama_radio) && $current_lama==true) echo "checked";?> value=true>LAMA Shots<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="lama_radio" class="radio"<?php if (isset($lama_radio) && $current_lama==false) echo "checked";?> value=false>Non-LAMA Shots<span class="checkmark"></span></label >
  <label class="radio_group"><input type="radio" name="lama_radio" class="radio"<?php if (isset($lama_radio) && $current_lama=="both") echo "checked";?> value="both">Both  <span class="checkmark"></span></label >

  
  <br>
  <label class="radio_group"><input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist==true) echo "checked";?> value=true>Assisted Shots<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist==false) echo "checked";?> value=false>Non-Assisted Shots<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist=="both") echo "checked";?> value="both">Both <span class="checkmark"></span></label>
  
  <br>
  <label class="radio_group"><input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home==true) echo "checked";?> value=true>Home Games<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home==false) echo "checked";?> value=false>Away Games<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home=="both") echo "checked";?> value="both">Both  <span class="checkmark"></span></label>
  
  <br>
  <label class="radio_group"><input type="radio" name="drafted_radio" <?php if (isset($drafted_radio) && $current_drafted==true) echo "checked";?> value=true>Player Drafted to the NBA<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="drafted_radio" <?php if (isset($drafted_radio) && $current_drafted==false) echo "checked";?> value=false>Players Not Drafted<span class="checkmark"></span></label>
  <label class="radio_group"><input type="radio" name="drafted_radio" <?php if (isset($drafted_radio) && $current_drafted=="both") echo "checked";?> value="both">Both  <span class="checkmark"></span></label>
  

  <br><br>
  <input type="submit" name="submit" value="PLOT" class="plot"> 
</form>

<?php

  foreach($team_selection as $team){
    if($current_lama == "both"){
      $query_lama = array('$ne' => null);
    }
    elseif($current_lama == "false"){
      $query_lama = false;
    }
    else{
      $query_lama = true;
    }

    if($current_assist == "both"){
      $query_assist = array('$ne' => null);
    }
    elseif($current_assist== "false"){
      $query_assist = 'n/a';
    }
    else{
      $query_assist = array('$ne' => 'n/a');
    }

    if($current_home == "both"){
      $query_home = array('$ne' => null);
    }
    elseif($current_home == "false"){
      $query_home = false;
    }
    else{
      $query_home = true;
    }

    if($current_drafted == "both"){
      $query_drafted = array('$ne' => null);
    }
    elseif($current_drafted == "false"){
      $query_drafted = false;
    }
    else{
      $query_drafted = true;
    }
   
    if($current_assist == "both"){
      $query_assist = array('$ne' => null);
    }
    if($current_home == "both"){
      $current_home = array('$ne' => null);
    }
    if($current_drafted == "both"){
      $current_drafted = array('$ne' => null);
    }

?> 

</div>






<div class="container">
<img src="/css/pictures/new_court.png">

<?php
      
$shot_makes = $shot ->find(['team_id' => $team['_id'],  'made' => true, 'LAMA' => $query_lama, 'assist' => $query_assist, 'player_drafted' => $query_drafted, 'home' => $query_home]);
//$shot_makes = $shot ->find(['team_id' => $team['_id'],  'made' => true, 'LAMA' => $query_lama]);
$shot_misses = $shot ->find(['team_id' => $team['_id'],  'made' => false, 'LAMA' => $query_lama, 'assist' => $query_assist, 'player_drafted' => $query_drafted, 'home' => $query_home]);
//$make_count = count($shot_makes);
//$shot_misses = $shot ->find(['team_id' => $team['_id'],  'made' => false, 'LAMA' => $query_lama]);
// echo "<p> $make_count </p>";

echo "<p> $current_team $current_season </p>";
echo "<p> LAMA: $current_lama</p>";
echo "<p> AST: $current_assist</p>";
echo "<p> HOME: $current_home_string</p>";
echo "<p> DRAFTED PLAYERS: $current_drafted_string</p>";



foreach($shot_makes as $row){
  $right = $row['yloc'];
  $left = $row['xloc'] * 1.8;
  $makes++;
  $points += $row['points'];
  if ($row['assist'] != 'n/a'){
    $assists += 1;
  }
  if($row['LAMA'] == true){
    $LAMA += 1;
  }
  echo "<span class = \"dot_make\" style= \"position:absolute;right:$right%;bottom:$left%;\"> </span>";
}
$count2 = 0;
foreach($shot_misses as $row){
  $right = $row['yloc'];
  $left = $row['xloc'] * 1.8;
  $misses++;
  if($row['LAMA'] == true){
    $LAMA += 1;
  }
  echo "<span class = \"dot_miss\" style= \"position:absolute;right:$right%;bottom:$left%;\"> </span>";
}
}


$total = $makes + $misses;
if($total <= 0){
$FG = 0.00;
$PPS = 0.00;
$AST = 0.00;
$lp = 0.00;
}
else{
$FG = round($makes / ($total) * 100,1);
$PPS = round($points / ($total),3);
$AST = round($assists / $makes * 100,1);
$lp = round($LAMA / ($total) * 100,1);
}


echo"
<p style=\"position:absolute;top:100%;\"> Shots Plotted: $total</p>   
</div>
<table id=\"statsTable\">
<tr>
<td class=\"tooltip\">$FG%
<span class='tooltiptext'>Field Goal Percentage</span></td>
<td class=\"tooltip\">$PPS
<span class='tooltiptext'>Average points per shot</span></td>
<td class=\"tooltip\">$AST%
<span class='tooltiptext'>Percentage of assisted made shotes</span></td>
<td class=\"tooltip\">$lp%
<span class='tooltiptext'>Percentage of shots that occur either behind the threepoint line or right at the basket (layup, dunk, etc.)</span></td>
</tr>
<tr class=\"statsTableBottomRow\">
<td>FG%</td>
<td>PPS</td>
<td>AST%</td>
<td>LAMA%</td>
</tr> 
";
      
?>



</body>

