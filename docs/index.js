window.addEventListener("DOMContentLoaded", () => {
  const nav = document.querySelector(".table-of-content");
  const titles = Array.from(document.querySelectorAll(".title"));
  let activeNavIndex = -1;

  const updateActiveNavItem = () => {
    const scrollPosition = window.scrollY;

    if (scrollPosition === 0) {
      if (activeNavIndex !== -1) {
        nav.querySelector("a.active").classList.remove("active");
        activeNavIndex = -1;
      }
      return;
    }

    let newActiveNavIndex = -1;
    for (let i = titles.length - 1; i >= 0; i--) {
      const title = titles[i];
      const rect = title.getBoundingClientRect();

      if (rect.top <= window.innerHeight / 2) {
        newActiveNavIndex = i;
        break;
      }
    }

    if (newActiveNavIndex !== activeNavIndex) {
      if (activeNavIndex !== -1) {
        nav.querySelector("a.active").classList.remove("active");
      }
      if (newActiveNavIndex !== -1) {
        nav.querySelector(`a[href="#${titles[newActiveNavIndex].id}"]`).classList.add("active");
      }
      activeNavIndex = newActiveNavIndex;
    }
  };

  const throttledUpdateActiveNavItem = () => {
    if (!throttledUpdateActiveNavItem.isTicking) {
      requestAnimationFrame(() => {
        updateActiveNavItem();
        throttledUpdateActiveNavItem.isTicking = false;
      });
      throttledUpdateActiveNavItem.isTicking = true;
    }
  };
  throttledUpdateActiveNavItem.isTicking = false;

  window.addEventListener("scroll", throttledUpdateActiveNavItem);
  window.addEventListener("resize", throttledUpdateActiveNavItem);

  updateActiveNavItem();

  const currentYear = document.getElementById('current-year');
	if (currentYear) {
		currentYear.textContent = new Date().getFullYear();
	}
});
