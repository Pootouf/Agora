import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    async sendMessage(message) {
        const url = message.params.url.replaceAll("%20","")
            + document.getElementById("inputMessage").value.replaceAll(" ","%20");
        console.log(url);
        const response = await fetch(url);
    }

}