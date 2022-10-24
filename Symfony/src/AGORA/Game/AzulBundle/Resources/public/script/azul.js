console.log(urlSock);
urlWS = urlSock.split("/")[2];

urlWS = urlWS.split(":")[0];

var conn = new WebSocket('ws://' + urlWS +':8069')
data['ready'] = false;

conn.onopen = function(e){
    console.log("Before sending");
    connect();
    data['action'] = null;
    console.log("Opened Socket!");

    /*
    * Si c'est la première fois qu'on lance le jeu, on initialise le jeux 
    */
    // if (data['initialisation'] == true) { // Définir data['initialisation'] => false
    //     console.log("initializeFabriques");
    //     initializeFabriques();
    //     document.getElementById("score_0").style.setProperty("background-color", 'black');
    //     data['initialisation'] = false;
    // }
}

function connect() {
    data['action'] = "connect";
    conn.send(JSON.stringify(data));
    console.log(data);
}


if(conn.readyState == conn.OPEN){
    data['action']
    console.log("send data when conn.open");
    conn.send(JSON.stringify(data));
}
//Verif
/**
 * Fonction permettant de savoir si toute les fabrique sont vide ou non.
 * @returns true si toute les fabrics sont vide, false sinon.
 * 
 */
function fabriques_all_empty() {
    var fabriques = document.getElementById('fabriques').getElementsByTagName('table');
    for (i = 0; i < fabriques.length; i++) {
        for(j = 0; j < fabriques[i].getElementsByTagName('td').length; j++) {
            if (fabriques[i].getElementsByTagName('td')[j].className !== 'none') {
                return false;
            }
        }
    }
    return true;
}

//Verif
/**
 * Fonction permettant de savoir si le centre du plateau est vide.
 * @returns true si le centre est vide, false sinon.
 */
function centre_empty() {
    for (let i = 1; i <= document.getElementById("centre").getElementsByTagName("td").length; i++) {
        if (document.getElementById("centre_" + i).getAttribute("class").toString() !== 'none') {
            return false;
        }
    }
    return true
}

//Verif
/**
 * Fonction permettant de savoir si le sac contenant les tuiles est vide.
 * @returns true si le sac est vide, false sinon.
 */
function sac_empty() {
    for (var key in sac) {
        if (sac[key] != 0) {
            return false;
        }
    }
    return true;
}

//Verif
/**
 * Fonction permettant de savoir si le couvercle contenant les tuiles retiré du jeu est vide.
 * @returns true si le couvercle est vide, false sinon.
 */
function couvercle_empty() {
    for (var key in couvercle) {
        if (couvercle[key] != 0) {
            return false;
        }
    }
    return true;
}

//Action !!!
/**
 * Fonction permettant de remplir le sac de tuile disponible avec les tuiles retiré du jeu.
 * @returns true parce que pourquoi pas 
 */
function remplir_sac_avec_couvercle() {
    for (var key in couvercle) {
        if (couvercle[key] != 0) {
            sac[key] = sac[key] + couvercle[key];
            couvercle[key] = 0;
        }
    }
    console.log("Remplissage du couvercle");
    conn.send(JSON.stringify({
        action : "actuSac",
        sac : sac,
        couvercle : couvercle,
        gameId : data['gameId'],
        playerId : data['playerId']
    }))
    return true;
}

//Action !!!
/**
 * Fonction permettant de remplir les fabrics avec les tuiles du sac.
 * Si le sac est vide, il va vérifier si le couvercle est vide, si non il va demander a remplir le sac avec les tuiles du couvercle.
 * @returns void
 */
function initializeFabriques() {
    var fabriques = document.getElementById('fabriques').getElementsByTagName('table');
    var dataFab = [];
    var index = 0;
    for (i = 0; i < fabriques.length; i++) {
        dataFab[i] = [];
        for(j = 0; j < 4; j++) {
            var colors = [];
            for (var key in sac) {
                if (sac[key] != 0) {
                    colors.push(key);
                    
                }
            }
            var color = colors[Math.floor(Math.random() * colors.length)];
            dataFab[i].push(color);
            //fabriques[i].getElementsByTagName('td')[j].className = color;
            sac[color] = sac[color] - 1;
            if (sac_empty()) {
                if (couvercle_empty()) {
                    console.log("Couvercle et sac vides");
                    conn.send(JSON.stringify({
                        action : 'actuFab',
                        gameId : data['gameId'],
                        fab : dataFab
                    }));
                    return;
                } else {
                    remplir_sac_avec_couvercle();
                }
            }
        }
    }
    console.log("Fabriques initialisées (initializeFabriques)");
    console.log(dataFab);
    conn.send(JSON.stringify({
        action : 'actuFab',
        gameId : data['gameId'],
        playerId : data['playerId'],
        fab : dataFab
    }));
}



