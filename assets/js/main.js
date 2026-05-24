// ============================================
// GENERAL FUNCTIONALITY & ANIMATIONS
// ============================================

// Smooth scroll animation
function smoothScroll(target) {
  const element = document.querySelector(target);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}

// Intersection Observer for scroll animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animated');
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

// Observe all elements with animate-on-scroll class
document.querySelectorAll('.animate-on-scroll').forEach((el) => {
  observer.observe(el);
});

// Handle product buy button clicks
document.querySelectorAll('.buy-btn').forEach((btn) => {
  btn.addEventListener('click', (e) => {
    const link = btn.getAttribute('data-link') || btn.href;
    if (link) {
      window.open(link, '_blank');
    }
  });
});

// Contact form submission
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    try {
      const response = await fetch('includes/api.php?action=contact', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ name, email, message })
      });

      const data = await response.json();

      if (data.success) {
        alert('Message sent successfully!');
        contactForm.reset();
      } else {
        alert('Error sending message: ' + data.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred. Please try again.');
    }
  });
}

// Load products dynamically
async function loadProducts(category = null) {
  try {
    let url = 'includes/api.php?action=products';
    if (category) {
      url += '&category=' + encodeURIComponent(category);
    }

    const response = await fetch(url);
    const data = await response.json();

    if (data.success) {
      displayProducts(data.products);
    }
  } catch (error) {
    console.error('Error loading products:', error);
  }
}

// Display products
function displayProducts(products) {
  const container = document.getElementById('productsContainer');
  if (!container) return;

  container.innerHTML = '';

  if (products.length === 0) {
    container.innerHTML = '<p>No products found.</p>';
    return;
  }

  products.forEach((product, index) => {
    const productCard = document.createElement('div');
    productCard.className = 'product-card animate-on-scroll';
    productCard.style.animationDelay = (index * 0.1) + 's';

    productCard.innerHTML = `
      <div class="product-image">📦</div>
      <div class="product-info">
        <div class="product-category">${product.category}</div>
        <div class="product-name">${product.name}</div>
        <div class="product-description">${product.description}</div>
        <div class="product-footer">
          <span class="product-price">₹${parseFloat(product.price).toLocaleString('en-IN')}</span>
          <a href="${product.affiliate_link}" target="_blank" class="buy-btn">Buy Now</a>
        </div>
      </div>
    `;

    container.appendChild(productCard);
  });

  // Observe new elements
  document.querySelectorAll('.animate-on-scroll').forEach((el) => {
    if (!el.classList.contains('animated')) {
      observer.observe(el);
    }
  });
}

// Load featured products on home page
document.addEventListener('DOMContentLoaded', () => {
  const featuredContainer = document.getElementById('featuredProducts');
  if (featuredContainer) {
    loadFeaturedProducts();
  }
});

async function loadFeaturedProducts() {
  try {
    const response = await fetch('includes/api.php?action=featured');
    const data = await response.json();

    if (data.success) {
      displayProducts(data.products);
    }
  } catch (error) {
    console.error('Error loading featured products:', error);
  }
}

// Navigation menu functionality
function setupNavigation() {
  const navLinks = document.querySelectorAll('.nav-links a');

  navLinks.forEach((link) => {
    link.addEventListener('click', (e) => {
      const href = link.getAttribute('href');
      
      // If it's an internal link (starts with #)
      if (href && href.startsWith('#')) {
        e.preventDefault();
        smoothScroll(href);
      }
    });
  });
}

// Initialize navigation
document.addEventListener('DOMContentLoaded', setupNavigation);

// Mobile menu toggle (if implemented)
function toggleMobileMenu() {
  const navLinks = document.querySelector('.nav-links');
  if (navLinks) {
    navLinks.classList.toggle('active');
  }
}

// Lazy loading for images
if ('IntersectionObserver' in window) {
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.src = img.dataset.src;
        img.classList.remove('lazy');
        imageObserver.unobserve(img);
      }
    });
  });

  document.querySelectorAll('img.lazy').forEach((img) => imageObserver.observe(img));
}

// Add scroll effect to header
let lastScrollTop = 0;
const header = document.querySelector('header');

if (header) {
  window.addEventListener('scroll', () => {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    
    if (scrollTop > 100) {
      header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
    } else {
      header.style.boxShadow = '0 10px 40px rgba(230, 0, 18, 0.1)';
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
  });
}
