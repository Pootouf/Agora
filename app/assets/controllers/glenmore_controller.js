import { Controller } from '@hotwired/stimulus';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    togglePersonalBoard(isOpening) {
        const open = isOpening.params.open;
        const openedPersonalBoard = document.getElementById("openedPersonalBoard");
        const closedPersonalBoard = document.getElementById("closedPersonalBoard");
        const Timing = {
            duration: 600,
            iterations: 1,
        }
        if (open) {
            const hidden = document.createAttribute("hidden");
            closedPersonalBoard.setAttributeNode(hidden);
            openedPersonalBoard.removeAttribute("hidden");
            const openingSliding = [
                { transform: "translateY(40rem)"},
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
                { transform: "translateY(40rem)"}
            ]
            openedPersonalBoard.animate(closingSliding,Timing).addEventListener("finish",
                () => openedPersonalBoard.setAttributeNode(hidden));
        }
    }

    async displayPropertyCards(board) {
        let url = board.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_glenmore");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }

    async selectTile(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_glenmore");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }

    async selectResource(resource)  {
        let url = resource.params.url;
        const response = await fetch(url);
    }

    async activateTile(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async removeVillager(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async moveVillager(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async validateNewResourcesAcquisition(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async cancelNewResourcesAcquisition(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async validateResourcesSelection(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async cancelResourcesSelection(tile)  {
        let url = tile.params.url;
        const response = await fetch(url);
    }

    async endRoundPlayer(player)  {
        let url = player.params.url;
        const response = await fetch(url);
    }

    async endActivationTilesPhase(player)  {
        let url = player.params.url;
        const response = await fetch(url);
    }

    async buyTile(tile) {
        let url = tile.params.url;
        const response = await fetch(url);
    }
}