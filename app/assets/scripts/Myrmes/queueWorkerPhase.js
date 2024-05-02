/**
 * queueWorkerPhase: allow the player to use a system of queue of actions to confirm or cancel his actions on the
 *                   worker phase
 */


let queue = createDoubleStackQueue()

const actions = {
    PLACE_ANT(idHole) {
        return 'HOLE_' + idHole
    },
    MOVE_WEST: 'MOVE_WEST',
    MOVE_EAST: 'MOVE_EAST',
    MOVE_NORTH_WEST: 'MOVE_NORTH_WEST',
    MOVE_NORTH_EAST: 'MOVE_NORTH_EAST',
    MOVE_SOUTH_WEST: 'MOVE_SOUTH_WEST',
    MOVE_SOUTH_EAST: 'MOVE_SOUTH_EAST',
    CLEAN_PHEROMONE: 'CLEAN_PHEROMONE'
}

const directions = {
    NORTH_WEST: 1,
    NORTH_EAST: 2,
    EAST : 3,
    SOUTH_EAST : 4,
    SOUTH_WEST : 5,
    WEST : 6
}

//Base url for fetching information
let url = ""

let spentDirt = 0;
let spentSoldier = 0;

//Initial number of soldier of the player
let soldierNumber = 0;
//Initial number of dirt of the player
let dirtNumber = 0;

//Tiles that the player have cleaned
let cleanedTiles = []

//Initial position of the ant, will move with action of the player
let antPosition = {x: 0, y: 0}

//Initial movement points of the player
let movementPoints = 0


/**
 * initQueue: init the action queue for the worker phase with the selected base resources and positions
 * @param baseUrl
 * @param playerSoldierNumber
 * @param playerDirtNumber
 */
function initQueue(baseUrl,
                   playerSoldierNumber,
                   playerDirtNumber) {
    url = baseUrl
    soldierNumber = playerSoldierNumber
    dirtNumber = playerDirtNumber
    movementPoints = 0
    antPosition = {x: 0, y: 0}
    cleanedTiles = []
    spentDirt = 0
    spentSoldier = 0
}

/**
 * move: make the ant of the player move in the selected direction
 * @param direction
 * @returns {Promise<void>}
 */
async function move(direction) {
    let coord = {x: antPosition.x, y: antPosition.y}
    let previousCoord = {x: coord.x, y: coord.y}

    if (movementPoints === 0) {
        return;
    }
    let action = calculateNewCoordinateWithSelectedMovement(direction, coord);
    if (!await isValidPositionForAnt(coord)) {
        return;
    }
    try {
        await manageSoldierOfPlayer(coord)
    } catch (InvalidSoldierNumberException) {
        return;
    }
    await manageMovementPointsOfPlayer(previousCoord, coord)
    antPosition = coord
    queue.push(action)
    await refreshMainBoard()
}

/**
 * cleanPheromone: make the player clean the pheromone at the position of the ant if possible
 * @returns {Promise<void>}
 */
async function cleanPheromone() {
    if (spentDirt >= dirtNumber) {
        return;
    }
    if (!await canPlayerCleanPheromone()) {
        return;
    }

    await managePheromoneCleanedTiles()

    spentDirt += 1
    queue.push(actions.CLEAN_PHEROMONE)
    await refreshMainBoard()
}

/**
 * placeWorkerOnAnthillHole : place the worker on the specified tileId
 * @param tileId
 * @param coordX
 * @param coordY
 * @param playerMovementPoints
 */
async function placeWorkerOnAnthillHole(tileId, coordX, coordY, playerMovementPoints) {
    queue.push(actions.PLACE_ANT(tileId))
    antPosition.x = coordX
    antPosition.y = coordY
    movementPoints = playerMovementPoints
    await refreshMainBoard()
}

/**
 * calculateNewCoordinateWithSelectedMovement: modify the coord object depending on the selected direction, and return
 *                                             the associated action
 * @param direction
 * @param coord
 * @returns {string}
 */
