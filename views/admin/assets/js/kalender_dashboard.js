    const grid = document.querySelector('.grid-kalender');
    const header = document.querySelector('.header-kalender span:first-child');

    const today = new Date();
    const year = today.getFullYear();
    const month = today.getMonth(); // 0-indexed

    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    header.textContent = `${monthNames[month]} ${year}`;

    // Clear previous dates if needed
    grid.innerHTML = '';

    // Days of the week
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    for (const day of dayNames) {
        const dayEl = document.createElement('div');
        dayEl.className = 'hari';
        dayEl.textContent = day;
        grid.appendChild(dayEl);
    }

    // Find first day and number of days in month
    const firstDay = new Date(year, month, 1).getDay(); // 0 (Sun) to 6 (Sat)
    const lastDate = new Date(year, month + 1, 0).getDate(); // Last date of current month

    // Fill empty cells before 1st
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'tanggal empty';
        grid.appendChild(emptyCell);
    }

    // Fill the dates
    for (let i = 1; i <= lastDate; i++) {
        const dateCell = document.createElement('div');
        dateCell.className = 'tanggal';
        dateCell.textContent = i;

        if (
            i === today.getDate() &&
            month === today.getMonth() &&
            year === today.getFullYear()
        ) {
            dateCell.classList.add('today-fix');
        }

        grid.appendChild(dateCell);
    }