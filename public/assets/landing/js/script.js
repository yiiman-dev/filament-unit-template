// AOS Initialization
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Form Validation and Submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contactForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const company = document.getElementById('company').value;
        const message = document.getElementById('message').value;

        if (name && email && company && message) {
            alert('پیام شما با موفقیت ارسال شد! (دمو)');
            form.reset();
        } else {
            alert('لطفاً تمام فیلدها را پر کنید.');
        }
    });

    // Newsletter Signup (Demo)
    const newsletterForm = document.querySelector('footer form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('عضویت در خبرنامه با موفقیت انجام شد! (دمو)');
        });
    }

    // Persian Digits Converter
    function persianDigits(str) {
        const persianDigits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str.toString().replace(/\d/g, function(d) {
            return persianDigits[parseInt(d)];
        });
    }

    // Apply to Stats on Load
    document.querySelectorAll('.stats').forEach(function(el) {
        el.textContent = persianDigits(el.textContent);
    });

    // Hover Effects for Buttons
    document.querySelectorAll('.btn').forEach(function(btn) {
        btn.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Keyboard Navigation for Accessibility
    document.querySelectorAll('a, button, input').forEach(function(el) {
        el.setAttribute('tabindex', '0');
    });

    // Parallax Fallback Check
    const parallaxImg = new Image();
    parallaxImg.src = '/assets/landing/images/contact-us-header.png';
    parallaxImg.onerror = function() {
        document.querySelector('#contact.parallax-bg').style.backgroundImage = "url('/assets/landing/images/contact-us-header.png')";
    };
});
