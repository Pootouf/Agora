/**
 FIFO queue for animations
 */
class AnimationQueue {
	waitingQueue = [];

	/**
	 * Adds a function to the animation queue.
	 *
	 * @param {Function} fct The function which execute an animation to add to the queue.
	 */
	addToQueue(fct) {
		this.waitingQueue.push(fct);
	}

	/**
	 * Executes the next function in the animation queue.
	 */
	executeNextInQueue() {
		if (this.waitingQueue.length > 0) {
			const nextAnim = this.waitingQueue.shift();
			animationContainer.classList.remove('hidden');
			nextAnim();
		} else {
			setTimeout(this.executeNextInQueue.bind(this), 500);
			animationContainer.classList.add('hidden');
		}
	}
}