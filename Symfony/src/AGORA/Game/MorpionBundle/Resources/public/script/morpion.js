function redirect() {
    for (let i = 1; i <= 9; i++) {
        document.getElementById("Case" + i).onclick = function () {
            document.location.href = "/joingame";
        };
    };
};


//On enlève le http (ou https)
urlWS = urlSock.split("/");
urlWS = urlWS[2];

//On enlève le port
urlWS = urlWS.split(":");
urlWS = urlWS[0];

console.log('ws://' + urlWS + ':8091');
var conn = new WebSocket('ws://' + urlWS + ':8091');


conn.onopen = function (e) {
    conn.send(JSON.stringify({
        type: 'connect',
        gameId: gameId,
        playerId: playerId
    }));
};

conn.onmessage = function (e) {
    var data = JSON.parse(e.data);

    if (data.action == "equality") {
        document.getElementById('currentPlayer').innerHTML = "EGALITE";
    } else if (data.action == "win") {
        if (data.votreTour) {
            document.getElementById('currentPlayer').innerHTML = "Vous Avez Gagné !";
        } else {
            document.getElementById('currentPlayer').innerHTML = "Vous Avez Perdu.";
        }
    } else if (data.action == "kick") {
        if (data.votreTour) {
            alert("C'est votre tour !");
        } else {
            alert("Il reste " + data.time + " secondes avant de pouvoir exclure le joueur");
        }
    } else if (data.action == "begin") {
        alert("La partie n'a pas encore commencé !");
    } else {
        if (data.votreTour) {
            document.getElementById('currentPlayer').innerHTML = "C'est votre tour !";
        } else {
            document.getElementById('currentPlayer').innerHTML = "C'est au tour de l'adversaire.";
        }
    }

    if (data.finished) {
        setTimeout(redirect(), 10000);
    }

    document.getElementById('playerSymbol').innerHTML = "Votre Symbole : " + data.playerSymbol;

    grille = data.grille.split(";");

    for (let i = 1; i <= 9; i++) {
        document.getElementById("Case" + i).innerHTML = grille[i - 1];
    }

    document.getElementById("vs").innerHTML = "J1 : " + data.player1 + " vs J2 : " + data.player2;
};

for (let i = 1; i <= 9; i++) {
    document.getElementById("Case" + i).onclick = function () {
        conn.send(JSON.stringify({
            type: 'jouer',
            case: (i - 1)
        }));
    };
}

function kick() {
    conn.send(JSON.stringify({
        type: 'kick',
        gameId: gameId,
        playerId: playerId
    }));
}