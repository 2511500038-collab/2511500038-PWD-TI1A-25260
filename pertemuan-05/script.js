document.getElementById("menuToggle").addEventListener("click", function () {
  document.querySelector("nav").classList.toggle("active");
});

document.getElementById("txtPesan").addEventListener("input", function () {
  const panjang = this.value.length;
  document.getElementById("charCount").textContent = panjang + "/200 karakter";
});

document.addEventListener("DOMContentLoaded", function () {
...
function setupCharCountLayout() {
const label = document.querySelector('label[for="txtPesan"]');
if (!label) return;
let wrapper = label.querySelector('[data-wrapper="pesan-wrapper"]');
const span = label.querySelector('span');
const textarea = document.getElementById('txtPesan');
const counter = document.getElementById('charCount');
if (!span || !textarea || !counter) return;
if (!wrapper) {
wrapper = document.createElement('div');
wrapper.dataset.wrapper = 'pesan-wrapper';
wrapper.style.width = '100%';
wrapper.style.flex = '1';
wrapper.style.display = 'flex';
wrapper.style.flexDirection = 'column';
label.insertBefore(wrapper, textarea);
wrapper.appendChild(textarea);
wrapper.appendChild(counter);
textarea.style.width = '100%';
textarea.style.boxSizing = 'border-box';
counter.style.color = '#555';
counter.style.fontSize = '14px';
counter.style.marginTop = '4px';
}
applyResponsiveLayout();
}
function applyResponsiveLayout() {
const label = document.querySelector('label[for="txtPesan"]');
const span = label?.querySelector('span');
const wrapper = label?.querySelector('[data-wrapper="pesan-wrapper"]');
const counter = document.getElementById('charCount');
if (!label || !span || !wrapper || !counter) return;
const isMobile = window.matchMedia('(max-width: 600px)').matches;
if (isMobile) {
label.style.display = 'flex';
 label.style.flexDirection = 'column';
label.style.alignItems = 'flex-start';
label.style.width = '100%';
span.style.minWidth = 'auto';
span.style.textAlign = 'left';
span.style.paddingRight = '0';
span.style.flexShrink = '0';
span.style.marginBottom = '4px';
wrapper.style.flex = '1';
wrapper.style.display = 'flex';
wrapper.style.flexDirection = 'column';
counter.style.alignSelf = 'flex-end';
counter.style.width = 'auto';
} else {
label.style.display = 'flex';
label.style.flexDirection = 'row';
label.style.alignItems = 'baseline';
label.style.width = '100%';
span.style.minWidth = '180px';
span.style.textAlign = 'right';
span.style.paddingRight = '16px';
span.style.flexShrink = '0';
span.style.marginBottom = '0';
wrapper.style.flex = '1';
wrapper.style.display = 'flex';
wrapper.style.flexDirection = 'column';
counter.style.alignSelf = 'flex-end';
counter.style.width = 'auto';
}
}
setupCharCountLayout();
window.addEventListener('resize', applyResponsiveLayout);
...
});
