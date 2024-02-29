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

		movingCardElement.animate(
			[
				{
					transform: "translate(" + nobleCardShape.x + "px, " + nobleCardShape.y + "px)",
					width: nobleCardShape.width + "px",
					height: nobleCardShape.height + "px",
				},
				{
					transform: "translate(" + (cardFinalPositionShape.x) + "px, " + cardFinalPositionShape.y + "px)",
				},
			],
			{
				duration: 5000,
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