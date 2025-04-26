let slideIndex = 0;

function showSlides() {
    let slides = document.getElementsByClassName("slide");
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1 }
    slides[slideIndex - 1].style.display = "block";
  //  setTimeout(showSlides, 10000); 
}

function changeSlide(n) {
    slideIndex += n - 1;
    showSlides();
}

function animateService(serviceElement) {
    serviceElement.classList.toggle('active');
}
setInterval(showSlides, 10000); // Change slide every 10 seconds

document.addEventListener("DOMContentLoaded", showSlides);
