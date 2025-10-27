document.getElementById("menuToggle").addEventListener("click", function () {
  const nav = document.querySelector("nav");
  nav.classList.toggle("active");

  if (nav.classList.contains("active")) {
    this.textContent = "\u2716";
  } else {
    this.textContent = "\u2630";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const homeSection = document.getElementById("home");
  const ucapan = document.createElement("p");
  ucapan.textContent = "Halo! Selamat datang di halaman saya!";
  homeSection.appendChild(ucapan);
}); 

document.querySelector("form").addEventListener("submit", function (e) {
  const nama = document.getElementById("txtNama");
  const email = document.getElementById("txtEmail");
  const pesan = document.getElementById("txtPesan");
  document.querySelectorAll(".error-msg").forEach(el => el.remove());
  [nama, email, pesan].forEach(el => el.style.border = "");

  let isValid = true;

if (nama.value.trim().length < 3) {
  showError(nama, "Nama minimal 3 huruf dan tidak boleh kosong.");
  isValid = false;
} else if (!/^[A-Za-z\s]+$/.test(nama.value)) {
  showError(nama, "Nama hanya boleh berisi huruf dan spasi.");
  isValid = false;
}

if (email.value.trim() === "") {
  showError(email, "Email wajib diisi.");
  isValid = false;
} else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
  showError(email, "Format email tidak valid. Contoh: nama@mail.com");
  isValid = false;
}

if (pesan.value.trim().length < 10) {
   showError(pesan, "Pesan minimal 10 karakter agar lebih jelas.");
   isValid = false;
}

if (!isValid) {
  e.preventDefault();
  } else {
    alert("Terima kasih, " + nama.value + "!\nPesan Anda telah dikirim.");
  }
});

function showError(inputElement, message) {
  const label = inputElement.closest("label");
  if (!label) return;

  label.style.flexWrap = "wrap";

  const small = document.createElement("small");
  small.className = "error-msg";
  small.textContent = message;
  small.style.color = "red";
  small.style.fontSize = "14px";
  small.style.display = "block";
  small.style.marginTop = "4px";
  small.style.flexBasis = "100%";
  small.dataset.forId = inputElement.id;
  if (inputElement.nextSibling) {
  label.insertBefore(small, inputElement.nextSibling);
} else {
  label.appendChild(small);
}

  inputElement.style.border = "1px solid red";

  alignErrorMessage(small, inputElement);
}

function alignErrorMessage(smallEl, inputEl) {
  const isMobile = window.matchMedia("(max-width: 600px)").matches;
  if (isMobile) {
    smallEl.style.marginLeft = "0";
    smallEl.style.width = "100%";
    return;
  }

  const label = inputEl.closest("label");
  if (!label) return;

  const rectLabel = label.getBoundingClientRect();
  const rectInput = inputEl.getBoundingClientRect();
  const offsetLeft = Math.max(0, Math.round(rectInput.left - rectLabel.left));

  smallEl.style.marginLeft = offsetLeft + "px";
  smallEl.style.width = Math.round(rectInput.width) + "px";
}

window.addEventListener("resize", () => {
  document.querySelectorAll(".error-msg").forEach(small => {
    const target = document.getElementById(small.dataset.forId);
    if (target) alignErrorMessage(small, target);
   });
});