import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    async sendMessage(message) {
        let m = document.getElementById("inputMessage").value;
        if(m.trim() === "") {
            return;
        }
        const url = message.params.url
            + encodeURI(m);
        const response = await fetch(url);
        document.getElementById("inputMessage").value = "";
    }

    toggleChat(isOpening) {
        const open = isOpening.params.open;
        const openedChat = document.getElementById("openedChat");
        const closedChat = document.getElementById("closedChat");
        const Timing = {
            duration: 600,
            iterations: 1,
        }
        if (open) {
            const hidden = document.createAttribute("hidden");
            closedChat.setAttributeNode(hidden);
            openedChat.removeAttribute("hidden");
            const openingSliding = [
                { transform: "translateY(40rem)"},
                { transform: "translateY(0rem)"}
            ]
            openedChat.animate(openingSliding,Timing);

            let chat = document.getElementById('chat');
            chat.scrollTop = chat.scrollHeight;
        } else {
            const hidden = document.createAttribute("hidden");
            closedChat.removeAttribute("hidden");
            const closingSliding = [
                { transform: "translateY(0rem)"},
                { transform: "translateY(40rem)"}
            ]
            openedChat.animate(closingSliding,Timing).addEventListener("finish",
                () => openedChat.setAttributeNode(hidden));
        }
    }

}