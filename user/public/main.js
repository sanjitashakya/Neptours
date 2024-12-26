

// for the navigation bar
var navbar = document.querySelector('.nav');
window.onscroll = function () {
  this.scrollY > 280 ? navbar.classList.add("sticky") : navbar.classList.remove("sticky");
}

//For swiper JS 

var swiper = new Swiper(".mySwiper", {
  spaceBetween: 30,
  pagination: {
    el: ".swiper-pagination",
  },

  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },

  slidesPerView: 1,
  loop: true,
});

