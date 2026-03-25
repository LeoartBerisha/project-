const menuToggle = document.getElementById("menu-toggle");
const navLinks = document.querySelector(".nav-links");
const links = document.querySelectorAll(".nav-links a");


menuToggle.addEventListener("change", () => {
    if (menuToggle.checked) {
        navLinks.classList.add("open");
    } else {
        navLinks.classList.remove("open");
    }
});


links.forEach(link => {
    link.addEventListener("click", () => {
        menuToggle.checked = false;
        navLinks.classList.remove("open");
    });
});
