import { Controller } from '@hotwired/stimulus';

export default class extends Controller  {

    //place nurse

    async placeNurseOnLarvaeTrack(position) {
        let url = position.params.url;
        const response = await fetch(url);
    }

    async placeNurseOnSoldiersTrack(position) {
        let url = position.params.url;
        const response = await fetch(url);
    }

    async placeNurseOnWorkersTrack(position) {
        let url = position.params.url;
        const response = await fetch(url);
    }

    async confirmNursesPlacement(confirm) {
        let url = confirm.params.url;
        const response = await fetch(url);
    }

    placeNurseOnWorkshop() {
        alert("workshop");
    }

    //move on event track

    moveToLeftOnEventTrack() {
        alert("event track left");
    }

    moveToRightOnEventTrack() {
        alert("event track right");
    }

    //throw resources from storage

    throwResourceFromWarehouse() {
        alert("warehouse");
    }

    //place worker on colony level track

    async placeWorkerOnColonyLevelTrack(level) {
        let url = level.params.url;
        const response = await fetch(url);
    }

    async showPersonalBoard(main)  {
        let url = main.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_myrmes");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }
}