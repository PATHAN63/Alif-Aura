// Alif-Aura - Main JavaScript

document.addEventListener('DOMContentLoaded', () => {
    initLoader();
    initHeader();
    initNavToggle();
    initScrollAnimations();
    initProductGallery();
    initCarousel();
});

// Page Loader - minimal, elegant
function initLoader() {
    const loader = document.getElementById('page-loader');
    const body = document.body;
    if (!loader) return;

    window.addEventListener('load', () => {
        loader.classList.add('hidden');
        body.classList.add('loaded');
    });
    // Fallback: hide after 2s if load event doesn't fire
    setTimeout(() => {
        loader.classList.add('hidden');
        body.classList.add('loaded');
    }, 2000);
}

// Hero Carousel
function initCarousel() {
    const carousel = document.querySelector('.hero-carousel');
    if (!carousel) return;
    const slides = carousel.querySelectorAll('.carousel-slide');
    const prev = carousel.querySelector('.carousel-btn.prev');
    const next = carousel.querySelector('.carousel-btn.next');
    const dotsCont = carousel.querySelector('.carousel-dots');
    let idx = 0;

    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.classList.add('carousel-dot');
        if (i === 0) dot.classList.add('active');
        dot.setAttribute('aria-label', `Slide ${i + 1}`);
        dot.addEventListener('click', () => goTo(i));
        dotsCont && dotsCont.appendChild(dot);
    });

    function goTo(i) {
        idx = (i + slides.length) % slides.length;
        slides.forEach((s, k) => s.classList.toggle('active', k === idx));
        carousel.querySelectorAll('.carousel-dot').forEach((d, k) => d.classList.toggle('active', k === idx));
    }

    prev && prev.addEventListener('click', () => goTo(idx - 1));
    next && next.addEventListener('click', () => goTo(idx + 1));

    let auto = setInterval(() => goTo(idx + 1), 5000);
    carousel.addEventListener('mouseenter', () => clearInterval(auto));
    carousel.addEventListener('mouseleave', () => { auto = setInterval(() => goTo(idx + 1), 5000); });
}

// Header scroll visibility
function initHeader() {
    const header = document.getElementById('main-header');
    if (!header) return;

    let lastScroll = 0;
    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        if (currentScroll > 100) {
            header.classList.add('visible');
        } else {
            header.classList.remove('visible');
        }
        lastScroll = currentScroll;
    });
}

// Mobile nav toggle
function initNavToggle() {
    const toggle = document.getElementById('nav-toggle');
    const navLinks = document.querySelector('.nav-links');
    if (!toggle || !navLinks) return;

    toggle.addEventListener('click', () => {
        navLinks.classList.toggle('open');
    });

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => navLinks.classList.remove('open'));
    });
}

// Scroll-triggered fade-in animations
function initScrollAnimations() {
    const sections = document.querySelectorAll('.section.fade-in');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    sections.forEach(section => observer.observe(section));
}

// Product detail image gallery
function initProductGallery() {
    const mainImg = document.querySelector('.product-gallery .main-image');
    const thumbnails = document.querySelectorAll('.product-thumbnails img');
    if (!mainImg || !thumbnails.length) return;

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', () => {
            mainImg.src = thumb.dataset.full || thumb.src;
            thumbnails.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        });
    });
}

// Add to cart (AJAX)
function addToCart(productId, quantity = 1) {
    fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', product_id: productId, quantity })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateCartBadge(data.count);
            showToast('Added to cart!');
        }
    });
}

// Wishlist toggle (global for inline onclick)
function wishlistToggle(productId, btn) {
    fetch('api/wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            btn.classList.toggle('active', data.in_wishlist);
            if (btn.querySelector('svg')) {
                btn.querySelector('svg').setAttribute('fill', data.in_wishlist ? 'currentColor' : 'none');
            }
        }
    });
}

function toggleWishlist(productId, btn) { wishlistToggle(productId, btn); }

function updateCartBadge(count) {
    const badge = document.querySelector('.nav-icon .badge');
    if (badge) badge.textContent = count;
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    toast.style.cssText = 'position:fixed;bottom:100px;left:50%;transform:translateX(-50%);background:#D4AF37;color:#0F0F0F;padding:1rem 2rem;z-index:9999;font-size:0.9rem;letter-spacing:0.1em;animation:fadeInUp 0.3s ease;';
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}
