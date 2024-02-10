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
    }

    async selectRow(row)  {
        let url = row.params.url;
        const response = await fetch(url);
    }

    toggleChat(isOpening) {
        const open = isOpening.params.open;
        const openedChat = document.getElementById("openedChat");
        const closedChat = document.getElementById("closedChat");
        if (open) {
            const hidden = document.createAttribute("hidden");
            closedChat.setAttributeNode(hidden);
            openedChat.removeAttribute("hidden");
        } else {
            const hidden = document.createAttribute("hidden");
            closedChat.removeAttribute("hidden");
            openedChat.setAttributeNode(hidden);
        }
    }
}