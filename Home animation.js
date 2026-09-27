// Staggered fade + slide-up reveal for the home page hero text.
// Works on any element with class "reveal-line" or "reveal-fade" already
// present in the HTML - no dependencies needed.
document.addEventListener('DOMContentLoaded', function () {
  var els = document.querySelectorAll('.reveal-line, .reveal-fade');
  els.forEach(function (el, i) {
    setTimeout(function () {
      el.classList.add('visible');
    }, i * 220);
  });
});