//Verif
/**
 * Fonction permettant de vérifier si les nbLigne première ligne du plateau personnel sont vide. 
 * @param {int} nbLigne Nombre de ligne a vérifier.
 * @returns true si toute les nbLignes première lignes sont vide, false sinon.
 */
function ligne_motif_vide(nbLigne, username = data.username) {
    for (let i = 1; i <= nbLigne; i++) {
        if ($("#plateau_" + username).find("#motif_" + nbLigne + "_" + i).attr("class").toString() !== "case_motif") {
            return false;
        }
    }
    return true;
}

//Verif
/**
 * Fonction permetant de savoir si les nbLigne première lignes sont complètes.
 * @param {int} nbLigne Nombre de ligne a vérifier.
 * @returns true si toutes les nbLignes première lignes sont complètes, false sinon.
 */
function ligne_motif_complete(nbLigne, username = data.username) {
    for (let i = 1; i <= nbLigne; i++) {
        if ($("#plateau_" + username).find("#motif_" + nbLigne + "_" + i).attr("class").toString() === "case_motif") {
            return false;
        }
    }
    return true;
}

//Verif
/**
 * Fonction permettant de savoir si il existe une lignes complètes sur le mur.
 * @returns true si il existe une ligne complètes sur le mur, false sinon.
 */
function existe_ligne_mur_complete(username = data.username) {
    for (let i = 1; i <= 5; i++) {
        var ligne_complete = true;
        for (let j = 1; j <= 5; j++) {
            if ($("#plateau_" + username).find("#mur_" + i + "_" + j).css("opacity")!= 1) {
                ligne_complete = false;
            }
        }
        if (ligne_complete) {
            return true;
        }
    }
    return false;
}

//Verif
/**
 * Fonction permettant de savoir combien il y a de lignes complètes sur le mur.
 * @returns Le nombre de ligne complètes dans le mur.
 */
function nb_ligne_mur_complete(username = data.username) {
    var nb = 0;
    for (let i = 1; i <= 5; i++) {
        var ligne_complete = true;
        for (let j = 1; j <= 5; j++) {
            if ($("#plateau_" + username).find("#mur_" + i + "_" + j).style.opacity != 1) {
                ligne_complete = false;
            }
        }
        if (ligne_complete) {
            nb = nb + 1;
        }
    }
    return nb;
}

//Verif
/**
 * Fonction permettant de savoir combien il y a de colonnes complètes sur le mur.
 * @returns Le nombre de colonne complètes dans le mur.
 */
function nb_colonne_mur_complete(username = data.username) {
    var nb = 0;
    for (let i = 1; i <= 5; i++) {
        var colonne_complete = true;
        for (let j = 1; j <= 5; j++) {
            if ($("#plateau_" + username).find("#mur_" + j + "_" + i).style.opacity != 1) {
                colonne_complete = false;
            }
        }
        if (colonne_complete) {
            nb = nb + 1;
        }
    }
    return nb;
}

//Verif
/**
 * Fonction permettant de savoir combien de couleurs complètes sont posé sur le mur.
 * Un couleur est "complète" lorsque que 5 tuile d'une même couleur sont posé sur le mur.
 * @returns Le nombre de couleur complète.
 */
function nb_couleur_mur_complete() {
    var nb = 0;
    for (var color in couvercle) {
        var nbTuilesPosees = 0;
        for(i = 0; i < $("#plateau_" + username).find("#tableMur").getElementsByClassName("."+color).length; ++i) {
            if ($("#plateau_" + username).find("#tableMur").find("."+color)[i].style.opacity == 1) {
                nbTuilesPosees = nbTuilesPosees + 1;
            }
        }
        if (nbTuilesPosees == 5) {
            nb = nb + 1;
        }
    }
    return nb;
}

