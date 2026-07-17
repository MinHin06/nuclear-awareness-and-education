function registerUser(event) {
  event.preventDefault();

  const first   = document.getElementById('first_name').value.trim();
  const last    = document.getElementById('last_name').value.trim();
  const username= document.getElementById('username').value.trim();
  const email   = document.getElementById('email').value.trim();
  const password= document.getElementById('password').value;
  const confirm = document.getElementById('confirm_password').value;
  const dob     = document.getElementById('dob').value; // yyyy-mm-dd
  const bio     = document.getElementById('bio').value.trim();
  const msg     = document.getElementById('reg-message');

  // simple checks
  if (password !== confirm) {
    msg.textContent = "Passwords do not match.";
    msg.style.color = "red";
    return;
  }
  if (password.length < 6) {
    msg.textContent = "Password must be at least 6 characters.";
    msg.style.color = "red";
    return;
  }

  const form = new FormData();
  form.append('first_name', first);
  form.append('last_name', last);
  form.append('username', username);
  form.append('email', email);
  form.append('password', password);
  form.append('dob', dob);
  form.append('bio', bio);      // optional
  // NOTE: we do NOT send role; server will set Role='Student'

  msg.textContent = "Creating account...";
  msg.style.color = "#444";

  fetch('register.php', { method: 'POST', body: form })
    .then(r => r.text())
    .then(text => {
      const data = (text || '').trim();
      if (data === 'OK') {
        alert('Registration successful! You can now log in.');
        window.location.href = 'login.html';
      } else {
        msg.textContent = data.startsWith('ERROR') ? data : 'Registration failed.';
        msg.style.color = "red";
      }
    })
    .catch(() => {
      msg.textContent = "Network error. Please try again.";
      msg.style.color = "red";
    });
}