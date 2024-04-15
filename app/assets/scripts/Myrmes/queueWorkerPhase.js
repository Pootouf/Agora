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

//identifier of the ant
let antId = 0

/**
 * initQueue: init the action queue for the worker phase with the selected base resources and positions
 * @param baseUrl
 * @param playerMovementPoints
 * @param antCoordX
 * @param antCoordY
 * @param idHole
 * @param playerSoldierNumber
 * @param playerDirtNumber
 * @param workerId
 */
function initQueue(baseUrl,
                   playerMovementPoints,
                   antCoordX,
                   antCoordY,
                   idHole,
                   playerSoldierNumber,
                   playerDirtNumber,
                   workerId) {
    url = baseUrl
    movementPoints = playerMovementPoints
    antPosition.x = antCoordX
    antPosition.y = antCoordY
    soldierNumber = playerSoldierNumber
    dirtNumber = playerDirtNumber
    antId = workerId
    queue.push(actions.PLACE_ANT(idHole))
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
    cleanedTiles.push(coord)
    antPosition = coord
    queue.push(action)
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
 * manageSoldierOfPlayer: remove the needed number of soldier for the ant to move on the selected tile
 * @throws InvalidSoldierNumberException if the player can't afford the cost
 * @param coord
 * @returns {Promise<void>}
 */
async function manageSoldierOfPlayer(coord) {
    let response = await fetch(url + "/moveAnt/neededResources/soldierNb/" + coord.x + "/" + coord.y)
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
    let response = await fetch(url + "/moveAnt/getPheromoneTiles/coords/givenTile"
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
        case directions.SOUTH_EAST:
            return directions.SOUTH_EAST;
        default:
            return null;
    }
}

async function rewindQueueWorkerPhase(queue) {
    while (!queue.isEmpty()) {
        const action = queue.shift();
        let routeUrl = url;
        switch (action) {
            case action.startsWith('MOVE'):
                const dir = directionByAction(action);
                routeUrl += '/moveAnt/' + antId + '/direction/' + dir;
                await fetch(routeUrl);
                break
            case actions.CLEAN_PHEROMONE:
                const pheromoneCoord = cleanedTiles.shift();
                routeUrl += '/moveAnt/clean/pheromone/' + pheromoneCoord.x + '/' + pheromoneCoord.y;
                await fetch(routeUrl);
                break
            case actions.PLACE_ANT:
                routeUrl += '/placeWorkerOnAntHillHole/' + action.split('_').pop();
                await fetch(routeUrl);
                break
            default:
                break
        }
    }
}