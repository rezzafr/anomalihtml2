<?php
// --- 1. BACKEND LOGIC ---
session_start();
require_once 'includes/db_connect.php'; // Ensure path is correct!

// Check Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Futsal Courts from Database
$sql = "SELECT * FROM services WHERE type = 'Badminton'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Badminton</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        .booking-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .sidebar { background-color: #003380; color: white; min-height: 600px; }
        
        /* Steps Sidebar */
        .step-item { padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; align-items: center; opacity: 0.6; transition: 0.3s; }
        .step-item.active { background-color: rgba(255,255,255,0.1); opacity: 1; font-weight: bold; }
        .step-item.done { color: #4ade80; opacity: 1; }
        
        /* Calendar Styles (Restored) */
        .calendar-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 10px; text-align: center; }
        .day-name { font-weight: 600; color: #6c757d; margin-bottom: 10px; }
        .calendar-day {
            height: 45px; width: 45px; margin: 0 auto; display: flex; align-items: center; justify-content: center;
            border-radius: 50%; font-weight: 500; cursor: default;
        }
        .calendar-day.past { background-color: #f2f2f2; color: #d6d6d6; cursor: not-allowed; }
        .calendar-day.available { background-color: #d1e7dd; color: #0f5132; border: 1px solid #198754; cursor: pointer; }
        .calendar-day.available:hover { background-color: #198754; color: white; }
        .calendar-day.selected { background-color: #0d6efd !important; color: white !important; border-color: #0d6efd !important; }

        /* Court Selection */
        .court-option { cursor: pointer; border: 2px solid #dee2e6; transition: all 0.2s; }
        .court-option:hover { border-color: #0d6efd; background-color: #f8f9fa; }
        .court-option.selected { border-color: #0d6efd; background-color: #e7f1ff; color: #003380; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<?php include 'template.php'; ?>

<main class="container mb-5 mt-5">
    <h2 class="text-center mb-4">Book Your Badminton Slot</h2>

    <?php if (isset($_GET['error']) || isset($_GET['msg'])): ?>
        <?php 
            $alertType = isset($_GET['error']) ? 'danger' : 'success';
            $alertText = isset($_GET['error']) ? $_GET['error'] : $_GET['msg'];
        ?>
        <div class="alert alert-<?php echo $alertType; ?> text-center" role="alert">
            <?php echo htmlspecialchars($alertText); ?>
        </div>
    <?php endif; ?>

    <div class="card booking-card mx-auto" style="max-width: 900px;">
        <div class="row g-0">
            <div class="col-md-4 sidebar d-flex flex-column p-4">
                <h4 class="mb-4">Booking Steps</h4>
                <div class="mb-4">
                    <div class="step-item active" id="step1-indicator">
                        <span class="me-3">🏟️</span> Select Court
                    </div>
                    <div class="step-item" id="step2-indicator">
                        <span class="me-3">📅</span> Date & Time
                    </div>
                    <div class="step-item" id="step3-indicator">
                        <span class="me-3">👤</span> Details
                    </div>
                </div>
                <div class="mt-auto">
                    <small>Need Help?</small>
                    <div class="fw-bold">support@sportsbooking.com</div>
                </div>
            </div>

            <div class="col-md-8 p-4 bg-white">
                
                <div id="section-court">
                    <h4 class="mb-4">Choose a Court</h4>
                    <div class="row g-3 mb-4">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <div class="col-12">
                                    <div class="card p-3 court-option" 
                                         onclick="selectCourt(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>', this)">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span><?php echo htmlspecialchars($row['name']); ?></span>
                                            <span class="badge bg-primary">RM <?php echo $row['price_per_hour']; ?>/hr</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No courts found in database.</p>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary px-4" id="btnToDate" disabled>Next</button>
                    </div>
                </div>

                <div id="section-calendar" class="d-none">
                    <div class="d-flex align-items-center mb-4">
                        <button class="btn btn-sm btn-outline-secondary me-3" id="btnBackToCourt">← Back</button>
                        <h4 class="mb-0">Select Date & Time</h4>
                    </div>

                    <div class="calendar-header">
                        <h5 class="fw-bold mb-0" id="currentMonthYear">Month Year</h5>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm" id="prevMonth">&lt;</button>
                            <button class="btn btn-outline-secondary btn-sm" id="nextMonth">&gt;</button>
                        </div>
                    </div>

                    <div class="calendar-grid mb-2">
                        <div class="day-name">Mon</div><div class="day-name">Tue</div><div class="day-name">Wed</div>
                        <div class="day-name">Thu</div><div class="day-name">Fri</div><div class="day-name">Sat</div>
                        <div class="day-name">Sun</div>
                    </div>
                    
                    <div class="calendar-grid mb-4" id="calendarDays"></div>

                    <div class="mb-3 border-top pt-3">
                        <label class="form-label fw-bold">Select Time Slot</label>
                        <select class="form-select" id="timeSlotSelect" disabled>
                            <option selected disabled>Select a date first...</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary px-4" id="btnToForm" disabled>Next</button>
                    </div>
                </div>

                <div id="section-form" class="d-none">
                    <div class="d-flex align-items-center mb-4">
                        <button class="btn btn-sm btn-outline-secondary me-3" id="btnBackToDate">← Back</button>
                        <h4 class="mb-0">Confirm Details</h4>
                    </div>

                    <form action="booking.php" method="POST" id="bookingForm">
                        <input type="hidden" name="service_id" id="input_service_id">
                        <input type="hidden" name="booking_date" id="input_booking_date">
                        <input type="hidden" name="time_slot" id="input_time_slot">

                        <div class="bg-light p-3 rounded mb-3">
                            <div class="row">
                                <div class="col-4 text-muted">Court:</div>
                                <div class="col-8 fw-bold" id="summaryCourt">...</div>
                                <div class="col-4 text-muted">Date:</div>
                                <div class="col-8 fw-bold" id="summaryDate">...</div>
                                <div class="col-4 text-muted">Time:</div>
                                <div class="col-8 fw-bold" id="summaryTime">...</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="customer_name" value="<?php echo $_SESSION['username']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="customer_phone" required pattern="[0-9]{9,11}" title="Please enter a valid phone number (9–11 digits, numbers only)"inputmode="numeric" placeholder="e.g. 0123456789">

                        </div>
                        <script>
                            document.getElementById('bookingForm').addEventListener('submit', function(e) {
                            const phoneInput = document.querySelector('input[name="customer_phone"]');
                            const phone = phoneInput.value;
                            if (!/^[0-9]{9,11}$/.test(phone)) {
                            e.preventDefault();
                            alert("Please enter a valid phone number (numbers only).");
                            phoneInput.focus();
                        }
                    });
                    </script>


                        <button type="submit" class="btn btn-success w-100 py-2 mt-2">Confirm Payment & Book</button>
                    </form>
                </div>

            </div> 
        </div> 
    </div> 
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // --- VARIABLES ---
    let bookingData = { courtId: null, courtName: null, date: null, timeSlot: null };
    let currentDate = new Date();

    // DOM Elements
    const sectionCourt = document.getElementById('section-court');
    const sectionCalendar = document.getElementById('section-calendar');
    const sectionForm = document.getElementById('section-form');

    const calendarDays = document.getElementById('calendarDays');
    const currentMonthYear = document.getElementById('currentMonthYear');
    const timeSlotSelect = document.getElementById('timeSlotSelect');

    const btnToDate = document.getElementById('btnToDate');
    const btnToForm = document.getElementById('btnToForm');

    // --- STEP 1: COURT SELECTION ---
    window.selectCourt = function(id, name, element) {
        document.querySelectorAll('.court-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        bookingData.courtId = id;
        bookingData.courtName = name;
        btnToDate.disabled = false;
    }

    btnToDate.addEventListener('click', () => {
        sectionCourt.classList.add('d-none');
        sectionCalendar.classList.remove('d-none');
        updateSidebar(2);
        renderCalendar(); // Generate the grid!
    });

    // --- STEP 2: CALENDAR LOGIC ---
    function renderCalendar() {
        calendarDays.innerHTML = '';
        const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        currentMonthYear.innerText = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;

        const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1).getDay();
        // Adjust so Monday is 0 (or Sunday is 0 depending on your preference. Here Sunday is 0)
        // If your CSS grid starts Mon, we need to adjust logic. 
        // Standard JS getDay(): 0=Sun, 1=Mon.
        // Let's assume Grid is Mon-Sun. So 1=Mon(0), 0=Sun(6).
        let adjustedFirstDay = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1; 

        const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();

        // Empty slots
        for (let i = 0; i < adjustedFirstDay; i++) {
            calendarDays.appendChild(document.createElement('div'));
        }

        // Days
        for (let i = 1; i <= daysInMonth; i++) {
            const dayDiv = document.createElement('div');
            dayDiv.classList.add('calendar-day');
            dayDiv.innerText = i;

            const checkDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), i);
            const today = new Date();
            today.setHours(0,0,0,0);

            if (checkDate < today) {
                dayDiv.classList.add('past');
            } else {
                dayDiv.classList.add('available');
                dayDiv.addEventListener('click', () => {
                    // Highlight
                    document.querySelectorAll('.calendar-day').forEach(d => d.classList.remove('selected'));
                    dayDiv.classList.add('selected');
                    
                    // SAVE DATE (YYYY-MM-DD Format for PHP)
                    // We need to be careful with Timezones. 
                    // Simple fix: 
                    let year = checkDate.getFullYear();
                    let month = String(checkDate.getMonth() + 1).padStart(2, '0');
                    let day = String(checkDate.getDate()).padStart(2, '0');
                    bookingData.date = `${year}-${month}-${day}`;
                    
                    // Generate Times
                    timeSlotSelect.disabled = false;
                    generateTimeSlots();
                });
            }
            calendarDays.appendChild(dayDiv);
        }
    }

    function generateTimeSlots() {
        timeSlotSelect.innerHTML = '<option selected disabled>Select Time Slot...</option>';
        for (let i = 8; i < 22; i += 2) { 
            let start = formatTime(i);
            let end = formatTime(i + 2);
            let slot = `${start} - ${end}`;
            let option = document.createElement('option');
            option.value = slot;
            option.text = slot;
            timeSlotSelect.appendChild(option);
        }
    }

    function formatTime(hour) {
        let ampm = hour >= 12 && hour < 24 ? 'PM' : 'AM';
        let displayHour = hour % 12;
        if (displayHour === 0) displayHour = 12;
        return `${displayHour}:00 ${ampm}`;
    }

    timeSlotSelect.addEventListener('change', function() {
        bookingData.timeSlot = this.value;
        btnToForm.disabled = false;
    });

    document.getElementById('prevMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar();
    });
    document.getElementById('nextMonth').addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar();
    });

    // --- STEP 2 -> 3 ---
    btnToForm.addEventListener('click', () => {
        // Fill Summary
        document.getElementById('summaryCourt').innerText = bookingData.courtName;
        document.getElementById('summaryDate').innerText = bookingData.date;
        document.getElementById('summaryTime').innerText = bookingData.timeSlot;

        // FILL HIDDEN INPUTS FOR PHP
        document.getElementById('input_service_id').value = bookingData.courtId;
        document.getElementById('input_booking_date').value = bookingData.date;
        document.getElementById('input_time_slot').value = bookingData.timeSlot;

        sectionCalendar.classList.add('d-none');
        sectionForm.classList.remove('d-none');
        updateSidebar(3);
    });

    // --- NAVIGATION HELPERS ---
    document.getElementById('btnBackToCourt').addEventListener('click', () => {
        sectionCalendar.classList.add('d-none');
        sectionCourt.classList.remove('d-none');
        updateSidebar(1);
    });
    document.getElementById('btnBackToDate').addEventListener('click', (e) => {
        e.preventDefault();
        sectionForm.classList.add('d-none');
        sectionCalendar.classList.remove('d-none');
        updateSidebar(2);
    });

    function updateSidebar(step) {
        // Simple class switching
        const s1 = document.getElementById('step1-indicator');
        const s2 = document.getElementById('step2-indicator');
        const s3 = document.getElementById('step3-indicator');
        
        s1.classList.remove('active', 'done');
        s2.classList.remove('active', 'done');
        s3.classList.remove('active', 'done');

        if(step === 1) s1.classList.add('active');
        if(step === 2) { s1.classList.add('done'); s2.classList.add('active'); }
        if(step === 3) { s1.classList.add('done'); s2.classList.add('done'); s3.classList.add('active'); }
    }
});
</script>

</body>
</html>