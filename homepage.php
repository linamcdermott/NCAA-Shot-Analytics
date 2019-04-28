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

 <div class="dropdown">
  <button onclick="myFunction()" class="dropbtn">Dropdown</button>
  <div id="myDropdown" class="dropdown-content">
    <a href="#home">Home</a>
    <a href="#about">About</a>
    <a href="#contact">Contact</a>
  </div>
</div>
 
 <a href="https://espn.com"> Here is a cool button selection </a>

<br>
<br>
<br>
 <select id="teams-menu" onchange="dropdownSelect()">
  <option value="all" selected>All teams</option>
  <option value="tournament">Tournament Teams</option>
  <option value="non-tournament">Non-tournament teams</option>
  <option value="Davidson">Davidson</option>
</select>
</body>


<!-- DROPDOWN MENU https://stackoverflow.com/questions/1085801/get-selected-value-in-dropdown-list-using-javascript -->
<script>
function dropdownSelect() {
  var e = document.getElementById("teams-menu");
  var text = e.options[e.selectedIndex].text;
  alert(text); // do anything once selected (probably run a query)
}

</script>



<script> 
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
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

<span class="dot"></span>


</html>
