import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    async excludePlayer(data) {
        let url = data.params.url;
        await fetch(url)
    }
}