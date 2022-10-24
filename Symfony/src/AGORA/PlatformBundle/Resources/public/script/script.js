var sticky;
var navbar;

// When the user scrolls the page, execute myFunction
window.onscroll = function() {myFunction()};

window.onload = function() {
navbar = document.getElementsByTagName("nav").item(0);

// Get the offset position of the navbar
sticky = navbar.offsetTop;

}

// Get the navbar

// Add the sticky class to the navbar when you reach its scroll position. Remove "sticky" when you leave the scroll position
function myFunction() {
    if (window.pageYOffset >= sticky) {
        navbar.classList.add("sticky")
    } else {
        navbar.classList.remove("sticky");
    }
}