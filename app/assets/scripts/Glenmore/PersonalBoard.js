export default class PersonalBoard {

    static togglePersonalBoard(open) {
        const openedPersonalBoard = document.getElementById("openedPersonalBoard");
        const closedPersonalBoard = document.getElementById("closedPersonalBoard");
        const Timing = {
            duration: 750,
            iterations: 1,
        }
        if (open) {
            const hidden = document.createAttribute("hidden");
            closedPersonalBoard.setAttributeNode(hidden);
            openedPersonalBoard.removeAttribute("hidden");
            const openingSliding = [
                { transform: "translateY(60rem)"},
                { transform: "translateY(0rem)"}
            ]
            openedPersonalBoard.animate(openingSliding,Timing);

            let personalBoard = document.getElementById('tileBoard');
            personalBoard.scrollTop = personalBoard.scrollHeight;
        } else {
            const hidden = document.createAttribute("hidden");
            closedPersonalBoard.removeAttribute("hidden");
            const closingSliding = [
                { transform: "translateY(0rem)"},
                { transform: "translateY(60rem)"}
            ]
            openedPersonalBoard.animate(closingSliding,Timing).addEventListener("finish",
                () => openedPersonalBoard.setAttributeNode(hidden));
        }
    }
}