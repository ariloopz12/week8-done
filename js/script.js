const prevBtn = document.querySelector('.prev');
const nextBtn = document.querySelector('.next');
const carouselContainer = document.querySelector('.carousel-container');
const carouselItems = document.querySelectorAll('.carousel-item');

let currentIndex = 0;
const totalItems = carouselItems.length;

nextBtn.addEventListener('click', () => {
  currentIndex = (currentIndex + 1) % totalItems;
  updateCarousel();
});

prevBtn.addEventListener('click', () => {
  currentIndex = (currentIndex - 1 + totalItems) % totalItems;
  updateCarousel();
});

function updateCarousel() {
  const offset = -currentIndex * 100;
  carouselContainer.style.transform = `translateX(${offset}%)`;
}

// JS Untuk Filter Section
// Select all filter buttons and program items
const filterButtons = document.querySelectorAll('.filter-btn');
const programItems = document.querySelectorAll('.program-item');

// Add click event listener to all buttons
filterButtons.forEach((button) => {
  button.addEventListener('click', () => {
    // Remove 'active' class from all buttons
    filterButtons.forEach((btn) => btn.classList.remove('active'));

    // Add 'active' class to the clicked button
    button.classList.add('active');

    // Get the selected category from data attribute
    const category = button.getAttribute('data-category');

    // Filter the program items
    programItems.forEach((item) => {
      // If the category is 'all', show all items
      if (category === 'all') {
        item.classList.remove('hidden');
      } else {
        // Otherwise, show only items that match the category
        if (item.classList.contains(category)) {
          item.classList.remove('hidden');
        } else {
          item.classList.add('hidden');
        }
      }
    });
  });
});

// By default, show all programs
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('.filter-btn[data-category="all"]').click();
});

// Toggle class active untuk hamburger menu
const navbarNav = document.querySelector('.navbar-nav');
// Ketika hamburger menu di klik
document.querySelector('#hamburger-menu').onclick = () => {
  navbarNav.classList.toggle('active');
};

// JavaScript untuk membuka modal pada halaman detail
function openModal() {
  document.getElementById('register-modal').style.display = 'flex';
}

// JavaScript untuk menutup modalpada halaman detail
function closeModal() {
  document.getElementById('register-modal').style.display = 'none';
}

// Toggle class active untuk search form
const searchForm = document.querySelector('.search-form');
const searchBox = document.querySelector('#search-form');

document.querySelector('#search-button').onclick = (e) => {
  searchForm.classList.toggle('active');
  e.preventDefault();
  searchBox.focus();
};
