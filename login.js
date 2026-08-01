// ===== Mobile Hamburger Menu =====

const hamburgerBtn = document.getElementById("hamburgerBtn");
const navLinks = document.getElementById("navLinks");

if (hamburgerBtn && navLinks) {
    hamburgerBtn.addEventListener("click", function () {
        hamburgerBtn.classList.toggle("active");
        navLinks.classList.toggle("nav-open");
        const isOpen = navLinks.classList.contains("nav-open");
        hamburgerBtn.setAttribute("aria-expanded", isOpen);
    });

    // Close the menu when a link inside it is clicked
    navLinks.querySelectorAll("a").forEach(function (link) {
        link.addEventListener("click", function () {
            hamburgerBtn.classList.remove("active");
            navLinks.classList.remove("nav-open");
            hamburgerBtn.setAttribute("aria-expanded", "false");
        });
    });

    // Close the menu on outside click
    document.addEventListener("click", function (e) {
        if (!hamburgerBtn.contains(e.target) && !navLinks.contains(e.target)) {
            hamburgerBtn.classList.remove("active");
            navLinks.classList.remove("nav-open");
            hamburgerBtn.setAttribute("aria-expanded", "false");
        }
    });
}

// ===== Login Modal =====

const openModalBtn = document.getElementById("openModalBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const modalOverlay = document.getElementById("modalOverlay");
const loginFormContainer = document.getElementById("loginFormContainer");
const signupFormContainer = document.getElementById("signupFormContainer");
const switchToSignUp = document.getElementById("switchToSignUp");
const switchToLogIn = document.getElementById("switchToLogIn");

// Open Modal
if (openModalBtn) {
    openModalBtn.addEventListener("click", function (e) {
        e.preventDefault();

        modalOverlay.classList.add("active");

        loginFormContainer.classList.add("active");
        signupFormContainer.classList.remove("active");
    });
}

// Close Modal Button
if (closeModalBtn) {
    closeModalBtn.addEventListener("click", function () {
        modalOverlay.classList.remove("active");
    });
}

// Close when clicking outside
if (modalOverlay) {
    modalOverlay.addEventListener("click", function (e) {
        if (e.target === modalOverlay) {
            modalOverlay.classList.remove("active");
        }
    });
}

// Switch to Sign Up
if (switchToSignUp) {
    switchToSignUp.addEventListener("click", function (e) {
        e.preventDefault();

        loginFormContainer.classList.remove("active");
        signupFormContainer.classList.add("active");
    });
}

// Switch to Login
if (switchToLogIn) {
    switchToLogIn.addEventListener("click", function (e) {
        e.preventDefault();

        signupFormContainer.classList.remove("active");
        loginFormContainer.classList.add("active");
    });
}