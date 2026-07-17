// Theme toggle
(function initTheme() {
  const saved = localStorage.getItem('theme'); // 'dark' | 'light' | null
  const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

  // Apply saved theme, else follow system preference
  if (saved === 'dark' || (saved === null && prefersDark)) {
    document.body.classList.add('dark');
  }

  updateToggleLabel();

  const btn = document.getElementById('theme-toggle');
  if (btn) {
    btn.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      const isDark = document.body.classList.contains('dark');
      localStorage.setItem('theme', isDark ? 'dark' : 'light');
      updateToggleLabel();
    });
  }

  function updateToggleLabel() {
    const isDark = document.body.classList.contains('dark');
    const btn = document.getElementById('theme-toggle');
    if (btn) {
      btn.textContent = isDark ? 'Light' : 'Dark';
      btn.setAttribute('aria-pressed', String(isDark));
      btn.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    }
  }
})();

function login() {
  var username = document.getElementById("username").value;
  var password = document.getElementById("password").value;
  var message = document.getElementById("message");

  var form = new FormData();
  form.append("username", username);
  form.append("password", password);

  fetch("login.php", {
    method: "POST",
    body: form
  })
  .then(response => response.text())
  .then(raw => {
    const data = (raw || '').trim();

    if (data === "Admin") {
      alert("Logged in as Admin");
      window.location.href = "admin_dashboard.php";
    }
    else if (data === "Educator") {
      alert("Logged in as Educator");
      window.location.href = "Educator_Dashboard.php";
    }
    else if (data === "Student") {
      alert("Logged in as Student");
      window.location.href = "student_dashboard.php";
    }
    else if (data === "Guest") {
      alert("Logged in as Guest");
      window.location.href = "guest.html";
    }
    else {
      message.innerHTML = "Invalid username or password";
      message.style.color = "red";
    }
  });
}

function loginAsGuest() {
  alert("Logged in as Guest");
  window.location.href = "guest.html";
}