//Verif
/**
 * Fonction permettant de savoir si une couleur donnée est déjà présent sur une ligne donné.
 * @param {*} nbLigne Numéro de la ligne.
 * @param {*} couleur Couleur a vérifié.
 * @param {*} typeLigne Type de ligne (soit c'est une ligne 'motif' soir c'est une ligne du 'mur').
 * @returns true si la ligne est présente dans la ligne, false sinon.
 */
function couleur_deja_sur_ligne(nbLigne, couleur, typeLigne, username = data.username) {
    switch(typeLigne) {
        case 'motif':
            for (let i = 1; i <= nbLigne; i++) {
                if ($("#plateau_" + username).find("#motif_" + nbLigne + "_" + i).attr("class").toString() === couleur) {
                    return true;
                }
            }
            return false;
        case 'mur':
            return ($("#plateau_" + username).find("#mur" + nbLigne).find("."+couleur)[0].style.getPropertyValue("opacity").toString() === "1");
        default:
            console.log("Erreur lors de l'appel à la fonction couleur_deja_sur_ligne");
    }
}

//Action
/**
 * Fonction permet d'ajouter une couleur au centre de la table.
 * @param {*} couleur 
 */
function ajout_case_centre(couleur, username = data.username) {
    for (let i = 1; i <= $("#plateau_" + username).find("#centre").find("td").length; i++) {
        if (d$("#plateau_" + username).find("#centre_" + i).attr("class").toString() === "none") {
            $("#plateau_" + username).find("#centre_" + i).attr("class", couleur);
            break;
        }
    }
}

//Action
/**
 * Fonction permettant de retirer tout une couleur du centre.
 * @param {*} couleur 
 */
function vider_couleur_centre(couleur, username = data.username) {
    for (let i = 1; i <= $("#plateau_" + username).find("#centre").find("td").length; i++) {
        if ($("#plateau_" + username).find("#centre_" + i).attr("class").toString() === couleur) {
            console.log("vider_couleur_centre");
            $("#plateau_" + username).find("#centre_" + i).attr("class", "none");
        }
    }
}

//Action
/**
 * Fonction permettant de vider une fabric en plaçant toute les tuiles mise a part la couleur selectionné.
 * @param {*} couleur Couleur selectionné
 * @param {*} id id de la fabrics à vider.
 */
function vider_fabrique(couleur, id) {
    console.log('vider_fabrique');
    for (let i = 1; i <= 4; i++) {
        if (document.getElementById("fab_" + id + "_" + i).getAttribute("class").toString() !== couleur) {
            ajout_case_centre(document.getElementById("fab_" + id + "_" + i).getAttribute("class").toString());
        }
        document.getElementById("fab_" + id + "_" + i).setAttribute("class", "none");
    }
}

//Verif
/**
 * Fonction permettant de compter les tuiles horizontal.
 * @param {*} ligne numéro de ligne a vérifier.
 * @param {*} colonne nombre de colone a vérifié.
 * @returns Le nombre de tuile horizontale.
 */
function compter_tuiles_voisines_horizontales(ligne, colonne, username=data.username) {
    var nb = 0;
    if (colonne != 1) {
        for (let i = (colonne - 1); 1 <= i; i--) {
            if ($("#plateau_" + username).find("#mur_" + ligne + "_" + i).css("opacity") == 1) {
                nb = nb + 1;
            } else {
                break;
            }
        }
    }
    if (colonne != 5) {
        for (let i = (colonne + 1); i <= 5; i++) {
            if ($("#plateau_" + username).find("#mur_" + ligne + "_" + i).css("opacity") == 1) {
                nb = nb + 1;
            } else {
                break;
            }
        }
    }
    return nb;
}

//Verif
/**
 * Fonction permettant de compter les tuiles verticale.
 * @param {*} ligne Nombre de ligne a vérifier.
 * @param {*} colonne numéro de la colonne à vérifier.
 * @returns Le nombre de tuile verticale.
 */
