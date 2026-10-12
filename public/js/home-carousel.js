(function () {
  const carousel = document.querySelector('[data-home-carousel]');

  if (!carousel) {
    return;
  }

  const slides = Array.from(carousel.querySelectorAll('[data-carousel-slide]'));
  const controls = Array.from(carousel.querySelectorAll('[data-carousel-to]'));

  if (slides.length < 2 || slides.length !== controls.length) {
    return;
  }

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  let activeIndex = 0;
  let interval;
  let cleanupTimeout;

  function showSlide(nextIndex) {
    if (nextIndex === activeIndex) {
      return;
    }

    window.clearTimeout(cleanupTimeout);
    const previousIndex = activeIndex;
    const previousSlide = slides[previousIndex];
    const nextSlide = slides[nextIndex];

    previousSlide.classList.remove('is-active', 'is-previous');
    previousSlide.classList.add('is-previous');
    previousSlide.setAttribute('aria-hidden', 'true');

    nextSlide.classList.remove('is-previous');
    nextSlide.classList.add('is-active');
    nextSlide.setAttribute('aria-hidden', 'false');
    nextSlide.setAttribute('aria-label', `${nextIndex + 1} sur ${slides.length}`);

    controls[previousIndex].setAttribute('aria-pressed', 'false');
    controls[nextIndex].setAttribute('aria-pressed', 'true');
    activeIndex = nextIndex;

    cleanupTimeout = window.setTimeout(() => {
      previousSlide.classList.remove('is-previous');
    }, 850);
  }

  function stopAutoplay() {
    window.clearInterval(interval);
  }

  function startAutoplay() {
    stopAutoplay();

    if (!reducedMotion.matches && !document.hidden) {
      interval = window.setInterval(() => {
        showSlide((activeIndex + 1) % slides.length);
      }, 5200);
    }
  }

  controls.forEach((control) => {
    control.addEventListener('click', () => {
      showSlide(Number(control.dataset.carouselTo));
      startAutoplay();
    });
  });

  carousel.addEventListener('mouseenter', stopAutoplay);
  carousel.addEventListener('mouseleave', startAutoplay);
  carousel.addEventListener('focusin', stopAutoplay);
  carousel.addEventListener('focusout', startAutoplay);
  document.addEventListener('visibilitychange', startAutoplay);
  reducedMotion.addEventListener('change', startAutoplay);
  startAutoplay();
})();
