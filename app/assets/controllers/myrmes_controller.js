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
}