function compter_tuiles_voisines_verticales(ligne, colonne, username = data.username) {
    var nb = 0;
    if (ligne != 1) {
        for (let i = (ligne - 1); 1 <= i; i--) {
            if ($("#plateau_" + username).find("#mur_" + i + "_" + colonne).css("opacity") == 1) {
                nb = nb + 1;
            } else {
                break;
            }
        }
    }
    if (ligne != 5) {
        for (let i = (ligne + 1); i <= 5; i++) {
            if ($("#plateau_" + username).find("#mur_" + i + "_" + colonne).css("opacity") == 1) {
                nb = nb + 1;
            } else {
                break;
            }
        }
    }
    return nb;
}

/*
 * Rends les boutons du centre cliquable 
 */
for (let i = 1; i <= document.getElementById("centre").getElementsByTagName("td").length; i++) {
    document.getElementById("centre_" + i).onclick = function() {
        var tuile =document.getElementById("centre_" + i).getAttribute("class").toString();
        if (tuile !== "none" && tuile !== "first") {
            if(data['tuileColor'] == null) {
                data['ready'] = true;
                data['action'] = 'piocher';
                data['source'] = 'centre';
                data['case'] = 'centre_' + i;
                data['tuileColor'] = document.getElementById("centre_" + i).getAttribute("class");
                data['nbTuiles'] = document.getElementById("centre").getElementsByClassName(data['tuileColor']).length;
                conn.send(JSON.stringify(data));
                data['tuileColor'] = null;
            } else {
                alert("Vous avez déjà pioché une tuile");
            }
        }
    };
}

/*
* Rends les boutons fabrics cliquable
*/
for (let i = 1; i <= document.getElementById('fabriques').getElementsByTagName('table').length; i++) {
    for(let j = 1; j <= 4; ++j) {
        document.getElementById("fab_" + i + "_" + j).onclick = function() {
            if (document.getElementById("fab_" + i + "_" + j).getAttribute("class").toString() !== "none") {
                if(data['tuileColor'] == null) {
                    data['ready'] = true;
                    data['action'] = 'piocher';
                    data['source'] = 'fabrique_' + i;
                    data['case'] = "fab_" + i + "_" + j;
                    data['tuileColor'] = document.getElementById("fab_" + i + "_" + j).getAttribute("class");
                    data['nbTuiles'] = document.getElementById("fabrique_" + i).getElementsByClassName(data['tuileColor']).length;
                    conn.send(JSON.stringify(data));
                    console.log(data);
                    data['tuileColor'] = null;
                } else {
                    alert("Vous avez déjà pioché une tuile");
                }
            }
        };
        }
}

/*
 *Rend les bouton du paneau motif cliquable 
 */
    for (let i = 1; i <= 5; i++) {
        for (let j = 1; j <= i; ++j) {
            $("#plateau_" + data.username).find("#motif_" + i + "_" + j).click(function() { 
                    if(data['tuileColor'] != null) {
                        if (!couleur_deja_sur_ligne(i, data['tuileColor'], 'motif') && !ligne_motif_vide(i)) {
                            alert("Cette ligne motif a été commencé avec une autre couleur, veuillez en choisir une autre");
                        } else if (couleur_deja_sur_ligne(i, data['tuileColor'], 'mur')) {
                            alert("Le mur a déjà cette couleur sur cette ligne, veuillez choisir une autre ligne motif");
                        } else if (ligne_motif_complete(i)) {
                            alert("Cette ligne motif est déjà complète, veuillez en remplir une autre");
                        } else {
                            data['action'] = 'poser';
                            data['destination'] = 'motif';
                            data['source'] = null;
                            data['case'] = 'motif_' + i + '_' + j;
                            conn.send(JSON.stringify(data));
                            console.log(data);
                            data['tuileColor'] = null;
                            data['nbTuiles'] = 0;
                        }
                    } else {
                        alert("Veuillez d'abord piocher une tuile");
                    }
            }).css('cursor', 'pointer')
        }
    }


/*
* Rend les bouton du plancher cliquable
*/
for (let i = 1; i <= 7; i++) {
    $("#plateau_" + data.username).find("#plancher_" + i).click(function() { 
            if(data['tuileColor'] != null) {
                data['action'] = 'poser';
                data['source'] = null;
                data['destination'] = 'plancher';
                data['case'] = 'plancher_' + i;
                conn.send(JSON.stringify(data));
                console.log(data);
                data['tuileColor'] = null;
                data['nbTuiles'] = 0;
            } else {
                alert("Veuillez d'abord piocher une tuile");
            }
    }).css('cursor', 'pointer')
}

