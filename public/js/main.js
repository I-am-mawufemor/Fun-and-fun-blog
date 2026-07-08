// ─── Navigation ───
function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
    document.querySelector(".sidenav-overlay").classList.add("active");
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.querySelector(".sidenav-overlay").classList.remove("active");
}

// Attach events after DOM is ready
document.querySelector(".menu-btn").addEventListener("click", openNav);
document.querySelector(".closebtn").addEventListener("click", closeNav);
document.querySelector(".sidenav-overlay").addEventListener("click", closeNav);