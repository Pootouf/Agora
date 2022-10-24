
//Crée le plateau de jeu
function createBoard(boardAsString) {
    board = boardAsString.split(";");
    var b = document.getElementById('board');
    for (var i = 1; i <= 4; ++i) {
        var rowNode = document.createElement('div');
        rowNode.setAttribute('id', 'row' + i);
        rowNode.className = 'row';

        var row = board[i - 1].split(",");
        boardModel["row" + i] = new Array();
        for (var j = 1; j <= 6; ++j) {
            var cell = document.createElement('div');
            cell.setAttribute('id', 'cell' + j + "" + i);
            cell.className = 'cell';
            if (row[j - 1] != 0 && row[j - 1] != null && row[j - 1] != undefined) {
                boardModel["row" + i]["cell" + j] = new GraphicCard(row[j - 1]);
                var img = document.createElement('div');
                img.setAttribute('id', 'tracker' + row[j - 1]);
                img.className = 'tracker';
                cell.appendChild(img);

            }
            rowNode.appendChild(cell);
        }
        b.appendChild(rowNode);
    }
    for (var row in boardModel) {
        for (var card in boardModel[row]) {
            boardModel[row][card].revealCard();
        }
    }
}


//Joue la carte sélectionnée sur le plateau, 
// affiche le choix de la ligne si la carte est trop petite
function displayRowChoice(card) {
    var board = document.getElementById("board");
    var children = board.childNodes;
    var count = 0;
    var rowIndex = -1;
    var diff = 200;//nombre magique !
    //On cherche la carte la plus proche de celle que l'on veut poser
    for(var i = 0; i < 4; ++i) {
        var size = 0;
        var row = "row" + (i + 1);
        var cell = "cell1";
        while (boardModel[row][cell] != undefined && boardModel[row][cell] != null) {
            ++size;
            cell = "cell" + size;
        }
        cell = "cell" + (size - 1);
        if (boardModel[row][cell].getValue() < card) {
            if (card - boardModel[row][cell].getValue() < diff) {
                diff = card - boardModel[row][cell].getValue();
                rowIndex = i;
            }
            ++count;
        }

    }
    //Si aucune carte est plus petite
    if (count == 0) {
        for(var i = 0; i < 4; ++i) {
            var size = 0;
            var row  = "row" + (i + 1);
            var cell = "cell1";
            while (boardModel[row][cell] != undefined && boardModel[row][cell] != null) {
                ++size;
                cell = "cell" + size;
            }
            cell = "cell" + (size - 1);

            var butt = document.createElement("button");
            butt.textContent = "Prendre la ligne";
            butt.setAttribute("onclick", "putCardInRow("+ i +")");
            butt.id = "buttRow" + i;
            butt.className = "bRow";
            children[i].insertBefore(butt, children[i].firstChild);
            ++count;
        }
        var stat = document.getElementById("status");
        stat.textContent = "Votre carte est trop petite ! Veuillez prendre une ligne !";
    } else {
        putCardInRow(rowIndex);
    }
}

//Crée la main
function createHand(handAsString) {
    hand = handAsString.split(",");

    var h = document.getElementById('hand');
    for (var i = 1; i <= 10; ++i) {
        if (hand[i - 1] != 0 && hand[i - 1] != 'undefined' && hand[i - 1] != null) {//Bizarre à approfondir
            handModel[i - 1] = new GraphicCard(hand[i - 1]);
            var img = document.createElement('div');
            img.setAttribute('id', 'tracker' + hand[i - 1]);
            if (cardInBox == null || cardInBox == undefined) {
                img.setAttribute('onclick', 'clickOnCard(' + hand[i - 1] + ');');
            }
            img.className = 'tracker';
            h.appendChild(img);
        }
    }
    for (var i in handModel) {
        handModel[i].revealCard();
    }
    $('#status').textContent = "Choisissez une carte !";
}

//Remove une carte de la main du joueur
function removeCardFromHandModel(card) {
    for (var i in handModel) {
        if (handModel[i] == card) {
            handModel.splice(i, 1);
        }
    }
}

//Enlève l'event sur les cartes de la main du joueur
function removeEventClickOnHand() {
    var handDOM = document.getElementById('hand');
    var cards = handDOM.childNodes;
    cards.forEach(function(element) {
        element.onclick = function () {
            return false;
        }
    });

}

//Fonction executée lors du cliquage sur le tchat pour l'agrandir/réduire
function tchatOnClick(e) {
    var tchat = document.getElementsByClassName("tchat")[0];
    if (IsOpenTchat == 0) {
        tchat.style.height = "23em";
        IsOpenTchat = 1;
        $('#flecheTchat')
            .attr("src", "/bundles/agoragamesqp/image/fleche_historique_haut.png");

    } else {
        tchat.style.height = "1em";
        IsOpenTchat = 0;
        $('#flecheTchat')
            .attr("src", "/bundles/agoragamesqp/image/fleche_historique_bas.png");

    }
}