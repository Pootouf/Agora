//     ------------------------- Notification Popup --------------------------
function notify($content, $type) {
    if (!("Notification" in window)) {
        // Check if the browser supports notifications
        alert("Ce navigateur ne supporte pas les notifications Desktop");
    } else if (Notification.permission === "granted") {
        // Check whether notification permissions have already been granted;
        // if so, create a notification
        const notification = new Notification($type+ ": "+ $content);
        notification.onclick = function() {
            // Perform action when notification is clicked
            // For example, redirect to a specific URL
            window.location.href = "/";
        };
    } else if (Notification.permission !== "denied") {
        // We need to ask the user for permission
        Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                const notification = new Notification($type+ ": "+ $content);
                notification.onclick = function() {
                    window.location.href = "/";
                };
            }
        });
    }
}
// -------------------------Refresh Notification frame---------------------------
function updateNotificationFrame($data) {
    const notificationDiv = document.createElement('div');
    notificationDiv.classList.add('notification');
    notificationDiv.innerHTML = `
        <p>${$data.content}</p>
        <p> -- Date: ${new Date($data.date).toISOString()}</p>
    `;
    const notificationFrame = document.getElementById('notification-frame');
    notificationFrame.insertBefore(notificationDiv, notificationFrame.firstChild);
}

//--------------------------Add Notification------------------------------
function addNotification($data,$type){
    updateNotificationFrame($data);
    notify($data.content, $type);
}