document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("searchInput");

    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            let input = this.value.toLowerCase();
            let boxes = document.querySelectorAll(".content-box");

            boxes.forEach(box => {
                let title = box.getAttribute("data-title");
                box.style.display = title.includes(input) ? "block" : "none";
            });
        });
    }
});

function toggleDesc(button) {
    let desc = button.previousElementSibling;
    desc.style.display = (desc.style.display === "none") ? "block" : "none";
}
