import { Controller } from '@hotwired/stimulus';

export default class extends Controller  {

    //place nurse

    placeNurseOnLarvaeTrack() {
        alert("larva");
    }

    placeNurseOnSoldiersTrack() {
        alert("soldier");
    }

    placeNurseOnWorkersTrack() {
        alert("worker");
    }

    placeNurseOnWorkshop() {
        alert("workshop");
    }

    //move on event track

    async moveToLeftOnEventTrack(event) {
        let url = event.params.url;
        const response = await fetch(url);
    }

    async moveToRightOnEventTrack(event) {
        let url = event.params.url;
        const response = await fetch(url);
    }

    //throw resources from storage

    throwResourceFromWarehouse() {
        alert("warehouse");
    }

    //place worker on colony level track

    placeWorkerOnLevel0() {
        alert("lvl 0");
    }

    placeWorkerOnLevel1() {
        alert("lvl 1");
    }

    placeWorkerOnLevel2() {
        alert("lvl 2");
    }

    placeWorkerOnLevel3() {
        alert("lvl 3");
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