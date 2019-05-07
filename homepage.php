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
 <p>A shot analytics website.</p>
 <p> We scraped ESPN and created a database of NCAA Division I men's basketball shots from the past 
 six seasons. Our goal was to create a tool that could query the database and analyze the direction 
 of shot selection in college basketball. </p>
 <p>Has the analytics craze that has consumed the NBA affected college basketball? Has 
 shot selection diverted from the mid-range to the three-point line and the area close to the basket?</p>
 <p> <i> LM, AH, MW, AH <i> </p> 
 </div>

<br>
<br>




<!-- STATS -->
<br>
<br>
<!-- TODO - THERE HAS TO BE A BETTER WAY TO PAD RIGHT? -->



<!-- </body> -->


 <div class="container">
    <img src="/css/pictures/new_court.png">

    <?php
      require 'vendor/autoload.php';
      $conn = new MongoDB\Client('mongodb://localhost');
      $db = $conn->baskets;

      $team = $db->team;
      $shot = $db->shot;

      $current_season = '2018-2019';
      $current_team = 'DAVIDSON';

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


      $current_lama = $current_assist = $current_home = "both";


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
        } else {
          $current_home = test_input($_POST["home_radio"]);
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
      
      echo "<p> $current_season, $current_team</p>"; //print status in top left corner

      $makes = 0;
      $misses = 0;
      $points = 0;
      $assists = 0;
      $LAMA = 0;

      foreach($team_selection as $team){
        $shot_makes = $shot ->find(['team_id' => $team['_id'],  'made' => true, "assist" => ""]);
        $shot_misses = $shot ->find(['team_id' => $team['_id'], 'made' => false, "assist" => ""]);

        // $shot_makes = $shot ->find(['team_id' => $team['_id'],  'made' => true]);
        // $shot_misses = $shot ->find(['team_id' => $team['_id'], 'made' => false]);

        // if ($current_assist == "false") {
        //   $shot_makes = $shot_makes -> find(["assist" => "n/a"]);
        //   $shot_misses = $shot_misses -> find(["assist" => "n/a"]);
        // }

        echo "<p> $current_assist</p>"; //print status in top left corner

        
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


 <div class="selector">
<p style="font-weight:bold">Filter shot database:</p>

<?php 

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
  
  // <option value='2014-2015'>2014-2015</option>
  // <option value='2015-2016'>2015-2016</option>
  // <option value='2016-2017'>2016-2017</option>
  // <option value='2017-2018'>2017-2018</option>
  // <option value='2018-2019'>2018-2019</option>
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



function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>


<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">  

  <br>
 
  <br>
  <input type="radio" name="lama_radio" <?php if (isset($lama_radio) && $current_lama=="true") echo "checked";?> value="true">LAMA Shots
  <input type="radio" name="lama_radio" <?php if (isset($lama_radio) && $current_lama=="false") echo "checked";?> value="false">Non-LAMA Shots
  <input type="radio" name="lama_radio" <?php if (isset($lama_radio) && $current_lama=="both") echo "checked";?> value="both" checked>Both  
  <br><br>
  
  <br>
  <input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist=="assisted") echo "checked";?> value="assisted">Assisted Shots
  <input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist=="unassisted") echo "checked";?> value="unassisted">Unassisted Shots
  <input type="radio" name="assist_radio" <?php if (isset($assist_radio) && $current_assist=="both") echo "checked";?> value="both" checked>Both  
  <br><br>
  
  <br>
  <input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home=="true") echo "checked";?> value="true">Home Games
  <input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home=="false") echo "checked";?> value="false">Away Games
  <input type="radio" name="home_radio" <?php if (isset($home_radio) && $current_home=="both") echo "checked";?> value="both" checked>Both  
  

  <br><br>
  <input type="submit" name="submit" value="PLOT" class="plot">  
</form>

<?php
  echo "<h2>Your Input:</h2>";

  echo $current_lama;
  echo "<br>";
  echo $current_assist;
  echo "<br>";
  echo $current_home;
?>




<!-- BUTTONS -->
<ol>
  <button>Home Games</button>
  <button>Away Games</button>
  <br>
  <br>
  <button>Assisted Shots</button>
  <button>Unassisted Shots</button>
  <br>
  <br>
  <button>LAMA Shots</button>
  <button>Non-Lama Shots</button>
  <br>
  <br>
  <button>Only Shots Taken by Future NBA Draft Picks</button>
  <br>
  <br>
  <br>
  <button class=plot>PLOT</button> 
  <button class=reset>RESET</button>
</ol>





<script>
$('button').click(function() {
    $(this).toggleClass("active");
    // alert("clicked/unclicked");
});
</script>
 </div>



</body>

