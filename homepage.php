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

      $team_selection = $team->findOne(array('school' => 'DUKE','season' => '2018-2019'));
      echo "<p> $team_selection[school], $team_selection[season]</p>";
      $shot_makes = $shot ->find(['team_id' => $team_selection['_id'],  'made' => true]);
      $shot_misses = $shot ->find(['team_id' => $team_selection['_id'], 'made' => false]);
      // $make_count = count($shot_makes);
      // echo "<p> $make_count </p>";
      $makes = 0;
      $misses = 0;
      $points = 0;
      $assists = 0;
      $LAMA = 0;
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

<br>

<!-- DROPDOWN MENU -->
<select class="customSelect" id="teams-menu" onchange="dropdownSelect()">
  <option value="all" selected>All teams</option>
  <option value="tournament">Tournament Teams</option>
  <option value="non-tournament">Non-tournament teams</option>
  <option value="Davidson">Davidson</option>
</select>

<select class="customSelect" id="years-menu" onchange="dropdownSelect()">
  <option value="2013-2014" >2013-2014</option>
  <option value="2014-2015">2014-2015</option>
  <option value="2015-2016">2015-2016</option>
  <option value="2016-201">2016-2017</option>
  <option value="2017-2018">2017-2018</option>
  <option value="2018-2019" selected>2018-2019</option>
</select>



<!-- YEAR RANGER SLIDER https://codepen.io/trevanhetzel/pen/rOVrGK -->
<!-- <input type="range" min="1" max="7" steps="1" value="1">
<ul class="range-labels">
  <li>2012-13</li>
  <li>2013-14</li>
  <li>2014-15</li>
  <li>2015-16</li>
  <li>2016-17</li>
  <li>2017-18</li>
  <li class="active selected">2018-19</li>
</ul> -->


<br>
<br>


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

<!-- <script>
$(function() {
  $('button').focus();
});
</script> -->


 </div>

 <!-- DROPDOWN MENU https://stackoverflow.com/questions/1085801/get-selected-value-in-dropdown-list-using-javascript -->
<script>
// function dropdownSelect() {
//   var e = document.getElementById("teams-menu");
//   var text = e.options[e.selectedIndex].text;
//   alert(text); // do anything once selected (probably run a query)
// }


</script>




</body>

