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

function moveNobleTile(cardId, playerUsername) {
	return new Promise(resolve => {
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
			resolve();
		});
		nobleCardElement.remove();
	});
}

function moveTakingToken(tokenId, playerUsername) {
	return new Promise(resolve => {
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
	});
}



function moveReturnedToken(tokenId, playerUsername) {
	return new Promise(resolve => {
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
			resolve();
		});
	});
}

function moveDrawCard(cardId, playerUsername, face) {
	return new Promise(resolve => {
		let cardFinalPositionElement = document.getElementById(playerUsername);
		let drawCardElement = document.getElementById('drawCards_' + cardId + '_' + face);

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
			resolve();
		});
	});
}

function moveDevCard(cardId, playerUsername) {
	return new Promise(resolve => {
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
			resolve();
		});
		devCardElement.remove();
	});
}

function moveDrawToDevCard(drawCardId, devCardId, face) {
	return new Promise(resolve => {
		console.log(face)
		let cardFinalPositionElement = document.getElementById('image_card_' + devCardId);
		let drawCardElement = document.getElementById('drawCards_' + drawCardId + '_' + face);

		console.log(cardFinalPositionElement, drawCardElement);

		let cardFinalPositionShape = cardFinalPositionElement.getBoundingClientRect();
		let discardCardShape = drawCardElement.getBoundingClientRect();

		console.log(cardFinalPositionShape, discardCardShape);

		let movingCardElement = document.getElementById('flip_card').cloneNode(true);
		movingCardElement.id = 'movingcard_' + devCardId + '_' + face;
		movingCardElement.classList.remove(face + ':invisible');

		let frontImage = drawCardElement.cloneNode(true);
		let backImage = cardFinalPositionElement.cloneNode(true);
		frontImage.classList.add('size-full');
		backImage.classList.add('size-full');

		movingCardElement.querySelector('.spl-front').append(frontImage)
		movingCardElement.querySelector('.spl-back').append(backImage)
		animationContainer.appendChild(movingCardElement);

		// Usefull to set a duration for the animation equal for every distance the translating movement will do
		let distance = Math.sqrt((cardFinalPositionShape.x - discardCardShape.x) ** 2 +
			(cardFinalPositionShape.y - discardCardShape.y) ** 2);

		movingCardElement.animate(
			[
				{
					transform: "translate(" + discardCardShape.x + "px, " + discardCardShape.y + "px)",
					width: discardCardShape.width + "px",
					height: discardCardShape.height + "px",
				},
				{
					transform: "translate(" + cardFinalPositionShape.x + "px, " + cardFinalPositionShape.y + "px)",
					width: cardFinalPositionShape.width + "px",
					height: discardCardShape.height + "px",
				},
			],
			{
				duration: distance / 0.3,
				fill: "forwards", // Stay at the final position
			}
		).addEventListener("finish", () => {
			console.log("Animation 1 finish");
			setTimeout(function () {
				console.log("Animation 2 finish");
				movingCardElement.querySelector('.spl-card').animate(
					[
						{transform : "rotateY(0deg)"},
						{transform : "rotateY(180deg)"},
					],
					{
						duration: 500,
						fill: "forwards", // Stay at the final position
					}
				).addEventListener("finish", () => {
					cardFinalPositionElement.classList.remove(face + ':invisible');
					movingCardElement.remove();
					setTimeout(() => resolve(), 1000);
				});
			}, 500)
		});
	});
}

let animationQueue = new AnimationQueue();
let animationContainer = document.getElementById('animationContainer');

animationQueue.executeNextInQueue();