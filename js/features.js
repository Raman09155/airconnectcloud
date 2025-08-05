// js/features.js
// Features and Why Choose sections fade-in effect on scroll

  document.addEventListener("DOMContentLoaded", function() {
    var sections = document.querySelectorAll('.features-section, .why-choose-section');
    function fadeInOnScroll() {
      sections.forEach(function(sec) {
        var position = sec.getBoundingClientRect();
        if (position.top < window.innerHeight - 30) {
          sec.classList.add('fade-in');
        }
      });
    }
    window.addEventListener('scroll', fadeInOnScroll);
    fadeInOnScroll();
  });

  // FAQ Accordion Functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(function(item) {
        const question = item.querySelector('.faq-question');
        
        question.addEventListener('click', function() {
            const isActive = item.classList.contains('active');
            
            // Close all other FAQ items
            faqItems.forEach(function(otherItem) {
                if (otherItem !== item) {
                    otherItem.classList.remove('active');
                }
            });
            
            // Toggle current item
            if (isActive) {
                item.classList.remove('active');
            } else {
                item.classList.add('active');
            }
        });
    });
    
    // Optional: Close FAQ when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.faq-container')) {
            faqItems.forEach(function(item) {
                item.classList.remove('active');
            });
        }
    });
});