function calculateNewCoordinateWithSelectedMovement(direction, coord) {
    let action
    switch (direction) {
        case directions.EAST : {
            action = actions.MOVE_EAST
            coord.y += 2;
            break;
        }
        case directions.WEST : {
            action = actions.MOVE_WEST
            coord.y -= 2;
            break;
        }
        case directions.NORTH_EAST : {
            action = actions.MOVE_NORTH_EAST
            coord.y += 1;
            coord.x -= 1;
            break;
        }
        case directions.NORTH_WEST : {
            action = actions.MOVE_NORTH_WEST
            coord.y -= 1;
            coord.x -= 1;
            break;
        }
        case directions.SOUTH_WEST : {
            action = actions.MOVE_SOUTH_WEST
            coord.y -= 1;
            coord.x += 1;
            break;
        }
        case directions.SOUTH_EAST : {
            action = actions.MOVE_SOUTH_EAST
            coord.y += 1;
            coord.x += 1;
            break;
        }
    }
    return action
}


/**
 * isValidPositionForAnt: return true if the selected position is a valid position for the ant
 * @param coord
 * @returns {Promise<boolean>}
 */
async function isValidPositionForAnt(coord) {
    let validPositionResponse = await fetch(url + "/moveAnt/isValid/tile/" + coord.x + "/" + coord.y)
    return await validPositionResponse.text() === "1";
}

/**
 * managePreyOnTile: add the coord object in the cleaned tiles if there is a prey on tile at coord coordinates
 * @param coord
 * @returns {Promise<void>}
 */
async function managePreyOnTile(coord) {
    let response = await fetch(url + "/moveAnt/isPrey/"
        + coord.x + "/" + coord.y + "/" + getCleanedTilesString())
    let bool = parseInt(await response.text())
    if (bool === 1) {
        cleanedTiles.push(coord)
    }
}

/**
 * manageSoldierOfPlayer: remove the needed number of soldier for the ant to move on the selected tile
 * @throws InvalidSoldierNumberException if the player can't afford the cost
 * @param coord
 * @returns {Promise<void>}
 */
async function manageSoldierOfPlayer(coord) {
    await managePreyOnTile(coord);
    let response = await fetch(url + "/moveAnt/neededResources/soldierNb/"
        + coord.x + "/" + coord.y + "/" + getCleanedTilesString())
    let soldier = parseInt(await response.text())
    if (soldierNumber < spentSoldier + soldier) {
        throw new InvalidSoldierNumberException()
    }
    spentSoldier += soldier
}

/**
 * manageMovementPointsOfPlayer: manage the movement points of the player by decreasing them if needed
 * @param previousCoord
 * @param actualCoord
 * @returns {Promise<void>}
 */
async function manageMovementPointsOfPlayer(previousCoord, actualCoord) {
    let responseMovementPoints = await fetch(url + "/moveAnt/neededResources/movementPoints"
        + "/originTile/" + previousCoord.x + "/" + previousCoord.y
        + "/destinationTile/" + actualCoord.x + "/" + actualCoord.y
    )
    movementPoints -= parseInt(await responseMovementPoints.text())
}


/**
 * canPlayerCleanPheromone: return true if the player can clean the pheromone
 * @returns {Promise<boolean>}
 */
async function canPlayerCleanPheromone() {
    let response = await fetch(url + "/moveAnt/canClean/pheromone/"
        + antPosition.x + "/" + antPosition.y + "/" + (dirtNumber - spentDirt));
    return await response.text() === "1";
}

/**
 * managePheromoneCleanedTiles: add the tiles of the cleaned pheromones into the cleaned tiles
 * @returns {Promise<void>}
 */
async function managePheromoneCleanedTiles() {
    let response = await fetch(url + "/moveAnt/getPheromoneTiles/coords/givenTile/"
        + antPosition.x + "/" + antPosition.y)
    let stringCoords = await response.text()
    let coordTiles = stringCoords.split(" ")
    coordTiles.forEach(
        (coord) => {
            let [x, y] = coord.split("_")
            cleanedTiles.push({x: parseInt(x), y: parseInt(y)})
        }
    )
}

