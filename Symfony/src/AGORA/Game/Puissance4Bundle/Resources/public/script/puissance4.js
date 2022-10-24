let urlWS = urlSock.split("/");
urlWS = urlWS[2]; // on enlève le http (ou https)
urlWS = urlWS.split(":");
urlWS = urlWS[0]; // on enlève le port
urlWS = 'ws://' + urlWS + ':8092';

var conn = new WebSocket(urlWS);
conn.onopen = function (e) {
	connectJson = JSON.stringify({
        type: 'connect',
        gameId: gameId,
        playerId: playerId
    });
    conn.send(connectJson);
	console.log("Connecté à " + urlWS + " !!");
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
    document.getElementById('playerSymbol').innerHTML = "Votre Symbole : " + data.playerSymbol;

    grille = data.grille;
	for (i = 0; i < data.grille.length; i++) {
		for (j = 0; j < data.grille[0].length; j++) {
			document.getElementById(i+':'+j).className = grille[i][j];
		}
	}

    document.getElementById("vs").innerText = "J1 : " + data.player1 + " vs J2 : " + data.player2;
};

var red = "red"; var yellow = "yellow"; var connectionNb = 4;

window.onload = main;

var globalGrid = null;
var isGameFinished = false;
var winner = "none";


function main() {
	globalGrid = generateGrid(7, 6);
}

//	Génère une grille de x colonnes et y lignes
function generateGrid(x, y) {
	let columnHeights = [];
	let p4Div = document.getElementById("puissance4_div");
	let p4Table = document.createElement("table");
	p4Table.setAttribute("style", "table-layout:fixed");
	p4Div.appendChild(p4Table);
	let p4Header = document.createElement("thead");
	p4Header.id = "puissance4_header";
	p4Table.appendChild(p4Header);
	let p4Body = document.createElement("tbody");
	p4Body.id = "puissance4_body";
	p4Table.appendChild(p4Body);

	let p4HeaderRow = document.createElement("tr");
	for (let i = 0; i < x; i++) {
		columnHeights[i] = 0;
		let p4ColumnHeader = document.createElement("th");
		p4ColumnHeader.setAttribute("scope", "col");
		let p4ColumnHeaderButton = document.createElement("button");
		p4ColumnHeaderButton.textContent = "V";
		p4ColumnHeaderButton.onclick = function() {
			insertToken(i,globalGrid);
		};
		p4ColumnHeader.appendChild(p4ColumnHeaderButton);
		p4HeaderRow.appendChild(p4ColumnHeader);
	}
	p4Header.appendChild(p4HeaderRow);


	let gridModel = [];
	for (i = 0; i < x; i++) {
		gridModel[i] = [];
		for (j = 0; j < y; j++) {
			gridModel[i][j] = "none";
		}
	}
	for (j = 0; j < y; j++) {
		let p4Row = document.createElement("tr");
		for (i = 0; i < x; i++) {
			let p4Cell = document.createElement("td");
			p4Cell.id = i + ":" + j;
			p4Row.appendChild(p4Cell);
		}
		p4Body.appendChild(p4Row);
	}
	return {
		model: gridModel,
		width: x, height: y,
		columnHeights: columnHeights,
		turn: red,
		tokenInserted: 0
	};
}

function insertToken(c, grid) {
	if (!isGameFinished) {
		var json = JSON.stringify({
			type: 'jouer',
			column: c
        });
		conn.send(json);
	}
}

function getCell(x, y) {
	let row = document.getElementById("puissance4_body");
	let column = row.childNodes[row.childElementCount - y - 1];
	let cell = column.childNodes[x];
	return cell;
}

function sleep(time) {
	return new Promise(resolve => setTimeout(resolve, time));
}
