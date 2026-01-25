document.addEventListener("DOMContentLoaded", () => {
  const counters = document.querySelectorAll(".kpi-number");

  counters.forEach((counter) => {
    const target = +counter.dataset.value;
    const duration = 1200; // ms
    const startTime = performance.now();

    function update(currentTime) {
      const elapsed = currentTime - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const value = Math.floor(progress * target);

      counter.textContent = value.toLocaleString();

      if (progress < 1) {
        requestAnimationFrame(update);
      }
    }

    requestAnimationFrame(update);
  });
});
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      const el = entry.target;
      const target = +el.dataset.value;
      let current = 0;
      const step = Math.ceil(target / 60);

      const interval = setInterval(() => {
        current += step;
        if (current >= target) {
          el.textContent = target.toLocaleString();
          clearInterval(interval);
        } else {
          el.textContent = current.toLocaleString();
        }
      }, 16);

      observer.unobserve(el);
    }
  });
});

document.querySelectorAll(".kpi-number").forEach((el) => observer.observe(el));
