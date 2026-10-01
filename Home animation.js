
document.addEventListener('DOMContentLoaded', function () {
  var els = document.querySelectorAll('.reveal-line, .reveal-fade');
  els.forEach(function (el, i) {
    setTimeout(function () {
      el.classList.add('visible');
    }, i * 220);
  });
});