/**
 * refreshMainBoard : refresh the main board based on the state of the worker phase
 * @returns {Promise<void>}
 */
async function refreshMainBoard() {
    const cleanedTilesString = getCleanedTilesString()
    const response = await fetch(url + "/workerPhase/mainBoard/" + antPosition.x + "/"
    + antPosition.y + "/" + movementPoints + "/" + cleanedTilesString);
    if (response.status === 200) {
        updateMainBoardAndDisplayPheromoneBorders(await response.text())
    }
}

/**
 * displayBoardBoxActions : display the board box actions based on the state of the tile given during worker phase
 * @param tileId
 * @returns {Promise<void>}
 */
async function displayBoardBoxActions(tileId) {
    const cleanedTilesString = getCleanedTilesString()
    const response = await fetch(url + "/workerPhase/displayBoardBoxActions/"
    + antPosition.x + "/" + antPosition.y + "/" + movementPoints + "/" + tileId + "/" + cleanedTilesString);
    if (response.status === 200) {
        closeSelectedBoxWindow();
        let placeholder = document.createElement("div");
        placeholder.innerHTML = await response.text();
        const node = placeholder.firstElementChild;
        document.getElementById('index_myrmes').appendChild(node);
    }
}

/**
 * getCleanedTilesString : format the cleaned tiles array to fetch route
 * @returns {string}
 */
function getCleanedTilesString() {
    return "[" + cleanedTiles.map(
        function ({x, y}) {
            return x + "_" + y
        }
    ).join(" ") + "]"
}

/**
 * directionByAction: take an action and return a direction if action was a movement else return null
 * @param action
 * @returns {number|null}
 */
function directionByAction(action) {
    switch (action) {
        case actions.MOVE_WEST:
            return directions.WEST;
        case actions.MOVE_NORTH_WEST:
            return directions.NORTH_WEST;
        case actions.MOVE_SOUTH_WEST:
            return directions.SOUTH_WEST;
        case actions.MOVE_EAST:
            return directions.EAST;
        case actions.MOVE_NORTH_EAST:
            return directions.NORTH_EAST;
        case actions.MOVE_SOUTH_EAST:
            return directions.SOUTH_EAST;
        default:
            return null;
    }
}

/**
 * rewindQueueWorkerPhase take a queue and go through it and call appropriate controller routes for actions.
 * @param queue
 * @returns {Promise<void>}
 */
async function rewindQueueWorkerPhase(queue) {
    while (!queue.isEmpty()) {
        const action = queue.shift();
        let routeUrl = url;
        if (action.startsWith('MOVE')) {
            const dir = directionByAction(action);
            routeUrl += '/moveAnt/direction/' + dir;
            await fetch(routeUrl);
        } else if (action.startsWith('HOLE')) {
            routeUrl += '/placeWorkerOnAntHillHole/' + action.split('_').pop();
            await fetch(routeUrl);
        } else {
            const pheromoneCoord = cleanedTiles.shift();
            routeUrl += '/moveAnt/clean/pheromone/' + pheromoneCoord.x + '/' + pheromoneCoord.y;
            await fetch(routeUrl);
        }
    }
}

async function canPlacePheromone(type, orientation) {
    const cleanedTilesString = getCleanedTilesString()
    const response = await fetch(url + "/canPlace/pheromone/" + antPosition.x
    + "/" + antPosition.y + "/" + type + "/" + orientation + "/" + cleanedTilesString);
    return response.status === 200 && await response.text() === "1";

}

/**
 * updateMainBoardAndDisplayPheromoneBorders: refresh the main board by replacing his content with data
 *                                            and execute all the border functions of the main board
 *                                            to make pheromone borders visible only at the real borders
 * @param data
 */
function updateMainBoardAndDisplayPheromoneBorders(data) {
    window.borderFunctions = null;
    const mainBoard = document.getElementById('mainBoard');
    let temp = document.createRange().createContextualFragment(data)
    mainBoard.innerHTML = "";
    mainBoard.appendChild(temp)
    window.borderFunctions.forEach(
        (func) => func()
    )
}