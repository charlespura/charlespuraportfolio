<?php
// f1.php
// Include this file in your main page (e.g. index.php).
// Ensure Tailwind is loaded in the parent page and images are reachable at pictures/f1down.png and pictures/f1up.png
?>
<div id="f1-car" class="fixed right-4 top-6 w-20 h-20 z-50 pointer-events-none">
  <img id="f1-car-img" src="pictures/f1down.png" class="w-full h-full object-contain" alt="F1 car">
</div>

<script>
(function () {
  const car = document.getElementById('f1-car');
  const carImg = document.getElementById('f1-car-img');

  // CONFIG: adjust these to taste
  const MAX_DOWN = 800;      // px from initial top where car can go (downward)
  const MAX_UP = -200;       // px the car can go upward (negative)
  const EASE = 0.12;         // easing for smooth movement (0.05..0.2)
  const DIRECTION_THRESHOLD = 2; // px change considered a direction change
  const VELOCITY_SMOOTH = 0.9;   // smoothing factor for velocity (0..1)

  // STATE
  let lastScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
  let currentY = 0;   // displayed position
  let targetY = 0;    // target position derived from scroll percent
  let lastTimestamp = performance.now();
  let velocity = 0;   // simple scroll velocity (px per ms, smoothed)

  // Compute scroll progress safely (0..1)
  function getScrollProgress() {
    const doc = document.documentElement;
    const body = document.body;
    const scrollTop = window.pageYOffset || doc.scrollTop || body.scrollTop || 0;
    const scrollHeight = Math.max(doc.scrollHeight, body.scrollHeight);
    const clientHeight = doc.clientHeight || window.innerHeight || 1;
    const maxScroll = Math.max(scrollHeight - clientHeight, 1);
    return scrollTop / maxScroll;
  }

  // Map progress (0..1) to targetY range (MAX_UP .. MAX_DOWN)
  function progressToY(progress) {
    // linear interpolation
    return MAX_UP + progress * (MAX_DOWN - MAX_UP);
  }

  // Update target and direction based on absolute scroll position and velocity
  function onScroll() {
    const now = performance.now();
    const curScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
    const dt = Math.max(now - lastTimestamp, 1); // ms
    const dy = curScrollTop - lastScrollTop; // px
    const instVelocity = dy / dt; // px per ms

    // smooth the velocity reading
    velocity = velocity * VELOCITY_SMOOTH + instVelocity * (1 - VELOCITY_SMOOTH);

    // image switching: based on immediate direction (dy) or velocity
    if (dy > DIRECTION_THRESHOLD || velocity > 0.002) {
      carImg.src = 'pictures/f1down.png';
    } else if (dy < -DIRECTION_THRESHOLD || velocity < -0.002) {
      carImg.src = 'pictures/f1up.png';
    }
    // set target based on absolute progress
    const progress = getScrollProgress();
    targetY = progressToY(progress);

    lastScrollTop = curScrollTop;
    lastTimestamp = now;
  }

  // Animation loop: ease currentY -> targetY and apply transform
  function animate() {
    // easing
    currentY += (targetY - currentY) * EASE;

    // snap small values
    if (Math.abs(targetY - currentY) < 0.05) currentY = targetY;

    // apply transform (translateY)
    car.style.transform = 'translateY(' + currentY + 'px)';

    requestAnimationFrame(animate);
  }

  // Update target once on load (so car is in right place if page not at top)
  function init() {
    // sync initial state
    lastScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
    lastTimestamp = performance.now();
    // set initial target based on progress
    targetY = progressToY(getScrollProgress());
    currentY = targetY;
    // small timeout to ensure styles applied before loop starts
    requestAnimationFrame(animate);
  }

  // Listen to scroll (passive)
  window.addEventListener('scroll', onScroll, { passive: true });

  // Also respond to resize since document height changes
  window.addEventListener('resize', function () {
    // recalc target on resize
    targetY = progressToY(getScrollProgress());
  });

  // expose reset if needed
  window.f1Reset = function () {
    targetY = progressToY(getScrollProgress());
    currentY = targetY;
    carImg.src = 'pictures/f1down.png';
    lastScrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
  };

  // start
  init();
})();
</script>
