
window.addEventListener('load', function () {
    console.log('Cette fonction est exécutée une fois quand la page est chargée.');
  });

function sendCase (e) {

    console.log( "row="+e.dataset.row+"&col="+e.dataset.col );
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            var response = JSON.parse(this.responseText);
            
            e.innerHTML = response["motif"];
            document.getElementById("currentPlayer").innerHTML = response["currentPlayer"];

            if (response.winner !== false) {
              if (response.winner === "Tie") alert ("Tie");
              else alert("Félicitations "+response.winner[0]+"!\nVictoire en "+response.winner[1]+" "+response.winner[2]+"😉" );
                document.location.href="/";
            }
        }
    }
    xhr.open("POST", "/", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("row="+e.dataset.row+"&col="+e.dataset.col);
  };