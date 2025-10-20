// LIENS AVEC ANCRE 
    document.querySelectorAll('a[href*="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href.includes('#') && href !== '#') {
                const targetId = href.split('#')[1];
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    e.preventDefault();
                    const navbarHeight = document.querySelector('.navbar').offsetHeight;
                    const targetPosition = targetElement.offsetTop - navbarHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });
