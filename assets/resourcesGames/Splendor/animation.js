function moveNobleTile(cardId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById(playerUsername);
		let nobleCardElement = document.getElementById('noble_' + cardId);

		let nobleCardShape = nobleCardElement.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();

		let movingCardElement = nobleCardElement.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - nobleCardShape.x) ** 2 +
			(cardFinalPositionShape.y - nobleCardShape.y) ** 2);

		let xFinalPosition = (cardFinalPositionShape.x + cardFinalPositionShape.width / 2)
										- (nobleCardShape.width / 2);
		let yFinalPosition = (cardFinalPositionShape.y + cardFinalPositionShape.height / 2)
										- (nobleCardShape.height / 2);

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
					transform: "translate(" + (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) + "px, "
								+ (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) + "px)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.1,
				fill: "forwards", // Stay at the final position
			}
		).addEventListener("finish", () => {
			movingCardElement.remove();
			cardFinalPositionElement.classList.remove('invisible');
			resolve();
		});
		nobleCardElement.remove();
	}).then(() => animationQueue.executeNextInQueue());
}

function moveTakingToken(tokenId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let tokenFinalPositionElement = document.getElementById(playerUsername);
		let tokenElement = document.getElementById(tokenId);

		let tokenShape = tokenElement.getBoundingClientRect();
		let tokenFinalPositionShape = tokenFinalPositionElement.getBoundingClientRect();

		let movingTokenElement = tokenElement.cloneNode(true);
		movingTokenElement.id = 'movingtoken_' + tokenId;
		movingTokenElement.classList.add('absolute');
		animationContainer.appendChild(movingTokenElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((tokenFinalPositionShape.x - tokenShape.x) ** 2 +
			(tokenFinalPositionShape.y - tokenShape.y) ** 2);

		let xFinalPosition = (tokenFinalPositionShape.x + tokenFinalPositionShape.width / 2)
										- (tokenShape.width / 2);
		let yFinalPosition = (tokenFinalPositionShape.y + tokenFinalPositionShape.height / 2)
										- (tokenShape.height / 2);

		movingTokenElement.animate(
			[
				{
					transform: "translate(" + tokenShape.x + "px, " + tokenShape.y + "px)",
					width: tokenShape.width + "px",
					height: tokenShape.height + "px",
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: tokenShape.width + "px",
					height: tokenShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + (tokenFinalPositionShape.x + tokenFinalPositionShape.width / 2) + "px, "
						+ (tokenFinalPositionShape.y + tokenFinalPositionShape.height / 2) + "px)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.25,
			}
		).addEventListener("finish", () => {
			movingTokenElement.remove();
			tokenFinalPositionElement.classList.remove('invisible');
			resolve();
		});
	}).then(() => animationQueue.executeNextInQueue());
}



function moveReturnedToken(tokenId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let tokenFinalPositionElement = document.getElementById(playerUsername);
		let tokenElement = document.getElementById(tokenId);

		let tokenShape = tokenElement.getBoundingClientRect();
		let tokenFinalPositionShape = tokenFinalPositionElement.getBoundingClientRect();

		let movingTokenElement = tokenElement.cloneNode(true);
		movingTokenElement.id = 'movingtoken_' + tokenId;
		movingTokenElement.classList.add('absolute');
		animationContainer.appendChild(movingTokenElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((tokenFinalPositionShape.x - tokenShape.x) ** 2 +
			(tokenFinalPositionShape.y - tokenShape.y) ** 2);

		let xFinalPosition = (tokenFinalPositionShape.x + tokenFinalPositionShape.width / 2)
			- (tokenShape.width / 2);
		let yFinalPosition = (tokenFinalPositionShape.y + tokenFinalPositionShape.height / 2)
			- (tokenShape.height / 2);

		movingTokenElement.animate(
			[
				{
					transform: "translate(" + (tokenFinalPositionShape.x + tokenFinalPositionShape.width / 2) + "px, "
						+ (tokenFinalPositionShape.y + tokenFinalPositionShape.height / 2) + "px)",
					width: 0,
					height: 0,
					opacity: 0,
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: tokenShape.width + "px",
					height: tokenShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + tokenShape.x + "px, " + tokenShape.y + "px)",
					width: tokenShape.width + "px",
					height: tokenShape.height + "px",
				},
			],
			{
				duration: distance / 0.25,
				easing: 'ease-in-out',
			}
		).addEventListener("finish", () => {
			movingTokenElement.remove();
			tokenFinalPositionElement.classList.remove('invisible');
			resolve();
		});
	}).then(() => animationQueue.executeNextInQueue());
}

function moveDrawCard(cardId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById(playerUsername);
		let drawCardElement = document.getElementById('drawCards_' + cardId);

		let drawCardShape = drawCardElement.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();

		let movingCardElement = drawCardElement.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - drawCardShape.x) ** 2 +
			(cardFinalPositionShape.y - drawCardShape.y) ** 2);

		let xFinalPosition = (cardFinalPositionShape.x + cardFinalPositionShape.width / 2)
			- (drawCardShape.width / 2);
		let yFinalPosition = (cardFinalPositionShape.y + cardFinalPositionShape.height / 2)
			- (drawCardShape.height / 2);

		movingCardElement.animate(
			[
				{
					transform: "translate(" + drawCardShape.x + "px, " + drawCardShape.y + "px)",
					width: drawCardShape.width + "px",
					height: drawCardShape.height + "px",
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: drawCardShape.width + "px",
					height: drawCardShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) + "px, "
						+ (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) + "px) rotate(360deg)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.2,
				fill: "forwards", // Stay at the final position
			}
		).addEventListener("finish", () => {
			movingCardElement.remove();
			cardFinalPositionElement.classList.remove('invisible');
			resolve();
		});
	}).then(() => animationQueue.executeNextInQueue());
}

function moveDevCard(cardId, playerUsername) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById(playerUsername);
		let devCardElement = document.getElementById('image_card_' + cardId);

		let devCardShape = devCardElement.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();

		let movingCardElement = devCardElement.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - devCardShape.x) ** 2 +
			(cardFinalPositionShape.y - devCardShape.y) ** 2);

		let xFinalPosition = (cardFinalPositionShape.x + cardFinalPositionShape.width / 2)
			- (devCardShape.width / 2);
		let yFinalPosition = (cardFinalPositionShape.y + cardFinalPositionShape.height / 2)
			- (devCardShape.height / 2);

		movingCardElement.animate(
			[
				{
					transform: "translate(" + devCardShape.x + "px, " + devCardShape.y + "px)",
					width: devCardShape.width + "px",
					height: devCardShape.height + "px",
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: devCardShape.width + "px",
					height: devCardShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) + "px, "
						+ (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) + "px) rotate(360deg)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.2,
				fill: "forwards", // Stay at the final position
			}
		).addEventListener("finish", () => {
			movingCardElement.remove();
			cardFinalPositionElement.classList.remove('invisible');
			resolve();
		});
		devCardElement.remove();
	}).then(() => animationQueue.executeNextInQueue());
}

function moveDrawToDevCard(cardId) {
	animationContainer.classList.remove('hidden');
	new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById('card_' + cardId);
		let devCardElement = document.getElementById('drawCards_' + cardId);

		console.log(cardFinalPositionElement, devCardElement);

		let devCardShape = devCardElement.getBoundingClientRect();
		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();

		let movingCardElement = devCardElement.cloneNode(true);
		movingCardElement.id = 'movingcard_' + cardId;
		movingCardElement.classList.add('absolute');
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - devCardShape.x) ** 2 +
			(cardFinalPositionShape.y - devCardShape.y) ** 2);

		let xFinalPosition = (cardFinalPositionShape.x + cardFinalPositionShape.width / 2)
			- (devCardShape.width / 2);
		let yFinalPosition = (cardFinalPositionShape.y + cardFinalPositionShape.height / 2)
			- (devCardShape.height / 2);

		console.log("Before animation");

		movingCardElement.animate(
			[
				{
					transform: "translate(" + devCardShape.x + "px, " + devCardShape.y + "px)",
					width: devCardShape.width + "px",
					height: devCardShape.height + "px",
				},
				{
					transform: "translate(" + xFinalPosition + "px, " + yFinalPosition + "px)",
					width: devCardShape.width + "px",
					height: devCardShape.height + "px",
					opacity: 1,
				},
				{
					transform: "translate(" + (cardFinalPositionShape.x + cardFinalPositionShape.width / 2) + "px, "
						+ (cardFinalPositionShape.y + cardFinalPositionShape.height / 2) + "px)",
					width: 0,
					height: 0,
					opacity: 0,
				},
			],
			{
				duration: distance / 0.2,
				fill: "forwards", // Stay at the final position
			}
		).addEventListener("finish", () => {
			console.log("Animation finish");
			movingCardElement.remove();
			cardFinalPositionElement.classList.remove('invisible');
			resolve();
		});
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