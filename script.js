// Initialize GSAP
gsap.registerPlugin(ScrollTrigger);

// Custom cursor effect
const cursor = document.createElement('div');
cursor.className = 'custom-cursor';
document.body.appendChild(cursor);

let cursorX = 0;
let cursorY = 0;
let targetX = 0;
let targetY = 0;

document.addEventListener('mousemove', (e) => {
    targetX = e.clientX;
    targetY = e.clientY;
});

gsap.ticker.add(() => {
    const speed = 0.15;
    cursorX += (targetX - cursorX) * speed;
    cursorY += (targetY - cursorY) * speed;
    cursor.style.transform = `translate(${cursorX}px, ${cursorY}px)`;
});

// Hero section animations
const heroAnimation = () => {
    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
    
    tl.from('.hero-content', {
        duration: 1.2,
        opacity: 0,
        y: 100,
        scale: 0.9,
    }).from('.nav-links a', {
        duration: 0.8,
        opacity: 0,
        y: -20,
        stagger: 0.2,
    }, '-=0.8');

    // Floating elements animation
    const createFloatingCards = () => {
        const container = document.querySelector('.floating-cards');
        const colors = ['rgba(255,255,255,0.1)', 'rgba(255,215,0,0.1)', 'rgba(34,139,34,0.1)'];
        
        for (let i = 0; i < 15; i++) {
            const card = document.createElement('div');
            card.className = 'card';
            card.style.left = `${Math.random() * 100}%`;
            card.style.top = `${Math.random() * 100}%`;
            card.style.background = colors[Math.floor(Math.random() * colors.length)];
            container.appendChild(card);

            gsap.to(card, {
                y: -300,
                rotation: 360 * (Math.random() < 0.5 ? -1 : 1),
                duration: 15 + Math.random() * 10,
                repeat: -1,
                ease: 'none',
                delay: Math.random() * 5
            });
        }
    };

    createFloatingCards();
};

// Scroll animations for articles
const scrollAnimations = () => {
    gsap.utils.toArray('article').forEach((article, i) => {
        gsap.from(article, {
            scrollTrigger: {
                trigger: article,
                start: 'top bottom-=100',
                toggleActions: 'play none none reverse'
            },
            duration: 1,
            y: 100,
            opacity: 0,
            scale: 0.9,
            delay: i * 0.2
        });
    });
};

// Glow effect for cards
const initGlowEffect = () => {
    const glowCards = document.querySelectorAll('[data-glow]');
    
    glowCards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            gsap.to(card, {
                '--x': x,
                '--y': y,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        card.addEventListener('mouseleave', () => {
            gsap.to(card, {
                '--x': rect.width / 2,
                '--y': rect.height / 2,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
    });
};

// Button hover animations
const buttonAnimations = () => {
    const buttons = document.querySelectorAll('button');
    
    buttons.forEach(button => {
        button.addEventListener('mouseenter', () => {
            gsap.to(button, {
                scale: 1.05,
                duration: 0.3,
                ease: 'power2.out'
            });
        });

        button.addEventListener('mouseleave', () => {
            gsap.to(button, {
                scale: 1,
                duration: 0.3,
                ease: 'power2.out'
            });
        });
    });
};

// Parallax effect for background
const parallaxEffect = () => {
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.floating-cards .card');
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
    });
};

// Navbar scroll effect
const navbarEffect = () => {
    const nav = document.querySelector('nav');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        if (currentScroll <= 0) {
            nav.classList.remove('scroll-up');
            return;
        }
        
        if (currentScroll > lastScroll && !nav.classList.contains('scroll-down')) {
            nav.classList.remove('scroll-up');
            nav.classList.add('scroll-down');
        } else if (currentScroll < lastScroll && nav.classList.contains('scroll-down')) {
            nav.classList.remove('scroll-down');
            nav.classList.add('scroll-up');
        }
        
        lastScroll = currentScroll;
    });
};

// Loading animation
const loadingAnimation = () => {
    const tl = gsap.timeline();
    
    tl.from('body', {
        opacity: 0,
        duration: 1
    }).from('.logo', {
        opacity: 0,
        x: -50,
        duration: 0.8
    }, '-=0.5');
};

// Initialize all animations
document.addEventListener('DOMContentLoaded', () => {
    loadingAnimation();
    heroAnimation();
    scrollAnimations();
    initGlowEffect();
    buttonAnimations();
    parallaxEffect();
    navbarEffect();
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            gsap.to(window, {
                duration: 1,
                scrollTo: {
                    y: target,
                    offsetY: 70
                },
                ease: 'power3.inOut'
            });
        }
    });
});