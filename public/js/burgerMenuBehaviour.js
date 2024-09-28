
const burger = document.getElementById("burgerMenu");
const submenu = document.getElementById("submenu");

submenu.style.display="none";
burger.addEventListener("click", manage);

function manage() {
    console.log(submenu.style.display)
    if (submenu.style.display === "none") {
        submenu.style.display = "flex";
        submenu.style.animation = "my_animation 0.2s ease-in";
    } else if (submenu.style.display === "flex") {
        submenu.style.animation = "my_animation2 0.2s ease-in-out";
        submenu.style.display = "none";
    }
}
