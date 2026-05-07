const monthNames = [
    "January","February","March","April","May","June",
    "July","August","September","October","November","December"
];

let currentDate = new Date();

function renderCalendar(dateObj) {
    const year = dateObj.getFullYear();
    const month = dateObj.getMonth();
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month+1, 0).getDate();

    document.getElementById('calendar-title').textContent =
        monthNames[month] + " " + year;

    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = "";

    let date = 1;
    for (let i = 0; i < 6; i++) {
        let row = document.createElement("tr");
        for (let j = 0; j < 7; j++) {
            let cell = document.createElement("td");
            if (i === 0 && j < firstDay) {
                cell.textContent = "";
            } else if (date > daysInMonth) {
                cell.textContent = "";
            } else {
                cell.textContent = date;

                // Highlight today
                const today = new Date();
                if (date === today.getDate() &&
                    month === today.getMonth() &&
                    year === today.getFullYear()) {
                    cell.classList.add("today-cell");
                }

                // Inject events
                if (window.calendarEvents) {
                    window.calendarEvents.forEach(ev => {
                        const evDate = new Date(ev.date); // only for matching the day

                        if (evDate.getDate() === date &&
                            evDate.getMonth() === month &&
                            evDate.getFullYear() === year) {
                            
                            let evSticker = document.createElement("div");
                            evSticker.classList.add("event-sticker");

                            // Convert DB time (HH:MM:SS) into AM/PM format
                            const [hourStr, minuteStr] = ev.time.split(':');
                            const hour = parseInt(hourStr, 10);
                            const minute = parseInt(minuteStr, 10);
                            const formattedTime = new Date(0, 0, 0, hour, minute)
                                .toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });

                            // Show name + formatted time
                            evSticker.innerHTML = `
                                <strong>${ev.title}</strong><br>
                                ${formattedTime}
                            `;

                            cell.appendChild(evSticker);
                        }
                    });
                }

                date++;
            }
            row.appendChild(cell);
        }
        calendarBody.appendChild(row);
    }
}

// Initial render
renderCalendar(currentDate);

// Navigation
document.getElementById("prevMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
});
document.getElementById("nextMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
});
