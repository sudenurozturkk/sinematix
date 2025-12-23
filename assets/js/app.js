/**
 * Sinematix - JavaScript Application
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize components
    initHeader();
    initSeatSelection();
    initDatePicker();
    initAnimations();
});

/**
 * Header scroll effect
 */
function initHeader() {
    const header = document.querySelector('.header');
    if (!header) return;
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

/**
 * Seat Selection System
 */
function initSeatSelection() {
    const seatsContainer = document.querySelector('.seats-container');
    if (!seatsContainer) return;
    
    const seats = seatsContainer.querySelectorAll('.seat:not(.occupied)');
    const selectedSeatsInput = document.getElementById('selected-seats');
    const bookingSummary = document.querySelector('.booking-summary');
    const summarySeats = document.querySelector('.summary-seats');
    const totalAmount = document.querySelector('.total-amount');
    const ticketPrice = parseFloat(document.getElementById('ticket-price')?.value || 150);
    
    let selectedSeats = [];
    
    seats.forEach(seat => {
        seat.addEventListener('click', function() {
            const seatId = this.dataset.seatId;
            const seatLabel = this.dataset.row + this.dataset.number;
            
            if (this.classList.contains('selected')) {
                // Deselect
                this.classList.remove('selected');
                selectedSeats = selectedSeats.filter(s => s.id !== seatId);
            } else {
                // Select (max 10 seats)
                if (selectedSeats.length >= 10) {
                    showToast('En fazla 10 koltuk seçebilirsiniz!', 'warning');
                    return;
                }
                this.classList.add('selected');
                selectedSeats.push({
                    id: seatId,
                    label: seatLabel,
                    price: ticketPrice
                });
            }
            
            updateBookingSummary();
        });
    });
    
    function updateBookingSummary() {
        if (!bookingSummary) return;
        
        if (selectedSeats.length > 0) {
            bookingSummary.classList.add('active');
            
            // Update seats display
            if (summarySeats) {
                summarySeats.innerHTML = selectedSeats.map(seat => 
                    `<span class="selected-seat-tag">${seat.label}</span>`
                ).join('');
            }
            
            // Update total
            const total = selectedSeats.reduce((sum, seat) => sum + seat.price, 0);
            if (totalAmount) {
                totalAmount.textContent = formatPrice(total);
            }
            
            // Update hidden input
            if (selectedSeatsInput) {
                selectedSeatsInput.value = JSON.stringify(selectedSeats.map(s => s.id));
            }
        } else {
            bookingSummary.classList.remove('active');
        }
    }
}

/**
 * Date Picker for Showtimes
 */
function initDatePicker() {
    const dateItems = document.querySelectorAll('.date-item');
    if (dateItems.length === 0) return;
    
    dateItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active from all
            dateItems.forEach(d => d.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');
            
            // Get selected date
            const date = this.dataset.date;
            
            // Load showtimes for this date
            loadShowtimes(date);
        });
    });
}

/**
 * Load showtimes via AJAX
 */
async function loadShowtimes(date) {
    const movieId = document.getElementById('movie-id')?.value;
    if (!movieId) return;
    
    const container = document.querySelector('.showtimes-container');
    if (!container) return;
    
    // Show loading
    container.innerHTML = '<div class="skeleton" style="height: 200px;"></div>';
    
    try {
        const response = await fetch(`api/get-showtimes.php?movie_id=${movieId}&date=${date}`);
        const data = await response.json();
        
        if (data.success) {
            renderShowtimes(data.showtimes, container);
        } else {
            container.innerHTML = '<p class="text-center text-muted">Bu tarihte seans bulunamadı.</p>';
        }
    } catch (error) {
        console.error('Error loading showtimes:', error);
        container.innerHTML = '<p class="text-center text-muted">Seanslar yüklenirken hata oluştu.</p>';
    }
}

/**
 * Render showtimes HTML
 */
