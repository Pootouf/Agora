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
            window.location.href = "/dashboard/profile";
        };
    } else if (Notification.permission !== "denied") {
        // We need to ask the user for permission
        Notification.requestPermission().then((permission) => {
            // If the user accepts, let's create a notification
            if (permission === "granted") {
                const notification = new Notification($type+ ": "+ $content);
                notification.onclick = function() {
                    window.location.href = "/dashboard/profile";
                };
            }
        });
    }
}