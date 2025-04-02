let slideIndex = 0;

function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    slides[slideIndex - 1].style.display = "block";
    setTimeout(showSlides, 10000); // Change image every 10 seconds
}

function changeSlide(n) {
    slideIndex += n - 1;
    showSlides();
}

function animateService(serviceElement) {
    serviceElement.classList.toggle('active');
    // Additional animation logic can be added here if needed
}

document.addEventListener("DOMContentLoaded", showSlides);
