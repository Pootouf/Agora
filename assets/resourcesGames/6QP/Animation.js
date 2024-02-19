/**
 * Updates the scoreElement of a player.
 *
 * @param {String[]} player array containing player's ID and score.
 */
function updateUserScore(player) {
	let scoreElement = document.getElementById(player[0]);
	if (scoreElement) {
		let landscapeScore = document.getElementById('l_' + player[0] + '_points');
		landscapeScore.dataset.score = player[1];
		if (landscapeScore) {
			landscapeScore.getElementsByTagName('p').item(0).innerText = player[1];
			if (player[1] > 1) {
				landscapeScore.getElementsByTagName('p').item(1).innerText = "points";
			}
		}
		let portraitScore = document.getElementById('p_' + player[0] + '_points');
		if (portraitScore) {
			portraitScore.getElementsByTagName('p').item(0).innerText = player[1];
			if (player[1] > 1) {
				portraitScore.getElementsByTagName('p').item(1).innerText = 'points';
			}
		}
	}
	updateLeaderboard().then(() => animationQueue.executeNextInQueue());
}

/**
 * Updates the leaderboard based on the current scores.
 *
 */
async function updateLeaderboard() {

	let leaderboardContainer = document.getElementById('leaderboard');
	if (leaderboardContainer) {
		let leaderboardItems = Array.from(leaderboardContainer.children);
		let positionMap = new Map();
		let newPositionMap = new Map();

		leaderboardItems.forEach((item) =>
			positionMap.set(item.dataset.origin, item.getBoundingClientRect()));

		leaderboardItems.sort(function (a, b) {
			let scoreA = parseInt(document.getElementById('l_' + a.id + '_points').dataset.score);
			let scoreB = parseInt(document.getElementById('l_' + b.id + '_points').dataset.score);
			return scoreA - scoreB;
		});


		leaderboardItems.forEach((item, index) => {
			newPositionMap.set(item, index);
			item.dataset.origin = index.toString();
		});
		applyScoresStyle(leaderboardItems);
		await animateLeaderboard(leaderboardItems, positionMap, newPositionMap)
	}
}

/**
 * Animates the leaderboard movement.
 *
 * @param {Element[]}           leaderboardItems Array of leaderboard item elements.
 * @param {Map<String, DOMRect>}    positionMap Map containing initial positions of leaderboard items.
 * @param {Map<Element, number>}    newPositionMap Map containing new positions of leaderboard items.
 */
async function animateLeaderboard(leaderboardItems, positionMap, newPositionMap) {
	await new Promise(resolve => {
		leaderboardItems.forEach((item) => {
			let initialPosition = item.getBoundingClientRect();
			let finalPosition = positionMap.get(newPositionMap.get(item).toString());
			let currentY = initialPosition.y + parseInt(item.dataset.origingap);
			let finalY = finalPosition.y - currentY;
			item.dataset.origingap = finalY.toString()

			item.animate(
				[
					{transform: `translateY(${currentY}px)`},
					{transform: `translateY(${finalY}px)`}
				],
				{
					duration: 1000,
					easing: 'ease-out',
					fill: 'forwards'
				}
			)
		});
		resolve()
	});
}

/**
 * Applies styles to leaderboard items based on scores' position
 *
 * @param {Element[]} leaderboardItems Array of leaderboard item elements.
 */
function applyScoresStyle(leaderboardItems) {
	let first = document.getElementById(
		'l_' + leaderboardItems[0].id + '_points').dataset.score;
	let last = document.getElementById('l_' +
		leaderboardItems[leaderboardItems.length - 1].id + '_points').dataset.score;

	leaderboardItems.forEach((item) => {
		if (document.getElementById('l_' + item.id + '_points').dataset.score === last) {
			item.classList.remove('score-white', 'score-gold');
			item.classList.add('score-red');
		} else if (document.getElementById('l_' + item.id + '_points').dataset.score === first) {
			item.classList.remove('score-red', 'score-white');
			item.classList.add('score-gold');
		} else {
			item.classList.remove('score-red', 'score-gold');
			item.classList.add('score-white');
		}

	});
}

