document.addEventListener('DOMContentLoaded', function() {
	let notificationsContainer = document.getElementById('notificationsContainer');
	let testNotif = new IngameNotification(50, 'info', 'Joueur suivant !', 'C\'est au tour de Yohann', 'red')
});

class IngameNotification {
	static nextId = 1;
	constructor(duration, iconName = 'info', message, description, loadingBarColor = 'green') {
		this.id = IngameNotification.nextId++;
		this.element = document.getElementById('notif_template').cloneNode(true);
		this.element.id = this.id;

		let icon = document.getElementById('svg_' + iconName).cloneNode(true);
		this.element.querySelector('.svgContainer').appendChild(icon)

		this.element.querySelector('.notifMessage').textContent = message;
		this.element.querySelector('.notifDescription').textContent = description;

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
		this.loadingBar.classList.add('bg-' + loadingBarColor + '-500')
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

