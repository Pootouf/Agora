/**
 * Show the end game screen
 * @param {string}winner name of the game's winner
 * @param {string}player name of the current player
 */
function gameFinished(winner, player) {
	if (winner === null) {
		document.getElementById('winner').textContent = 'Match nul ! 🤝';
	} else if (winner === player) {
		document.getElementById('winner').textContent = 'Bravo ! 👏 Vous avez gagné la partie';
	} else if (player) {
		document.getElementById('winner').textContent = 'Dommage ! 😖 Vous avez perdu la partie';
	} else {
		document.getElementById('winner').textContent = winner + " a remporté la partie 👏";
	}
	showEndgame();
}

/**
 * Animates the show up of the endGame displayer
 */
function showEndgame() {
	new Promise(resolve => {
		let endGameScreen = document.getElementById('endGameScreen');
		document.body.style.overflow = 'hidden';
		endGameScreen.firstElementChild.animate(
			[
				{transform: "translateY(-30px)", opacity: 0},
				{transform: "translateY(0px)", opacity: 1},
			],
			{
				duration: 2000,
				easing: "ease",
				fill: "forwards",
			}
		).finished.then(() => resolve())
		endGameScreen.classList.remove('hidden');
	}).then(() => animationQueue.executeNextInQueue())
}

/**
 * Animate the movement of a villager on a personnal board
 * @param playerid              id of the player who own the personnal board
 * @param originTileId          id of the tile where the villager begin his move
 * @param tileTargetedId        id of the tile where the villager end his move
 * @returns {Promise<unknown>}  Promise of the animation
 */
function moveVillagerOnPersonnalBoard(playerid, originTileId, tileTargetedId) {
	return new Promise(resolve => {
		let villagerOriginElement =
			document.getElementById(playerid + '_tile_' + originTileId).querySelector('.villager');
		let targetedTileElement = document.getElementById(playerid + '_tile_' + tileTargetedId);

		let movingElement = villagerOriginElement.cloneNode(true);
		movingElement.id = 'movingvillager';
		movingElement.classList.add('absolute');
		animationContainer.appendChild(movingElement);

		let destinationElement = villagerOriginElement.cloneNode(true);
		destinationElement.classList.add('invisible');
		targetedTileElement.appendChild(destinationElement);
		destinationElement.id = 'targetedvillagerposition';

		let villagerOriginShape = villagerOriginElement.getBoundingClientRect();
		let destinationShape = destinationElement.getBoundingClientRect();
		villagerOriginElement.classList.add('invisible');
		movingElement.animate(
			[
				{
					transform: "translate(" + villagerOriginShape.x + "px, " + villagerOriginShape.y + "px)",
					width: villagerOriginShape.width + "px",
					height: villagerOriginShape.height + "px",
				},
				{width: villagerOriginShape.width * 1.2 + "px", height: villagerOriginShape.height * 1.2 + "px"},
				{
					transform: "translate(" + destinationShape.x + "px, " + destinationShape.y + "px)",
					width: destinationShape.width + "px",
					height: destinationShape.height + "px",
				},
			],
			{
				duration: 3000,
				fill: "forwards",
			}
		).addEventListener("finish", () => {
			destinationElement.classList.remove('invisible');
			movingElement.remove();
			resolve();
		});
	});
}

let animationQueue = new AnimationQueue();
let animationContainer = document.getElementById('animationContainer');

animationQueue.executeNextInQueue();