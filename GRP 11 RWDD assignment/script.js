function toggleSidebar() {
    document.getElementById("sidebar")?.classList.toggle("show");
}

(function initTheme() {
    const saved = localStorage.getItem("theme");
    if (saved === "dark") {
        document.body.classList.add("dark");
    }
})();

function toggleDarkMode() {
    document.body.classList.toggle("dark");
    localStorage.setItem(
        "theme",
        document.body.classList.contains("dark") ? "dark" : "light"
    );
}

function closeAllModals() {
    document.querySelectorAll(".modal").forEach(modal => {
        modal.style.display = "none";
    });
}

function openModal(id) {
    closeAllModals();
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = "flex";
    }
}

function closeModal(id) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = "none";
    }
}

function openAddModal() {
    openModal("addModal");
}

function openEditModal(id, role, status) {
    document.getElementById("editUserId").value = id;
    document.getElementById("editRole").value = role;
    document.getElementById("editStatus").value = status;
    openModal("editModal");
}

function confirmLogout() {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "admin_logout.php";
    }
}