//verif
/**
 * Fonction calculant les points obtenu en fin de partie et affiche le resultat sur le plateau du joueur
 */
function points_fin_de_partie() {
    var timeOut = 0;

    //on rajoute 2 points par ligne complète
    for (let i = 0; i < nb_ligne_mur_complete(); i++) {
        setTimeout(function() { 
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
            data['score'] = data['score'] + 2;
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'black');
        }, timeOut);
        timeOut = timeOut + 1000;
    }

    //on rajoute 7 points par colonne complète
    for (let i = 0; i < nb_colonne_mur_complete(); i++) {
        setTimeout(function() {
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
            data['score'] = data['score'] + 7;
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'black');
        }, timeOut);
        timeOut = timeOut + 1000;
    }

    //on rajoute 10 points par couleur entièrement présente sur le mur
    for (let i = 0; i < nb_colonne_mur_complete(); i++) {
        setTimeout(function() {
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
            data['score'] = data['score'] + 10;
            // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'black');
        }, timeOut);
        timeOut = timeOut + 1000;
    }

    console.log("Fin du game");
    conn.send(JSON.stringify({
        action : "fin",
        score : data['score'],
        gameId : data['gameId'],
        playerId : data['playerId']
    }))
}

//action
/**
 * Fonction permettant d'effectuer la fin d'une manche
 */
function fin_de_manche(username=data.username) {
    console.log("fin de manche");
    var hasBeenFullyChecked = false;
    //PHASE 2
    document.getElementsByTagName('h2')[0].innerHTML = "Phase 2 : Décoration des murs du palais";
    data["phase"] = 2;

    //on désactive tout les évènements onclick
    document.body.style.pointerEvents = "none";

    var timeOut = 200;
    var timeOutExtra = timeOut;
    //on construit le mur au fur et à mesure
    for (let i = 1; i <= 5; i++) {
        if (ligne_motif_complete(i)) {
            setTimeout(function(ligne) {
                var color = $("#plateau_" + username).find("#motif_" + ligne + "_" + 1).attr("class");
                $("#plateau_" + username).find("#motif_" + ligne + "_" + 1).attr("class", "case_motif");
                $($("#plateau_" + username).find("#mur" + ligne).find("."+color)[0]).css("opacity", 1);
                var colonne = $("#plateau_" + username).find("#mur" + ligne).find("."+color)[0].id.split("_")[2];
                // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
                console.log("coordonnees :" + ligne + "," + colonne);
                console.log("voisins horizontaux : " + compter_tuiles_voisines_horizontales(ligne, colonne,username));
                console.log("voisins verticaux : " + compter_tuiles_voisines_verticales(ligne, colonne, username));
                var score = 1 + compter_tuiles_voisines_horizontales(ligne, colonne, username) + compter_tuiles_voisines_verticales(ligne, colonne, username);
                console.log("score : " + score);
                if ((compter_tuiles_voisines_horizontales(ligne, colonne,username) != 0) && (compter_tuiles_voisines_verticales(ligne, colonne) != 0)) {
                    score = score + 1;
                }
                data['score'] = data['score'] + score;
                // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'black');
            }, timeOut, i);

            timeOut = timeOut + timeOutExtra;
            for (let j = 2; j <= i; j++) {
                setTimeout(function()
                {
                    var colorMotif = $("#plateau_" + username).find("#motif_" + i + "_" + j).attr("class");
                    $("#plateau_" + username).find("#motif_" + i + "_" + j).attr("class", "case_motif");
                    couvercle[colorMotif] = + couvercle[colorMotif] + 1;
                }, timeOut);
                timeOut = timeOut + timeOutExtra;
            }
        }
    }

    var isTherePlancher = $("#plateau_" + username).find("#plancher_1").attr("class") !== 'none';
    //on vide la ligne plancher
    if(isTherePlancher) {
        for (let i = 1; i <= 7; i++) {
            if ($("#plateau_" + username).find("#plancher_" + i).attr("class") !== 'none') {
                setTimeout(function() {
                    var colorMotif = $("#plateau_" + username).find("#plancher_" + i).attr("class");
                    $("#plateau_" + username).find("#plancher_" + i).attr("class", "none");
                    couvercle[colorMotif] = + couvercle[colorMotif] + 1;
                    
                    // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
                    var points = 3;
                    if(i <= 5) {
                        points = 2;
                    }
                    if(i <= 2) {
                        points = 1;
                    }
                    if ((data['score'] - points) > 0) {
                        data['score'] = data['score'] - points;
                    } else {
                        data['score'] = 0;
                    }
                    // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'black');
                    if (i == 7 || $("#plateau_" + username).find("#plancher_" + (i+1)).attr("class") == 'none') fin_de_manche_traitement(username);
                }, timeOut);
                timeOut = timeOut + timeOutExtra;
            }
        }
    } else {
        fin_de_manche_traitement(username);
    }
    

    }

    function is_game_finished() {
        //on teste si le jeu est fini
        var jeuFini = false;
        if (existe_ligne_mur_complete()) {
            jeuFini = true
        }
        return jeuFini;
    }

    function fin_de_manche_traitement(username=data.username) {
        var timeOut = 1000;

        var wall = [];
        for (let i = 1; i <= 5; i++) {
            wall[i-1] = [];
            for (let j = 1; j <= 5; j++) {
                wall[i-1][j-1] = $("#plateau_" + username).find("#mur_" + i + "_" + j).css("opacity") == 1;
            }
        }

        var msg = JSON.stringify({
            action : "fin_manche",
            wall : wall,
            score : data['score'],
            playerId : data['playerId'],
            gameId : data['gameId'],
            couvercle : couvercle
        });
        console.log("Traitement du fin de manche");
        console.log(msg);
        conn.send(msg);

        var jeuFini = is_game_finished();


        //Si le jeu n'est pas fini, on passe à la phase 3, sinon on compte les points de fin de partie
        setTimeout(function()
        {
            var time = 0; 
            if(!jeuFini) {
                //PHASE 3
                setTimeout(function()
                { document.getElementsByTagName('h2')[0].innerHTML = "Phase 3 : Préparation de la prochaine manche";
                    data["phase"] = 3;

                    //on initialise les fabriques
                    initializeFabriques();
                }, time);
                time = time + 1000;

                //on réactive tout les évènements onclick
                setTimeout(function()
                    { document.body.style.pointerEvents = "auto";
                    document.getElementsByTagName('h2')[0].innerHTML = "Phase 1 : Offre des fabriques";
                    data["phase"] = 1;
                }, time);
            } else {
                points_fin_de_partie();
                document.getElementsByTagName('h2')[0].innerHTML = "Jeu terminé !";
            }
        }, timeOut);
    }

