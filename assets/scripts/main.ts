// Scripts (TypeScript)

export {};

type Nullable<T> = T | null;

window.addEventListener('DOMContentLoaded', () => {
  // Navbar shrink function
  const navbarShrink = (): void => {
    const navbarCollapsible = document.querySelector<HTMLElement>('#mainNav');
    if (!navbarCollapsible) return;

    if (window.scrollY === 0) {
      navbarCollapsible.classList.remove('navbar-shrink');
    } else {
      navbarCollapsible.classList.add('navbar-shrink');
    }
  };

  // Shrink the navbar initially
  navbarShrink();

  // Shrink the navbar when page is scrolled
  document.addEventListener('scroll', navbarShrink);

  // Activate Bootstrap ScrollSpy on the main nav element
  const mainNav = document.querySelector<HTMLElement>('#mainNav');
  const Bootstrap: any = (window as any).bootstrap;
  if (mainNav && Bootstrap && Bootstrap.ScrollSpy) {
    new Bootstrap.ScrollSpy(document.body, {
      target: '#mainNav',
      rootMargin: '0px 0px -40%',
    });
  }

  // Collapse responsive navbar when toggler is visible
  const navbarToggler = document.querySelector<HTMLButtonElement>('.navbar-toggler');
  const responsiveNavItems = Array.from(document.querySelectorAll<HTMLAnchorElement>('#navbarResponsive .nav-link'));

  responsiveNavItems.forEach((responsiveNavItem) => {
    responsiveNavItem.addEventListener('click', () => {
      if (!navbarToggler) return;
      const display = window.getComputedStyle(navbarToggler).display;
      if (display !== 'none') {
        navbarToggler.click();
      }
    });
  });
});
