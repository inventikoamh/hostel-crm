/* Resido Landing Page JavaScript - Complete Redesign */

// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('navbar-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (toggleBtn && mobileMenu) {
        toggleBtn.addEventListener('click', function() {
            toggleBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        
        // Close mobile menu when clicking outside
        mobileMenu.addEventListener('click', function(e) {
            if (e.target === mobileMenu) {
                toggleBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
        
        // Close mobile menu when clicking on links
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                toggleBtn.classList.remove('active');
                mobileMenu.classList.remove('active');
            });
        });
    }
});

// Navbar Scroll Effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.new-navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offsetTop = target.offsetTop - 80; // Account for fixed navbar
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        }
    });
});

// Active navigation link highlighting
window.addEventListener('scroll', function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link[href^="#"]');
    
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (window.scrollY >= sectionTop) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === '#' + current) {
            link.classList.add('active');
        }
    });
});

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success feedback
        const button = event.target.closest('.copy-btn');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.background = '#4ade80';
        
        setTimeout(() => {
            button.innerHTML = originalIcon;
            button.style.background = '';
        }, 2000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        // Show success feedback
        const button = event.target.closest('.copy-btn');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i>';
        button.style.background = '#4ade80';
        
        setTimeout(() => {
            button.innerHTML = originalIcon;
            button.style.background = '';
        }, 2000);
    });
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeNavigation();
  initializeHeroSection();
  initializeStatisticsSection();
  initializeScrollAnimations();
});

// Navigation functionality
function initializeNavigation() {
  const navbar = document.querySelector('.nav');
  let lastScrollY = window.scrollY;

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
      }
    });
  });

  // Navbar scroll effect
  window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;
    
    if (currentScrollY > 100) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
    
    lastScrollY = currentScrollY;
  });
}

// Hero Section functionality
function initializeHeroSection() {
  const dashboardCard = document.querySelector('.dashboard-card');
  const actionItems = document.querySelectorAll('.action-item');

  // Dashboard card hover effect
  if (dashboardCard) {
    dashboardCard.addEventListener('mouseenter', () => {
      dashboardCard.style.transform = 'translateY(-5px)';
      dashboardCard.style.boxShadow = 'var(--shadow-xl)';
    });
    
    dashboardCard.addEventListener('mouseleave', () => {
      dashboardCard.style.transform = 'translateY(0)';
      dashboardCard.style.boxShadow = 'var(--shadow-lg)';
    });
  }

  // Action items hover effect
  actionItems.forEach(item => {
    item.addEventListener('mouseenter', () => {
      item.style.transform = 'translateY(-1px)';
      item.style.background = 'rgba(255, 255, 255, 0.9)';
    });

    item.addEventListener('mouseleave', () => {
      item.style.transform = 'translateY(0)';
      item.style.background = 'rgba(255, 255, 255, 0.6)';
    });
  });
}

// Statistics Section functionality
function initializeStatisticsSection() {
  const counters = document.querySelectorAll('.stat-number[data-target], .additional-stat-number[data-target]');
  const statCards = document.querySelectorAll('.stat-card');
  const statIcons = document.querySelectorAll('.stat-card .stat-icon');
  const progressBars = document.querySelectorAll('.progress-fill');

  // Counter animation
  if (counters.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const counter = entry.target;
          if (counter.getAttribute('data-target') && !counter.classList.contains('animated')) {
            counter.classList.add('animated');
            animateCounter(counter);
          }
        }
      });
    }, {
      threshold: 0.5
    });
    
    counters.forEach(counter => {
      observer.observe(counter);
    });
  }

  // Progress bar animation
  if (progressBars.length > 0) {
    const progressObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const progressBar = entry.target;
          if (!progressBar.classList.contains('animated')) {
            progressBar.classList.add('animated');
            // Trigger the width animation
            const width = progressBar.style.width;
            progressBar.style.width = '0%';
            setTimeout(() => {
              progressBar.style.width = width;
            }, 100);
          }
        }
      });
    }, {
      threshold: 0.5
    });
    
    progressBars.forEach(progressBar => {
      progressObserver.observe(progressBar);
    });
  }

  // Stat cards hover effect
  statCards.forEach(card => {
    card.addEventListener('mouseenter', () => {
      card.style.transform = 'translateY(-8px)';
    });
    
    card.addEventListener('mouseleave', () => {
      card.style.transform = 'translateY(0)';
    });
  });

  // Stat icons hover effect
  statIcons.forEach(icon => {
    icon.addEventListener('mouseenter', () => {
      icon.style.transform = 'scale(1.1) rotate(5deg)';
    });
    
    icon.addEventListener('mouseleave', () => {
      icon.style.transform = 'scale(1) rotate(0deg)';
    });
  });
}

// Counter animation function
function animateCounter(counter) {
  const target = parseInt(counter.getAttribute('data-target'));
  const duration = 2000; // 2 seconds
  const increment = target / (duration / 16); // 60fps
  let current = 0;
  
  const updateCounter = () => {
    current += increment;
    if (current < target) {
      counter.textContent = Math.floor(current);
      requestAnimationFrame(updateCounter);
    } else {
      counter.textContent = target;
    }
  };
  
  updateCounter();
}

// Scroll animations
function initializeScrollAnimations() {
  const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate-fade-in-up');
      }
    });
  }, observerOptions);

  // Observe elements for animation
  const elementsToAnimate = document.querySelectorAll(`
    .hero-badge,
    .hero-headline,
    .hero-subheadline,
    .hero-actions,
    .hero-visual,
    .statistics-badge,
    .statistics-title,
    .statistics-subtitle,
    .stat-card,
    .features-badge,
    .features-title,
    .features-subtitle,
    .feature-card,
    .technology-badge,
    .technology-title,
    .technology-subtitle,
    .tech-item,
    .cta-title,
    .cta-subtitle,
    .cta-actions
  `);

  elementsToAnimate.forEach(el => {
    observer.observe(el);
  });
}

// Utility functions
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Performance optimization
function optimizePerformance() {
  // Lazy load images if any
  const images = document.querySelectorAll('img[data-src]');
  if (images.length > 0) {
    const imageObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          img.src = img.dataset.src;
          img.classList.remove('lazy');
          imageObserver.unobserve(img);
        }
      });
    });
    
    images.forEach(img => imageObserver.observe(img));
  }
}

// Initialize performance optimizations
optimizePerformance();