/**
 * Fonction effectuer lors de la reception d'un message.
 * @param {*} e message reçus
 */
conn.onmessage = function(e){
    var jsonData = JSON.parse(e.data);
    console.log("received : ");
    console.log(jsonData);
    // console.log("received : " + jsonData.action);
    
    if(jsonData.action == 'piocher') { //never use
        if(jsonData.source == 'centre') {
            vider_couleur_centre(jsonData.tuileColor);
        } else {
            var id = jsonData.source.split("_")[1];
            vider_fabrique(jsonData.tuileColor, id);
        }
        document.getElementById(jsonData.case).setAttribute("class", "none");
        console.log("onmessage 1");
        document.getElementById('color_pioche').setAttribute("class", jsonData.tuileColor);
        document.getElementById('nb_pioche').innerHTML = jsonData.nbTuiles;
        data['tuileColor'] = jsonData.tuileColor;
        data['nbTuiles'] = jsonData.nbTuiles;
    
    }

    if(jsonData.action == 'poser') { //never use
        if(jsonData.destination == 'motif') {
            var nbLigne = jsonData.case.split("_")[1];
            var nbCasesPoses = $("#plateau_" + username).find("#motif_" + nbLigne).find("."+jsonData.tuileColor).length;
            if (jsonData.nbTuiles <= (nbLigne - nbCasesPoses)) {
                for (let i = (nbCasesPoses + 1); i <= (nbCasesPoses + jsonData.nbTuiles); i++) {
                    $("#plateau_" + username).find("#motif_" + nbLigne + "_" + i).attr("class", jsonData.tuileColor);
                }
            } else {
                for (let i = (nbCasesPoses + 1); i <= nbLigne; i++) {
                    $("#plateau_" + username).find("#motif_" + nbLigne + "_" + i).attr("class", jsonData.tuileColor);
                }
                var cases_plancher = (jsonData.nbTuiles - (nbLigne - nbCasesPoses));
                for (let i = 1; i <= 7; i++) {
                    if (cases_plancher > 0) {
                        if($("#plateau_" + username).find("#plancher_" + i).attr("class").toString() === "none") {
                            $("#plateau_" + username).find("#plancher_" + i).attr("class", jsonData.tuileColor);
                            --cases_plancher;
                        }
                    }
                }
                couvercle[jsonData.tuileColor] = couvercle[jsonData.tuileColor] + cases_plancher;
            }
        }
        if (jsonData.destination == 'plancher') {
            var cases_plancher = jsonData.nbTuiles;
            for (let i = 1; i <= 7; i++) {
                if (cases_plancher > 0) {
                    if($("#plateau_" + username).find("#plancher_" + i).attr("class").toString() === "none") {
                        $("#plateau_" + username).find("#plancher_" + i).attr("class", jsonData.tuileColor);
                        --cases_plancher;
                    }
                }
            }
            couvercle[jsonData.tuileColor] = couvercle[jsonData.tuileColor] + cases_plancher;
        }
        $("#plateau_" + username).find('#color_pioche').attr("class", "none");
        console.log("onmessage 2");
        $("#plateau_" + username).find('#nb_pioche').html("0");

        
    }

    if(jsonData.action == 'actu'){
        refresh(jsonData);
    }

    if(jsonData.action == 'not your turn'){
        alert("Ce n'est pas à votre tour de jouer.\nC'est le tour de " + jsonData.turn);
    }

    
    
    if(jsonData.action == 'fin'){
        var affiche = "";
        if(jsonData.win){
            affiche = "Vous avez gagné!\n";
        } else {
            affiche = jsonData.winner + " a gagné!";
        }
        alert(affiche);
    } 

    if(jsonData.action == '...'){
        console.log("...................................");
        setTimeout(function(){
            conn.send(JSON.stringify({
                action : '?',
                gameId : data['gameId'],
                playerId : data['playerId']
            }))
        }, 1000);
    }

}

