$(document).ready(function () {
    const sections = document.querySelectorAll("#bsSpyContent > div");
    const navLinks = document.querySelectorAll("#bsSpyTarget > a");

    function setActiveLink(link) {
        navLinks.forEach((navLink) => navLink.classList.remove("active"));
        link.classList.add("active");
    }

    window.onscroll = () => {
        const scrollY = window.scrollY;

        sections.forEach((section) => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.offsetHeight;
            const sectionId = section.getAttribute("id");
            if (scrollY + 300 >= sectionTop - 300 && scrollY < sectionTop + sectionHeight) {
                setActiveLink(document.querySelector(`nav a[href*='${sectionId}']`));
            }
        });
    };

});
