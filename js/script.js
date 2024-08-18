let currentSlide = 0;
const slides = document.querySelectorAll('.sliderItem');
const totalSlides = slides.length;

function showSlide(index) {
    slides.forEach((slide, i) => {
        if (i === index) {
            slide.style.display = 'flex';
        } else {
            slide.style.display = 'none';
        }
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function startSlider() {
    showSlide(currentSlide);
    setInterval(nextSlide, 3000); // Change slide every 5 seconds
}

// Call this function when the page loads
startSlider();


// map
function initMap() {
    var location = { lat: 40.7128, lng: -74.0060 }; // Example coordinates (New York)
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: location
    });
    new google.maps.Marker({
        position: location,
        map: map
    });
}




const productButton = document.querySelector(".productButton");
const payment = document.querySelector(".payment");
const close = document.querySelector(".close");

productButton.addEventListener("click", () => {
  payment.style.display = "flex";
});

close.addEventListener("click", () => {
  payment.style.display = "none";
});