function renderShowtimes(showtimes, container) {
    if (!showtimes || showtimes.length === 0) {
        container.innerHTML = '<p class="text-center text-muted">Bu tarihte seans bulunamadı.</p>';
        return;
    }
    
    // Group by cinema
    const grouped = {};
    showtimes.forEach(st => {
        const key = st.cinema_name;
        if (!grouped[key]) {
            grouped[key] = {
                cinema_name: st.cinema_name,
                city: st.city,
                district: st.district,
                halls: {}
            };
        }
        
        const hallKey = st.hall_name;
        if (!grouped[key].halls[hallKey]) {
            grouped[key].halls[hallKey] = {
                hall_name: st.hall_name,
                hall_type: st.hall_type,
                times: []
            };
        }
        
        grouped[key].halls[hallKey].times.push(st);
    });
    
    let html = '';
    
    Object.values(grouped).forEach(cinema => {
        html += `
            <div class="cinema-card animate-slideUp">
                <div class="cinema-header">
                    <h3 class="cinema-name">${cinema.cinema_name}</h3>
                    <span class="cinema-location">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        ${cinema.district}, ${cinema.city}
                    </span>
                </div>
        `;
        
        Object.values(cinema.halls).forEach(hall => {
            html += `
                <div class="hall-row">
                    <div class="hall-info">
                        <span class="hall-name">${hall.hall_name}</span>
                        <span class="hall-type">${hall.hall_type}</span>
                    </div>
                    <div class="showtimes-list">
            `;
            
            hall.times.forEach(time => {
                html += `
                    <a href="index.php?page=select-seat&showtime=${time.id}" class="showtime-btn">
                        <span class="time">${formatTime(time.show_time)}</span>
                        <span class="price">${formatPrice(time.price)}</span>
                    </a>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        });
        
        html += `</div>`;
    });
    
    container.innerHTML = html;
}

/**
 * Initialize scroll animations
 */
function initAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-slideUp');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    document.querySelectorAll('.movie-card, .cinema-card').forEach(el => {
        observer.observe(el);
    });
}

/**
 * Format price to Turkish Lira
 */
function formatPrice(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        minimumFractionDigits: 0
    }).format(amount);
}

/**
 * Format time (HH:MM:SS -> HH:MM)
 */
function formatTime(time) {
    return time.substring(0, 5);
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-message">${message}</span>
    `;
    
    // Add styles
    Object.assign(toast.style, {
        position: 'fixed',
        top: '100px',
        right: '20px',
        background: type === 'warning' ? '#f59e0b' : type === 'error' ? '#ef4444' : '#3b82f6',
        color: 'white',
        padding: '15px 25px',
        borderRadius: '10px',
        boxShadow: '0 4px 20px rgba(0,0,0,0.3)',
        zIndex: '9999',
        animation: 'slideIn 0.3s ease-out'
    });
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add toast animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

/**
 * Checkout form validation
 */
function validateCheckoutForm(form) {
    const name = form.querySelector('[name="customer_name"]');
    const email = form.querySelector('[name="customer_email"]');
    const phone = form.querySelector('[name="customer_phone"]');
    
    let isValid = true;
    
    if (!name.value.trim()) {
        showFieldError(name, 'Ad Soyad gereklidir');
        isValid = false;
    }
    
    if (!email.value.trim() || !isValidEmail(email.value)) {
        showFieldError(email, 'Geçerli bir e-posta adresi giriniz');
        isValid = false;
    }
    
    if (phone.value && !isValidPhone(phone.value)) {
        showFieldError(phone, 'Geçerli bir telefon numarası giriniz');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    field.style.borderColor = '#ef4444';
    
    let errorEl = field.parentNode.querySelector('.field-error');
    if (!errorEl) {
        errorEl = document.createElement('span');
        errorEl.className = 'field-error';
        errorEl.style.cssText = 'color: #ef4444; font-size: 0.85rem; margin-top: 5px; display: block;';
        field.parentNode.appendChild(errorEl);
    }
    errorEl.textContent = message;
    
    field.addEventListener('input', function() {
        this.style.borderColor = '';
        if (errorEl) errorEl.remove();
    }, { once: true });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^[0-9\s\-\+\(\)]{10,}$/.test(phone);
}