/**
 * Resets the order of scores in the leaderboard.
 */
function resetRanking() {
	let leaderboardContainer = document.getElementById('leaderboard');
	if (leaderboardContainer) {
		let leaderboardItems = Array.from(leaderboardContainer.children);
		leaderboardItems.sort(function (a, b) {
			let scoreA = parseInt(document.getElementById('l_' + a.id + '_points').dataset.score);
			let scoreB = parseInt(document.getElementById('l_' + b.id + '_points').dataset.score);
			return scoreA - scoreB;
		});


		leaderboardItems.forEach((item, index) => {
			leaderboardContainer.removeChild(item);
			item.style.transform = `translateY(0px)`;
			item.dataset.origingap = "0";
			item.dataset.origin = index.toString();

		});
		leaderboardItems.forEach(item => leaderboardContainer.appendChild(item));
	}
}

/**
 * Animates a row to translate out of the screen
 *
 * @param {string} rowid The ID of the row to translate.
 */
function translateRow(rowid) {
	new Promise(resolve => {
		let row = document.getElementById(rowid);
		row.animate(
			[
				{transform: "translateX(0px)", opacity: 1},
				{transform: "translateX(700px) scale(0.5)", opacity: 0},
			],
			{
				duration: 2000,
				iterations: 1,
				fill: "forwards",
			},
		).addEventListener("finish", () => {
			resolve()
		})
	}).then(() => animationQueue.executeNextInQueue());

}

/**
 * Moves a chosen card element to the mainBoard.
 *
 * @param {string} cardId The ID of the card to move.
 */
function moveChosenCard(cardId) {
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById('image_' + cardId);
		let cardElementInChosenCard = document.getElementById(cardId).firstElementChild;

		let chosenCardShape = cardElementInChosenCard.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();

		let movingCardElement = cardElementInChosenCard.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);
		movingCardElement.height = chosenCardShape.height;
		movingCardElement.width = chosenCardShape.width;
		animationContainer.classList.remove('hidden');

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - chosenCardShape.x) ** 2 +
			(cardFinalPositionShape.y - chosenCardShape.y) ** 2);

		movingCardElement.animate(
			[
				{
					transform: "translate(" + chosenCardShape.x + "px, " + chosenCardShape.y + "px)",
					width: chosenCardShape.width + "px",
					height: chosenCardShape.height + "px",
				},
				{width: chosenCardShape.width * 1.5 + "px", height: chosenCardShape.height * 1.5 + "px"},
				{
					transform: "translate(" + cardFinalPositionShape.x + "px, " + cardFinalPositionShape.y + "px)",
					width: cardFinalPositionShape.width + "px",
					height: cardFinalPositionShape.height + "px"
				},
			],
			{
				duration: distance / 0.3,
				iterations: 1,
				fill: "forwards", // Reste a la positon final
			}
		).addEventListener("finish", () => {
			movingCardElement.remove();
			cardElementInChosenCard.remove();
			cardFinalPositionElement.classList.remove('invisible');
			animationContainer.classList.add('hidden');
			resolve();
		});
		cardElementInChosenCard.remove()
	}).then(() => animationQueue.executeNextInQueue());
}

let animationQueue = new AnimationQueue();
let animationContainer = document.getElementById('animationContainer');
window.addEventListener('load', function () {
	let leaderboardContainer = document.getElementById('leaderboard');
	if (leaderboardContainer) {
		applyScoresStyle(Array.from(leaderboardContainer.children));
	}
});

let timerForReset;
window.onresize = function () {
	clearTimeout(timerForReset);
	timerForReset = setTimeout(resetRanking, 100);
};


animationQueue.executeNextInQueue();