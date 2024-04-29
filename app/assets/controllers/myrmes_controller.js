import { Controller } from '@hotwired/stimulus';
import workshop from "../scripts/Myrmes/workshop.js";

export default class extends Controller  {

    //place nurse

    async placeNurseOnLarvaeTrack(position) {
        let url = position.params.url;
        await fetch(url);
    }

    async placeNurseOnSoldiersTrack(position) {
        let url = position.params.url;
        await fetch(url);
    }

    async placeNurseOnWorkersTrack(position) {
        let url = position.params.url;
        await fetch(url);
    }

    async placeNurseOnWorkshop(position) {
        let url = position.params.url;
        await fetch(url);
    }

    async confirmNursesPlacement(confirm) {
        let url = confirm.params.url;
        await fetch(url);
    }

    async cancelNursesPlacement(confirm) {
        let url = confirm.params.url;
        await fetch(url);
    }

    //move on event track

    async moveToLeftOnEventTrack(event) {
        let url = event.params.url;
        await fetch(url);
    }

    async moveToRightOnEventTrack(event) {
        let url = event.params.url;
        await fetch(url);
    }

    async confirmBonus(event) {
        let url = event.params.url;
        await fetch(url);
    }

    //throw resources from storage

    async selectPlayerResourceToThrow(playerResource) {
        let url = playerResource.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_myrmes");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }

    async throwResourceFromWarehouse(resource) {
        let url = resource.params.url;
        await fetch(url);
    }

    async selectAntHillHoleToSendWorker(select) {
        let url = select.params.url;
        const response = await fetch(url);
        if (response.status === 200) {
            closeWindow();
            document.getElementById('mainBoard').innerHTML = await response.text();
        }
    }

    async placeWorkerOnAntHillHole(hole) {
        let tileId = hole.params.tileId;
        let coordX = hole.params.coordX
        let coordY = hole.params.coordY
        let movementPoints = hole.params.movementPoints
        await placeWorkerOnAnthillHole(tileId, coordX, coordY, movementPoints)
    }

    //place worker on colony level track

    async placeWorkerOnColonyLevelTrack(level) {
        let url = level.params.url;
        if (window.confirm("Confirmez vous le placement de l'ouvrière sur le niveau " + url.split('/').pop())) {
            await fetch(url);
        }
    }

    //management of worker phase

    async confirmWorkerPhase() {
        await rewindQueueWorkerPhase(queue)
        await fetch(url + "/confirm/action/workerPhase/")
    }

    async cancelWorkerPhase() {
        location.reload()
    }

    //harvest a resource

    async harvestResource(resource){
        let url = resource.params.url;
        await fetch(url)
    }

    async endHarvestPhase(endingPhase) {
        let url = endingPhase.params.url;
        await fetch(url)
    }

    // workshop actions

    async cancelAnthillHolePlacement(placement) {
        alert("Ouvrir menu de l'atelier");
    }

    async placeAnthillHole(tile) {
        let url = tile.params.url;
        await fetch(url)
    }

    async sacrificeLarvae(larvae) {
        let url = larvae.params.url;
        await fetch(url);
    }

    // ant movement

    async moveAnt(dir) {
        let direction = dir.params.dir;
        await move(direction)
        const response = await fetch("/game/myrmes/getTile/id/coords/"
            + antPosition.x + "/" + antPosition.y);
        if(response.status === 200) {
            let tileId = await response.text();
            closeSelectedBoxWindow()
            await displayBoardBoxActions(tileId)
        }
    }


    // dynamic display

    async showPersonalBoard(main)  {
        let url = main.params.url;
        const response = await fetch(url);
        let tree = document.getElementById("index_myrmes");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
    }

    async displayBoxActions(boardBox) {
        if (window.currentTileMode === 1) {
            closeSelectedBoxWindow();
            let url = boardBox.params.url;
            const response = await fetch(url);
            let tree = document.getElementById("index_myrmes");
            let placeholder = document.createElement("div");
            placeholder.innerHTML = await response.text();
            const node = placeholder.firstElementChild;
            tree.appendChild(node);
        } else if (window.currentTileMode === 3 &&
            window.goalPheromonesSelected.length <= window.goalNeededResources) {
            let pheromoneId =
                getOwnedPheromoneIdFromBoardBoxTileId(parseInt(boardBox.target.parentElement.id.split('_')[0]));

            if (pheromoneId !== null) {
                pheromoneSelectedForGoal(pheromoneId);
            }
        }
    }

    async displayObjectives(objective) {
        closeObjectivesWindow();
        let url = objective.params.url;
        document.getElementById('objectives_button').setAttribute('disabled', '');
        const response = await fetch(url);
        let tree = document.getElementById("index_myrmes");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
        window.currentTileMode = 0;
    }

    async displayBoxActionsWorkerPhase(boardBox) {
        if (window.currentTileMode === 1) {
            closeSelectedBoxWindow();
            let tileId = boardBox.params.tileId
            await displayBoardBoxActions(tileId)
        } else if (window.currentTileMode === 2 && window.clickableTilesForPlacement.includes(boardBox.target)) {
            window.currentTileMode = 0;
            let confirmButton = document.getElementById('PrepareTilePositioning')
                .querySelector('#objectPositioningValidation');
            let cancelButton = document.getElementById('PrepareTilePositioning')
                .querySelector('#objectPositioningCancel');

            for (const tile of document.querySelectorAll('.selectedTilePlacement')) {
                tile.classList.remove('selectedTilePlacement')
            }

            for (const connectedTile of boardBox.target.parentElement.dataset.connectedTiles.split("_")) {
                let tile = document.getElementById(`${connectedTile}-clickable-zone`);
                tile.parentElement.classList.add("linkedTilePlacement");
            }
            boardBox.target.parentElement.parentElement.classList.add("selectedTilePlacement");

            confirmButton.removeAttribute("disabled");
            cancelButton.removeAttribute("disabled");

            confirmButton.setAttribute("data-myrmes-tileid-param", boardBox.target.parentElement.parentElement.id);



            cancelButton.onclick = () => {
                confirmButton.setAttribute("disabled", "");
                cancelButton.setAttribute("disabled", "");
                document.querySelector('.selectedTilePlacement').classList.remove('selectedTilePlacement')
                for (const tile of document.querySelectorAll('.linkedTilePlacement')) {
                    tile.classList.remove('linkedTilePlacement')
                }

                for (const pivotPoint of clickableTilesForPlacement) {
                    pivotPoint.parentElement.parentElement.classList.add("selectedTilePlacement");
                }
                window.currentTileMode = 2;
            }
        }

    }

    async displayPlayerPersonalBoard(board) {
        let url = board.params.url;
        let open = board.params.open;
        if (open) {
            const response = await fetch(url);
            document.getElementById('playerPersonalBoard').innerHTML = await response.text();
        }

        await this.togglePlayerPersonalBoard(open);
    }

    async togglePlayerPersonalBoard(open) {
        const openedPlayerPersonalBoard = document.getElementById("openedPlayerPersonalBoard");
        const Timing = {
            duration: 750,
            iterations: 1,
        }
        if (open) {
            openedPlayerPersonalBoard.removeAttribute("hidden");
            const openingSliding = [
                {transform: "translateY(60rem)"},
                {transform: "translateY(0rem)"}
            ]
            openedPlayerPersonalBoard.animate(openingSliding, Timing);

            let personalBoard = document.getElementById('persoBoard');
            personalBoard.scrollTop = personalBoard.scrollHeight;
        } else {
            const hidden = document.createAttribute("hidden");
            openedPlayerPersonalBoard.animate(
                [
                    {transform: "translateY(0rem)"},
                    {transform: "translateY(60rem)"},
                ],
                {
                    duration: Timing.duration,
                    fill: "forwards",
                }
            ).addEventListener("finish",
                () => openedPlayerPersonalBoard.setAttributeNode(hidden));
        }
    }

    toggleWorkshopMenu(open) {
        let opened = open.params.open;
        workshop.toggleWorkshop(opened);
    }

    async activateWorkshop(placement) {
        let url = placement.params.url;
        let place = placement.params.placement;
        switch (place) {
            case 1:
                workshop.toggleWorkshop(false);
                await fetch(url);
                break;
            case 2:
                if (window.confirm("Confirmez vous l'augmentation du niveau de la fourmilière ?")) {
                    await fetch(url);
                }
                break;
            case 3:
                closeObjectivesWindow();
                closeSelectedBoxWindow();
                window.currentTileMode = 0;
                const response = await fetch(url);
                let tree = document.getElementById("index_myrmes");
                let placeholder = document.createElement("div");
                placeholder.innerHTML = await response.text();
                const node = placeholder.firstElementChild;
                tree.appendChild(node);
                break;
            case 4:
                if (window.confirm("Confirmez vous la création d'une nouvelle nourrice ?")) {
                    await fetch(url);
                }
                break;
            default:
                break;
        }
    }

    async confirmWorkshopActions(confirm) {
        await fetch(confirm.params.url);
    }

    async cleanPheromoneAction() {
        await cleanPheromone()
    }

    async displayPheromonePlacement(board)  {
        let url = board.params.url;
        let open = board.params.open;
        if (open) {
            const response = await fetch(url);
            let tree = document.getElementById("index_myrmes");
            let placeholder = document.createElement("div");
            placeholder.innerHTML = await response.text();
            const node = placeholder.firstElementChild;
            tree.appendChild(node);
        }

        await this.togglePheromonePlacement(open);
    }

    async togglePheromonePlacement(open) {
        const openedDisplayObjectPlacement = document.getElementById("openedDisplayObjectPlacement");
        const Timing = {
            duration: 750,
            iterations: 1,
        }
        if (open) {
            closeSelectedBoxWindow(false);
            openedDisplayObjectPlacement.removeAttribute("hidden");
            const openingSliding = [
                {transform: "translateX(60rem)"},
                {transform: "translateX(0rem)"}
            ]
            openedDisplayObjectPlacement.animate(openingSliding, Timing);
        } else {
            openedDisplayObjectPlacement.animate(
                [
                    {transform: "translateX(0rem)"},
                    {transform: "translateX(60rem)"},
                ],
                {
                    duration: Timing.duration,
                    fill: "forwards",
                }
            ).addEventListener("finish",
                () => {
                    openedDisplayObjectPlacement.remove();
                    window.selectedObjectId = null;
                    window.selectedOrientation = null;
                    window.currentTileMode = 1;
                });
            let tile = document.getElementsByClassName("displayedActionsTile").item(0);
            tile.classList.value = "";
            tile.classList.add("fill-[rgba(0,_0,_0,_0)]");
        }
    }

    async returnMenuPheromonePlacement(board) {
        // Menu positionnement d'une tuile
        if (document.getElementById('PrepareTilePositioning')) {
            document.getElementById('PrepareTilePositioning').remove();
            document.getElementById('ObjectOrientationList').classList.remove('hidden');

            document.getElementById('selectObjectTitle').innerText = "Orientation de la tuile"

            for (const tile of document.querySelectorAll('.linkedTilePlacement')) {
                tile.classList.remove('linkedTilePlacement')
            }
            for (const tile of document.querySelectorAll('.selectedTilePlacement')) {
                tile.classList.remove('selectedTilePlacement')
            }
            window.currentTileMode = 0;
            window.clickableTilesForPlacement = [];

        // Menu choix orientation d'une tuile
        } else if (document.getElementById('ObjectOrientationList')) {
            document.getElementById('ObjectOrientationList').remove();
            document.getElementById('ObjectSelectionList').classList.remove('hidden');
            document.getElementById('selectObjectTitle').innerText = "Selection d'une tuile à placer";
        // Menu choix d'une tuile
        } else {
            await this.displayPheromonePlacement(board);
        }
    }

    async showAvailablePlacementForTile(tileSelected) {
        let tiletype = tileSelected.params.tileid;
        let orientation = tileSelected.params.orientation;
        let url = tileSelected.params.url
            .replace("tileType", tiletype)
            .replace("orientation", orientation)
            .replace("antCoordX", antPosition.x)
            .replace("antCoordY", antPosition.y)
            .replace("cleanedTiles", getCleanedTilesString());

        const response = await fetch(url);
        if (!response.ok) {
            return;
        }
        const tiles = await response.text();

        PrepareTilePositioning(orientation);

        for (const tile of tiles.split("___")) {
            let tilesSplit = tile.split("__")
            let pivotPoint = tilesSplit[0];

            let boardBox = document.getElementById(`${pivotPoint}-clickable-zone`)
            boardBox.parentElement.classList.add("selectedTilePlacement");
            clickableTilesForPlacement.push(boardBox.firstElementChild);

            boardBox.dataset.connectedTiles = tilesSplit[1];
        }

        window.currentTileMode = 2;
    }

    async placePheromone(boardBox) {
        if (await canPlacePheromone(window.selectedObjectId, window.selectedOrientation, boardBox.params.tileid)) {
            await rewindQueueWorkerPhase(queue)
            let url = boardBox.params.url
                .replace("tileType", window.selectedObjectId)
                .replace("orientation", window.selectedOrientation)
                .replace("tileId", boardBox.params.tileid);
            await fetch(url);
            await this.togglePheromonePlacement(false);
        }
    }

    async displayStoneDirtGoal(goal) {
        let url = goal.params.url;
        const response = await fetch(url);
        if (response.ok) {
            await this.initInteractiveGoal(response);
            updateOtherRange(document.getElementById('stone-range'))
        }

    }

    async initInteractiveGoal(response) {
        workshop.toggleWorkshop(false);
        document.getElementById('objectives_home').classList.add('hidden');
        let tree = document.getElementById("objectives");
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        tree.appendChild(node);
        return node;
    }

    async displayPheromoneOrSpecialTileGoal(goal) {
        let url = goal.params.url;
        const response = await fetch(url);

        if (response.ok) {
            let node = await this.initInteractiveGoal(response);
            initTilesSelectionForGoal(node.dataset.tilesOwned, parseInt(node.dataset.neededResources));
        }

    }

    async validateGoal(goal) {
        let url = goal.params.url
        const response = await fetch(url);
        if (response.ok) {
            workshop.toggleWorkshop(false);
            closeObjectivesWindow();
        }
    }

    async validateStoneOrDirtGoal(goal) {
        const stoneRange = document.getElementById("stone-range");
        const dirtRange = document.getElementById("dirt-range");
        let url = goal.params.url
            .replace("stoneQuantity", stoneRange.value)
            .replace("dirtQuantity", dirtRange.value);
        const response = await fetch(url);
        if (response.ok) {
            closeObjectivesWindow();
        }
    }

    async validatePheromoneOrSpecialTileGoal(goal) {
        let url = goal.params.url
            .replace("pheromoneIds", window.goalPheromonesSelected.join('_'));
        const response = await fetch(url);
        if (response.ok) {
            closeObjectivesWindow();
        }
    }
}