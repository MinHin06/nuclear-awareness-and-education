document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const pointsCards = document.querySelectorAll(".points-card");
    const badgeCards = document.querySelectorAll(".badge-card");

    const pointsNoMatch = document.getElementById("pointsNoMatch");
    const badgesNoMatch = document.getElementById("badgesNoMatch");

    function filterCards() {
        const keyword = (searchInput.value || "").toLowerCase().trim();

        let pointsVisible = 0;
        pointsCards.forEach(card => {
            const text = (card.getAttribute("data-search") || "").toLowerCase();
            const show = text.includes(keyword);
            card.style.display = show ? "block" : "none";
            if (show) pointsVisible++;
        });

        let badgesVisible = 0;
        badgeCards.forEach(card => {
            const text = (card.getAttribute("data-search") || "").toLowerCase();
            const show = text.includes(keyword);
            card.style.display = show ? "block" : "none";
            if (show) badgesVisible++;
        });

        if (pointsNoMatch) pointsNoMatch.style.display = (keyword !== "" && pointsCards.length > 0 && pointsVisible === 0) ? "block" : "none";
        if (badgesNoMatch) badgesNoMatch.style.display = (keyword !== "" && badgeCards.length > 0 && badgesVisible === 0) ? "block" : "none";
    }

    if (searchInput) {
        searchInput.addEventListener("keyup", filterCards);
    }
});
