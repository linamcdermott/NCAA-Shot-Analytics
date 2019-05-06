<html>
    <head>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <title>BA$KET$</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

<body>
<!-- PHP HEADER -->

<h1 style="position:relative;left:20px;"><span>Ba$ket$</span></h1>
 <p class="header">Welcome to our shot database!</p>
 <!-- <p class="right-align">Welcome to our shot database 000</p> -->
 <p class="header">Total number of shots</p>
 <p class="header">Total number of makes</p>
 <p class="header">Total number of misses</p>
 

 <a style="position:relative;left:20px;" href="https://espn.com"> Here is a cool button selection </a>

<br>
<br>


<!-- STATS -->
<br>
<br>
<!-- TODO - THERE HAS TO BE A BETTER WAY TO PAD RIGHT? -->



<!-- </body> -->


 <div class="container">
    <img src="/css/pictures/courtHalfBW.jpg">

    <?php
      require 'vendor/autoload.php';
      $conn = new MongoDB\Client('mongodb://localhost');
      $db = $conn->baskets;

      $team = $db->team;
      $shot = $db->shot;

      $m_state = $team->findOne(array('school' => 'CREIGHTON','season' => '2013-2014'));
      echo "<p> $m_state[school], $m_state[season]</p>";
      $shot_makes = $shot ->find(['team_id' => $m_state['_id'], 'player_name' => 'DOUG MCDERMOTT', made => true]);
      $shot_misses = $shot ->find(['team_id' => $m_state['_id'],'player_name' => 'DOUG MCDERMOTT', made => false]);
      // $make_count = count($shot_makes);
      // echo "<p> $make_count </p>";
      $makes = 0;
      $misses = 0;
      $points = 0;
      $assists = 0;
      $LAMA = 0;
      foreach($shot_makes as $row){
        $right = $row[yloc];
        $left = $row[xloc] * 1.9;
        $makes++;
        $points += $row[points];
        if ($row[assist] != 'n/a'){
          $assists += 1;
        }
        if($row[LAMA] == true){
          $LAMA += 1;
        }
        echo "<span class = \"dot_make\" style= \"position:absolute;right:$right%;bottom:$left%;\"> </span>";
      }
      $count2 = 0;
      foreach($shot_misses as $row){
        $right = $row[yloc];
        $left = $row[xloc] * 1.9;
        $misses++;
        if($row[LAMA] == true){
          $LAMA += 1;
        }
        echo "<span class = \"dot_miss\" style= \"position:absolute;right:$right%;bottom:$left%;\"> </span>";
      }
      $FG = round($makes / ($makes + $misses) * 100,1);
      $PPS = round($points / ($makes + $misses),3);
      $AST = round($assists / $makes * 100,1);
      $lp = round($LAMA / ($makes + $misses) * 100,1);

 echo"   
 </div>
 <table id=\"statsTable\">
  <tr>
    <td class=\"statTableTopRow\">$FG%</td>
    <td class=\"statTableTopRow\">$PPS</td>
    <td class=\"statTableTopRow\">$AST%</td>
    <td class=\"statTableTopRow\">$lp%</td>
  </tr>
  <tr class=\"statsTableBottomRow\">
    <td>FG%</td>
    <td>PPS</td>
    <td>AST%</td>
    <td>LAMA%</td>
  </tr>";
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
  <button>Home</button>
  <button>Away</button>
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
</ol>

<script>
$('button').click(function() {
    $(this).toggleClass("active");
    alert("clicked/unclicked");
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
function dropdownSelect() {
  var e = document.getElementById("teams-menu");
  var text = e.options[e.selectedIndex].text;
  alert(text); // do anything once selected (probably run a query)
}


</script>




</body>

