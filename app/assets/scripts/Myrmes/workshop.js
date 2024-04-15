export default class Workshop {

    static toggleWorkshop(open) {
        const openedWorkshop = document.getElementById('openedWorkshopMenu');
        const closedWorkshop = document.getElementById('closedWorkshopMenu');
        const Timing = {
            duration: 750,
            iterations: 1,
        }

        if (open) {
            const hidden = document.createAttribute('hidden');
            closedWorkshop.setAttributeNode(hidden);
            openedWorkshop.removeAttribute('hidden');
            const openingSliding = [
                { transform: "translateY(-26vw)"},
                { transform: "translateY(0vw)"}
            ];
            openedWorkshop.animate(openingSliding, Timing);
        } else {
            const hidden = document.createAttribute('hidden');
            closedWorkshop.removeAttribute('hidden');
            const closingSliding = [
                { transform: "translateY(0vw)"},
                { transform: "translateY(-26vw)"}
            ];
            openedWorkshop.animate(closingSliding, Timing).addEventListener('finish',
                () => openedWorkshop.setAttributeNode(hidden));
        }
    }
}