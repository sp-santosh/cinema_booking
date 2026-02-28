/**
 * app.js - Frontend Interactivity for CineBook
 */

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Seat Selection Logic
    const bookingForm = document.getElementById('booking-form');
    
    if (bookingForm) {
        const checkboxes = bookingForm.querySelectorAll('.seat-checkbox');
        const seatCountDisplay = document.getElementById('seat-count');
        const seatTotalDisplay = document.getElementById('seat-total');
        const bookingSummary = document.getElementById('booking-summary');
        
        // Define default ticket price matching constant
        const TICKET_PRICE = 12.50;

        function updateSummary() {
            const selectedSeats = Array.from(checkboxes).filter(cb => cb.checked);
            const count = selectedSeats.length;
            
            // Visual toggle label color
            checkboxes.forEach(cb => {
                const label = cb.parentElement;
                if (cb.checked) {
                    label.classList.add('seat--selected');
                    label.classList.remove('seat--available');
                } else {
                    label.classList.remove('seat--selected');
                    label.classList.add('seat--available');
                }
            });

            if (count > 0) {
                seatCountDisplay.textContent = count;
                seatTotalDisplay.textContent = '£' + (count * TICKET_PRICE).toFixed(2);
                bookingSummary.style.display = 'flex';
            } else {
                bookingSummary.style.display = 'none';
            }
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateSummary);
        });
        
        // Initial setup for form repopulation (e.g. back button or validation fail)
        updateSummary();
    }

    // 2. Flash Message Auto-dismiss
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        // Automatically hide success and info alerts after 5 seconds
        if (alert.classList.contains('alert--success') || alert.classList.contains('alert--info')) {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 5000);
        }
    });

    // 3. Confirm Deletions dynamically
    const confirmButtons = document.querySelectorAll('[data-confirm]');
    confirmButtons.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-confirm') || 'Are you sure?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

});
