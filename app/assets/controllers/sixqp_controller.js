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
        await fetch(url);
    }

    async selectRow(row)  {
        let url = row.params.url;
        await fetch(url);
    }
}