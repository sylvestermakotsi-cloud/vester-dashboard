

function openWhatsApp() {
    const phone = "254180236178";
    const message = "Hello, I would like to know more about your services.";

    const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

    window.open(url, "_blank");
}
function hidemenu() {
    const menu = document.querySelector(".top nav ul");

    menu.classList.toggle("show-menu");
}
