document.addEventListener('DOMContentLoaded', function() {
	let notificationsContainer = document.getElementById('notificationsContainer');
	let testNotif = new IngameNotification(5)
});

class IngameNotification {
	static nextId = 1;
	constructor(duration) {
		this.id = IngameNotification.nextId++;
		this.element = document.getElementById('notif_turn').cloneNode(true);
		this.element.id = this.id;
		this.element.animate(
			[
				{opacity: 0},
				{opacity: 1},
			],
			{
				fill: "forwards",
				duration: 200
			}
		)
		this.loadingBar = this.element.querySelector('.loadingElement')
		notificationsContainer.appendChild(this.element);
		this.animation = this.loadingBar.animate(
			[
				{ width: "0%" },
				{ width: "100%" }
			],
			{
				fill: "forwards",
				duration: duration * 1000
			}
		);
		this.animation.finished.then(() => {
			this.#delete();
		});

		this.element.querySelector('.button-notif').addEventListener('click', () => {
			this.animation.pause();
			this.#delete();
		});

	}

	#delete() {
		this.element.animate(
			[
				{opacity: 1},
				{opacity: 0},
			],
			{
				fill: "forwards",
				duration: 200
			}
		).finished.then(() => {
			this.element.remove();
			delete this.element;
			delete this.loadingBar;
		})
	}
}

