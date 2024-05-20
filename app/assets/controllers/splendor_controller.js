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

    async selectCard(card)  {
        let url = card.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_splendor");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }

    async buyCard(card) {
        await this.closeWindowAndFetchUrl(card.params.url)
    }

    async reserveCardOnRow(card) {
        await this.closeWindowAndFetchUrl(card.params.url)
    }

    async reserveCardOnDraw(level) {
        await this.closeWindowAndFetchUrl(level.params.url)
    }

    async takeToken(token) {
        let url = token.params.url;
        await fetch(url);
    }

    async clearSelectedTokens(button) {
        let url = button.params.url;
        await fetch(url);
    }

    async closeWindowAndFetchUrl(url) {
        closeWindow()
        await fetch(url);
    }

}