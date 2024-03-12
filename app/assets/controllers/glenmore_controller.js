import { Controller } from '@hotwired/stimulus';
import personalBoard from "../scripts/Glenmore/personalBoard.js";

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
        let open = isOpening.params.open;
        personalBoard.togglePersonalBoard(open);
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

    async displayPlayerBoard(board) {
        let url = board.params.url;
        const response = await fetch(url);
    }

}