function refresh(jsonData) {
    // console.log(data.playersInGame + "::" + jsonData.players);
    if (data.playersInGame == 0) {
        data.playersInGame = jsonData.players;
    } else if ($("#tabs li").length != jsonData.players) {
        location.reload();
    }
    if (!jsonData.for_me || !jsonData.isGameFilled) return;
    // console.log(data.username);
    // $(".plateau").each(function () {
    //     console.log($(this).attr('id').split('_')[1])
    //     if (data.username != $(this).attr('id').split('_')[1]) {
    //         $(this).css('display','none');
    //     }
    // });

    var plateau = $("#plateau_" + jsonData.refreshWho);
    // console.log(plateau);
    //On met a jour le plancher
    for(let i = 1; i <= 7; i++){
        if(i <= jsonData.plancher.length){
            plateau.find("#plancher_" + i).attr('class', jsonData.plancher[i-1]);
        } else {
            plateau.find("#plancher_" + i).attr('class', "none");
        }
        
    }


    //on met a jour les tuiles selectionnées
    if (jsonData.refreshWho == data.username) {
        if(jsonData.selected == "none"){
            document.getElementById('color_pioche').setAttribute("class", "none");
            document.getElementById('nb_pioche').innerHTML = "0";
            data['tuileColor'] = null;
            data['nbTuiles'] = 0;
        } else {
            document.getElementById('color_pioche').setAttribute("class", jsonData.selected);
            document.getElementById('nb_pioche').innerHTML = jsonData.nbSelect;
            data['tuileColor'] = jsonData.selected;
            data['nbTuiles'] = jsonData.nbSelect;
        }
    }
    

    //On vérifie l'état des fabrique et du centre
    if(jsonData.endTurn && fabriques_all_empty() && centre_empty()) {
        fin_de_manche(data.username);
    }

    //On met a jour le motif
    for(let i = 0; i < 5; i++){
        for(let j = 0; j < i+1; j++){
            if(j < jsonData.motif[i].length){
                plateau.find("#motif_" + (i+1) + "_" + (j+1)).attr("class", jsonData.motif[i][j]);
            } else {
                plateau.find("#motif_" + (i+1) + "_" + (j+1)).attr("class", "case_motif");
            }
            
        }
    }

    //On met a jour le centre
    for(let i = 1; i <= document.getElementById("centre").getElementsByTagName("td").length; i++){
        if(i <= jsonData.centre.length){
            document.getElementById("centre_"+i).setAttribute("class",jsonData.centre[i-1]);
        } else {
            document.getElementById("centre_"+i).setAttribute("class","none");
        }
    }

    //On met a jour les fabriques
    //console.log('phase=' + data["phase"]);
    if(data["phase"] != 2) {
        for(let i = 0; i < jsonData.fab.length; i++){
            if(jsonData.fab[i].length != 0){
                for(let j = 0 ; j < jsonData.fab[i].length; j++){
                    // console.log("fab : " + i + " : " + j + " : " + jsonData.fab[i][j]);
                    document.getElementById("fab_"+(i+1)+"_"+(j+1)).setAttribute("class",jsonData.fab[i][j]);
                }
            } else {
                for(let j = 1; j <= 4; j++){
                    document.getElementById("fab_"+(i+1)+"_"+j).setAttribute("class","none");
                }
            }
            
        }
    }

    //On met a jour le mur
    //console.log(plateau)
    for (let i = 0; i < jsonData.mur.length; i++) {
        for (let j = 0; j < jsonData.mur[i].length;j++){
            if (jsonData.mur[i][j]) {
                plateau.find("#mur_"+(i+1)+"_"+(j+1)).css("opacity", 1);
            }
        }
    }

    //On met a jour le score
    // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'transparent');
    if (jsonData.refreshWho == data.username) {
        data['score'] = jsonData.score;
        document.getElementById('score').innerHTML = jsonData.score;
    }
    // document.getElementById("score_" + data['score']).style.setProperty("background-color", 'grey');

    //On met a jour le sac
    sac['red'] = jsonData.sac['red'];
    sac['blue'] = jsonData.sac['blue'];
    sac['cyan'] = jsonData.sac['cyan'];
    sac['orange'] = jsonData.sac['orange'];
    sac['black'] = jsonData.sac['black'];

    //On met a jour le couvercle
    couvercle['red'] = jsonData.couvercle['red'];
    couvercle['blue'] = jsonData.couvercle['blue'];
    couvercle['cyan'] = jsonData.couvercle['cyan'];
    couvercle['orange'] = jsonData.couvercle['orange'];
    couvercle['black'] = jsonData.couvercle['black'];
    
    

    //on affiche le nom du joueur dont c'est le tour
    document.getElementById("tour").innerHTML = "C'est le tour de " + jsonData.turn
                                            + (jsonData.currentPlayerId == data.playerId ? ' (Vous)':'');
    //On indique si le joueur est le premier joueur
    if(jsonData.first){
        document.getElementById("premier_joueur").setAttribute("class", "first");
    } else {
        document.getElementById("premier_joueur").setAttribute("class", "none");
    }

    $("#score_" + jsonData.refreshWho).html(jsonData.score)
}

function display(username = data.username) {
    var plateaux = $('.plateau');
    for (var i = 0; i < plateaux.length; i++) {
        var plateau = plateaux[i]
        var plateau_owner = $(plateau).attr('id').substring('plateau_'.length,$(plateau).attr('id').length)
        if (plateau_owner == username) {
            if ($(plateau).hasClass('hidden')) {
                $(plateau).removeClass('hidden')
            }
        } else {
            $(plateau).addClass('hidden')
        }
    }

    var tabs = $('.tab');
    for (var i = 0; i < tabs.length; i++) {
        var tab = tabs[i]
        var tab_owner = $(tab).attr('id').substring('tab_'.length,$(plateau).attr('id').length)
        if (tab_owner == username) {
            $(tab).addClass('selected_tab')
        } else {
            if ($(tab).hasClass('selected_tab')) {
                $(tab).removeClass('selected_tab')
            }
        }
    }

}

display()