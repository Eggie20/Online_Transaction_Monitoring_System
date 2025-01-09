document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const transactionForm = document.querySelector('form');
    if (transactionForm) {
        transactionForm.addEventListener('submit', function(e) {
            const amount = document.querySelector('input[name="amount"]').value;
            if (amount <= 0) {
                e.preventDefault();
                alert('Please enter a valid amount greater than 0');
                return false;
            }
        });
    }

    // Auto-format amount input
    const amountInput = document.querySelector('input[name="amount"]');
    if (amountInput) {
        amountInput.addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/[^\d.]/g, '');
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            e.target.value = value;
        });
    }

    // Set default date and time
    const dateInput = document.querySelector('input[name="date"]');
    const timeInput = document.querySelector('input[name="time"]');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }
    if (timeInput && !timeInput.value) {
        const now = new Date();
        timeInput.value = `${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}`;
    }

    // Start the main clock
    updateClock();
    setInterval(updateClock, 1000);

    // Add transaction time display to the form
    const form = document.querySelector('form');
    if (form) {
        const timeDisplay = document.createElement('div');
        timeDisplay.className = 'transaction-time-container';
        timeDisplay.innerHTML = '<div class="transaction-time"></div>';
        form.insertBefore(timeDisplay, form.firstChild);
        
        // Start the transaction time update
        updateTransactionTime();
        setInterval(updateTransactionTime, 1000);
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#transactionTable tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Filter functionality
    const filterSelect = document.getElementById('filterSelect');
    if (filterSelect) {
        filterSelect.addEventListener('change', function() {
            const column = this.value;
            if (!column) return;
            
            const tableRows = Array.from(document.querySelectorAll('#transactionTable tbody tr'));
            const columnIndex = getColumnIndex(column);
            
            tableRows.sort((a, b) => {
                const aValue = a.cells[columnIndex].textContent;
                const bValue = b.cells[columnIndex].textContent;
                return aValue.localeCompare(bValue);
            });
            
            const tbody = document.querySelector('#transactionTable tbody');
            tableRows.forEach(row => tbody.appendChild(row));
        });
    }
    
    function getColumnIndex(column) {
        const columns = {
            'name': 0,
            'method': 1,
            'amount': 3,
            'date': 4
        };
        return columns[column] || 0;
    }
}); 

// Function para sa current date at time
function updateDateTime() {
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    
    const currentDate = now.toLocaleDateString('tl-PH', dateOptions);
    const currentTime = now.toLocaleTimeString('tl-PH', timeOptions);
    
    document.getElementById('current-date').textContent = currentDate;
    document.getElementById('current-time').textContent = currentTime;
}

// Auto-generate transaction number
function generateTransactionNumber() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const random = Math.floor(Math.random() * 10000).toString().padStart(4, '0');
    
    return `TXN-${year}${month}${day}-${random}`;
}

// Update every second
setInterval(updateDateTime, 1000);

// Initial update
updateDateTime(); 

// Update the clock function
function updateClock() {
    const now = new Date();
    const options = { 
        timeZone: 'Asia/Manila',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };
    
    const dateOptions = { 
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        timeZone: 'Asia/Manila'
    };

    const timeString = now.toLocaleTimeString('en-US', options);
    const dateString = now.toLocaleDateString('en-US', dateOptions);

    document.getElementById('digital-clock').innerHTML = `
        <div class="clock-container">
            <div class="time">${timeString}</div>
            <div class="date">${dateString}</div>
        </div>
    `;
}

// Update transaction time display
function updateTransactionTime() {
    const timeContainer = document.querySelector('.transaction-time-container');
    if (timeContainer) {
        const now = new Date();
        const options = {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        };
        
        timeContainer.querySelector('.transaction-time').textContent = 
            now.toLocaleTimeString('en-US', options);
    }
} 