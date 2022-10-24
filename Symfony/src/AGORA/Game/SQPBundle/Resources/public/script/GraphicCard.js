//Cette fonction est très importante,
// elle permet de récupérer l'offset du scroll
// évitant les bugs d'affichage (div mal placé) quand l'utilisateur déplace
//un objet alors qu'il a scrollé
function dw_getScrollOffsets() {
    var doc = document, w = window;
    var x, y, docEl;

    if ( typeof w.pageYOffset === 'number' ) {
        x = w.pageXOffset;
        y = w.pageYOffset;
    } else {
        docEl = (doc.compatMode && doc.compatMode === 'CSS1Compat')?
            doc.documentElement: doc.body;
        x = docEl.scrollLeft;
        y = docEl.scrollTop;
    }
    return {x:x, y:y};
}

class GraphicCard {
    constructor (id) {
        this.id = "tracker" + id;
        //On calcule le nombre de tête de boeuf
        var nbeef = 1;
        if (id%10 == 0) {
            nbeef = 3;
        } else if (id%5 == 0) {
            if (id == 55) {
                nbeef = 7;
            } else {
                nbeef = 2;
            }

        } else if (id%11 == 0) {
            nbeef = 5;
        }
        //Card6QP est défini dans le fichier js/Card6QP.js
        this.card = new Card6QP(nbeef, id);

    }
    //On affiche la carte
    revealCard() {
        $('#' + this.id).css("background-image", "url('" + "/bundles/agoragamesqp/image/cartes/" + this.getValue() + ".png" + "')");
    }
    //Nombre de la carte
    getValue() {
        return this.card.getValue();
    }
    //Nombre de têtes de boeuf
    getNbBeef() {
        return this.card.getNbBeef();
    }
    //ID dans le DOM
    getId() {
        return this.id;
    }
    //Déplace le div
    moveDiv(event) {
        var off = dw_getScrollOffsets();
        $('#' + this.id).css('position', 'absolute');
        $('#' + this.id).css('top', event.clientY - ($('#' + this.id).height() / 2) + off.y);
        $('#' + this.id).css('left', event.clientX - ($('#' + this.id).width() / 2) + off.x);
    }
    setDivPosition(x, y, enableScrollOffset) {
        var off = dw_getScrollOffsets();
        if (!enableScrollOffset) {
            off.x = 0;
            off.y = 0;
        }

        $('#' + this.id).css('position', 'absolute');
        $('#' + this.id).css('top', y - ($('#' + this.id).height() / 2) + off.y);
        $('#' + this.id).css('left', x - ($('#' + this.id).width() / 2) + off.x);
    }
    //Déplacele div dans la case du joueur
    moveDivInSlot(slotID) {
        var slotDOM = document.getElementById(slotID);
        var card = document.getElementById(this.id);
        var newCard = document.createElement('div');
        newCard.setAttribute('id', 'tracker' + this.getValue());
        newCard.className = 'trackerNoRotation';
        slotDOM.appendChild(newCard);
        card.parentNode.removeChild(card);
        this.revealCard();
    }
}
