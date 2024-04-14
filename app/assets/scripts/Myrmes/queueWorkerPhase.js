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

let spentDirt = 0;
let spentSoldier = 0;
let soldierNumber = 0;

let cleanedTiles = []
let antPosition = {x: 0, y: 0}
let movementPoints = 0


function initQueue(playerMovementPoints, antCoordX, antCoordY, idHole, playerSoldierNumber) {
    movementPoints = playerMovementPoints
    antPosition.x = antCoordX
    antPosition.y = antCoordY
    soldierNumber = playerSoldierNumber
    queue.push(actions.PLACE_ANT(idHole))
}

async function move(direction, url) {
    let coord = antPosition
    let previousCoord = {x: coord.x, y: coord.y}

    if (movementPoints === 0) {
        return;
    }

    switch (direction) {
        case directions.EAST : {
            coord.y += 2;
            break;
        }
        case directions.WEST : {
            coord.y -= 2;
            break;
        }
        case directions.NORTH_EAST : {
            coord.y += 1;
            coord.x -= 1;
            break;
        }
        case directions.NORTH_WEST : {
            coord.y -= 1;
            coord.x -= 1;
            break;
        }
        case directions.SOUTH_WEST : {
            coord.y -= 1;
            coord.x += 1;
            break;
        }
        case directions.SOUTH_EAST : {
            coord.y += 1;
            coord.x += 1;
            break;
        }
    }
    let validPositionResponse = await fetch(url + "/moveAnt/isValid/tile/" + coord.x + "/" + coord.y)
    if (await validPositionResponse.text() !== "1") {
        return;
    }

    let response = await fetch(url + "/moveAnt/neededResources/soldierNb/" + coord.x + "/" + coord.y)
    let soldier = parseInt(await response.text())
    if (soldierNumber < spentSoldier + soldier) {
        return;
    }
    spentSoldier += soldier

    let responseMovementPoints = await fetch(url + "/moveAnt/neededResources/movementPoints"
        + "/originTile/" + previousCoord.x + "/" + previousCoord.y
        + "/destinationTile/" + coord.x + "/" + coord.y
    )
    movementPoints -= parseInt(await responseMovementPoints.text())
    cleanedTiles.push(coord)
}
