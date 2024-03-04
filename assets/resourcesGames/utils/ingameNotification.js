/**
 * Class to create in-game notification
 */
class GameNotification {
	static nextId = 1;
	static notificationsContainer;


	/**
	 * Create a notification
	 * @param duration Expiration delay before a notification auto-delete
	 * @param iconName ID of an SVG located in notificationsStorage.html.twig
	 * @param message Main text of the notification
	 * @param description Secondary text of the notification
	 * @param loadingBarColor Color for the loadingBar
	 */
	constructor(duration, iconName = 'info', message, description, loadingBarColor = 'green') {
		this.id = GameNotification.nextId++;
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

	/**
	 * Exit animation and deletion of a notification
	 */
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

document.addEventListener('DOMContentLoaded', function() {
	GameNotification.notificationsContainer = document.getElementById('notificationsContainer');
	new GameNotification(10, 'info', 'Notification test', 'Ceci est un test', 'red');
});