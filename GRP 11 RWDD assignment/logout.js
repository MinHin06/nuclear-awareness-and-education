document.addEventListener("DOMContentLoaded", () => {
  const logout = document.querySelector(".logout-top");
  if (logout) {
    logout.addEventListener("click", (e) => {
      if (!confirm("Logout now?")) e.preventDefault();
    });
  }
});
