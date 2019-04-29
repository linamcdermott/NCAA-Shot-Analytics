<html>
    <head>
        <title> BA$KET$</title>
        <link rel="stylesheet" href="css/style.css">
    </head>

<body>
 <h1>Ba$ket$ Supreme WHOOOOOO </h1>
 <p>Welcome to our shot database 000</p>
 <p>Total number of shots</p>
 <p>Total number of makes</p>
 <p class=misses>Total number of misses</p>
  
 <img class= "court" src="/css/pictures/courtHalfBW.jpg">


 
 <a href="https://espn.com"> Here is a cool button selection </a>

<br>
<br>


<!-- DROPDOWN MENU -->
 <select class="customSelect" onchange="dropdownSelect()">
  <option value="all" selected>All teams</option>
  <option value="tournament">Tournament Teams</option>
  <option value="non-tournament">Non-tournament teams</option>
  <option value="Davidson">Davidson</option>
</select>


<!-- STATS -->
<br>
<br>
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


</body>

<!-- DROPDOWN MENU https://stackoverflow.com/questions/1085801/get-selected-value-in-dropdown-list-using-javascript -->
<script>
function dropdownSelect() {
  var e = document.getElementById("teams-menu");
  var text = e.options[e.selectedIndex].text;
  alert(text); // do anything once selected (probably run a query)
}
</script>




<!-- <script> 
var pointsX = [300], pointsY = [450];
for(var i = 0; i < pointsX.length; i++){
    var div = document.createElement('div');
    div.className = 'dot';
    div.style.left = pointsX[i] + 'px';
    div.style.top = pointsY[i] + 'px';
    document.getElementById('wrapper').appendChild(div);
} 
</script> -->



</html>
