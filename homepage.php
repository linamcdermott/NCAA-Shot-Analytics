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


      if(isset($_POST['submit_teams'])){
        // As output of $_POST['Color'] is an array we have to use foreach Loop to display individual value
        foreach ($_POST['teams'] as $select)
        {
          $current_team = $select;
        }
      }
      
      if(isset($_POST['submit_years'])){
        // As output of $_POST['Season'] is an array we have to use foreach Loop to display individual value
        foreach ($_POST['Season'] as $select)
        {
          $current_season = $select;
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
      // echo "<p> $team_selection[school], $team_selection[season]</p>";
      // $school_print = find('season' => $current_season);
      echo "<p> $current_season, $current_team</p>";
      $makes = 0;
      $misses = 0;
      $points = 0;
      $assists = 0;
      $LAMA = 0;
      foreach($team_selection as $team){
        $shot_makes = $shot ->find(['team_id' => $team['_id'],  'made' => true]);
        $shot_misses = $shot ->find(['team_id' => $team['_id'], 'made' => false]);
        // $make_count = count($shot_makes);
        // echo "<p> $make_count </p>";
        
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
      $FG = round($makes / ($total) * 100,1);
      $PPS = round($points / ($total),3);
      $AST = round($assists / $makes * 100,1);
      $lp = round($LAMA / ($total) * 100,1);

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


<!-- BUTTONS -->

<form action="#" method="post">
<select name="Season[]"  class = "customSelect"> // Initializing Name With An Array
  <option value="2013-2014">2013-2014</option>
  <option value="2014-2015">2014-2015</option>
  <option value="2015-2016">2015-2016</option>
  <option value= "2016-2017">2016-2017</option>
  <option value="2017-2018">2017-2018</option>
  <option value="2018-2019"selected>2018-2019</option>
</select>
<input type="submit" name="submit_years" value="Get Selected Value" />
</form>


<?php 
echo "<form action=\"#\" method=\"post\">
<select name=\"teams[]\" class = \"customSelect\">
<option value=ALL TEAMS>ALL TEAMS</option>
<option value=TOURNAMENT TEAMS>TOURNAMENT TEAMS</option>
<option value=NON-TOURNAMENT TEAMS>NON-TOURNAMENT TEAMS</option>";
foreach($teams as $t){  
  echo "<option value=$t>$t</option>";

}
echo "</select>
<input type=\"submit\" name=\"submit_teams\" value=\"Get Selected Value\" />
</form>"; ?>


<br>


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

