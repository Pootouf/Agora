function moveNobleTile(cardId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById(playerUsername);
		let nobleCardElement = document.getElementById('noble_' + cardId);

		let nobleCardShape = nobleCardElement.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();



		console.log(cardFinalPositionShape)

		let movingCardElement = nobleCardElement.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - nobleCardShape.x) ** 2 +
			(cardFinalPositionShape.y - nobleCardShape.y) ** 2);

		let xFinalPosition = (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) - (nobleCardShape.width / 2);
		let yFinalPosition = (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) - (nobleCardShape.height / 2);

		movingCardElement.animate(
			[
				{
					transform: "translate(" + nobleCardShape.x + "px, " + nobleCardShape.y + "px)",
					width: nobleCardShape.width + "px",
					height: nobleCardShape.height + "px",
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: nobleCardShape.width + "px",
					height: nobleCardShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) + "px, " + (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) + "px)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.1,
				fill: "forwards", // Reste a la position final
			}
		).addEventListener("finish", () => {
			movingCardElement.remove();
			//nobleCardElement.remove();
			console.log('finish')
			cardFinalPositionElement.classList.remove('invisible');
			resolve();
		});
		nobleCardElement.remove();
	}).then(() => animationQueue.executeNextInQueue());
}

let animationQueue = new AnimationQueue();
let animationContainer = document.getElementById('animationContainer');

/*window.addEventListener('load', function () {
	let leaderboardContainer = document.getElementById('leaderboard');
	if (leaderboardContainer) {
		applyScoresStyle(Array.from(leaderboardContainer.children));
	}
});*/

animationQueue.executeNextInQueue();