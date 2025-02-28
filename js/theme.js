// Ambil elemen tombol toggle
const themeToggle = document.getElementById('theme-toggle');

// Cek preferensi tema dari localStorage
const savedTheme = localStorage.getItem('theme') || 'light-theme';
document.body.className = savedTheme;
updateButtonText(savedTheme);

// Fungsi untuk mengubah tema
themeToggle.addEventListener('click', () => {
  if (document.body.classList.contains('light-theme')) {
    document.body.classList.replace('light-theme', 'dark-theme');
    localStorage.setItem('theme', 'dark-theme');
  } else {
    document.body.classList.replace('dark-theme', 'light-theme');
    localStorage.setItem('theme', 'light-theme');
  }
  updateButtonText(document.body.className);
});

// Fungsi untuk mengupdate teks tombol
function updateButtonText(theme) {
  if (theme === 'dark-theme') {
    themeToggle.textContent = '☀️ Mode Terang';
  } else {
    themeToggle.textContent = '🌙 Mode Gelap';
  }
}