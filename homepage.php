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
<table id="statsTable">
  <tr>
    <td class="statTableTopRow">XX.X%</td>
    <td class="statTableTopRow">X.XXX</td>
    <td class="statTableTopRow">XX.X%</td>
    <td class="statTableTopRow">XX.X%</td>
  </tr>
  <tr class="statsTableBottomRow">
    <td>FG%</td>
    <td>PPS</td>
    <td>AST%</td>
    <td>LAMA%</td>
  </tr>


<!-- </body> -->


 <div class="container">
    <img src="/css/pictures/courtHalfBW.jpg">

    <?php
      require 'vendor/autoload.php';
      $conn = new MongoDB\Client('mongodb://localhost');
      $db = $conn->baskets;

      $team = $db->team;
      $shot = $db->shot;

      $MSTATE = $team->findOne(array('school' => 'MICHIGAN STATE'),array('season' => '2018-2019'));
      $shot_makes = $shot ->find(['team_id' => $MSTATE['_id'], 'player_name' => 'CASSIUS WINSTON', 'type' => 'THREE POINT JUMPER',]);
      // $make_count = count($shot_makes);
      // echo "<p> $make_count </p>";
      $count = 0;
      foreach($shot_makes as $row){
        $count ++;
        echo "<span class = \"dot\" style= \"position:absolute;right:$row[yloc]%;bottom:$row[xloc]%;\"> </span>";
      }
      echo"<p>$count</p>";
    ?>
 </div>



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
  <br>
  <br>
  <button class=plot>PLOT!</button>
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

