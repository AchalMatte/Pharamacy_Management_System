// Pharmacy Management System JavaScript

console.log("Pharmacy System Loaded Successfully");

// ===============================
// Navbar Active Link
// ===============================

const navLinks = document.querySelectorAll(".nav-link");

navLinks.forEach(link => {

    link.addEventListener("click", function () {

        navLinks.forEach(nav => nav.classList.remove("active"));

        this.classList.add("active");

    });

});


// ===============================
// Smooth Scroll
// ===============================

document.querySelectorAll('a[href^="#"]').forEach(anchor => {

    anchor.addEventListener('click', function (e) {

        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));

        if(target){

            target.scrollIntoView({
                behavior: 'smooth'
            });

        }

    });

});


// ===============================
// Hero Button Animation
// ===============================

const buttons = document.querySelectorAll(".btn");

buttons.forEach(button => {

    button.addEventListener("mouseenter", () => {

        button.style.transform = "scale(1.05)";
        button.style.transition = "0.3s";

    });

    button.addEventListener("mouseleave", () => {

        button.style.transform = "scale(1)";

    });

});


// ===============================
// Feature Card Hover Effect
// ===============================

const cards = document.querySelectorAll(".feature-card");

cards.forEach(card => {

    card.addEventListener("mouseover", () => {

        card.style.boxShadow = "0 15px 35px rgba(0,0,0,0.1)";

    });

    card.addEventListener("mouseout", () => {

        card.style.boxShadow = "0 10px 25px rgba(0,0,0,0.05)";

    });

});


// ===============================
// Typing Effect
// ===============================

const heroTitle = document.querySelector(".hero-title");

if(heroTitle){

    const text = "Modern Pharmacy Management";

    let index = 0;

    heroTitle.innerHTML = "";

    function typeEffect(){

        if(index < text.length){

            heroTitle.innerHTML += text.charAt(index);

            index++;

            setTimeout(typeEffect, 70);

        }

    }

    typeEffect();

}


// ===============================
// Scroll Animation
// ===============================

window.addEventListener("scroll", () => {

    const cards = document.querySelectorAll(".feature-card");

    cards.forEach(card => {

        const cardTop = card.getBoundingClientRect().top;

        const windowHeight = window.innerHeight;

        if(cardTop < windowHeight - 100){

            card.style.opacity = "1";
            card.style.transform = "translateY(0px)";
            card.style.transition = "0.5s";

        }

    });

});


// ===============================
// Initial Card State
// ===============================

cards.forEach(card => {

    card.style.opacity = "0";
    card.style.transform = "translateY(50px)";

});


// ===============================
// Dynamic Year in Footer
// ===============================

const footer = document.querySelector("footer p");

if(footer){

    const year = new Date().getFullYear();

    footer.innerHTML =
        `© ${year} Pharmacy Management System`;

}