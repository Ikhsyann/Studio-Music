<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="booking-container">
    <div class="booking-header">
        <h1>Form Booking - <?= htmlspecialchars($studio['nama_studio']) ?></h1>
        <p>Isi data diri dan informasi booking Anda</p>
    </div>
    
    <div class="booking-content">
        <div class="studio-info-box">
            <h3>Informasi Studio</h3>
            <table class="info-table">
                <tr>
                    <td>Nama Studio</td>
                    <td>: <?= htmlspecialchars($studio['nama_studio']) ?></td>
                </tr>
                <tr>
                    <td>Harga per Jam</td>
                    <td>: Rp. <?= number_format($studio['harga_per_jam'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Kapasitas</td>
                    <td>: <?= $studio['kapasitas'] ?> orang</td>
                </tr>
                <tr>
                    <td>Fasilitas</td>
                    <td>: <?= htmlspecialchars($studio['fasilitas']) ?></td>
                </tr>
            </table>
        </div>
        
        <form action="/Studio-Music/public/index.php?url=user/bookingProcess" method="POST" class="booking-form">
            <input type="hidden" name="id_studio" value="<?= $studio['id_studio'] ?>">
            
            <h3>Data Diri</h3>
            <div class="form-row">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="no_telp">No. Telepon</label>
                    <input type="tel" id="no_telp" name="no_telp" class="form-control" value="<?= htmlspecialchars($user['no_telp']) ?>" readonly>
                </div>
            </div>
            
            <h3>Informasi Booking</h3>
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="tanggal_main">Tanggal Main</label>
                    <input type="date" id="tanggal_main" name="tanggal_main" class="form-control" min="<?= date('Y-m-d') ?>" required value="<?= htmlspecialchars(old('tanggal_main', '')) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Pilih Jam (Bisa pilih lebih dari 1 jam)</label>
                <p class="info-text">FYI kapasitas studio kami sesuai product yang dipilih 20 orang ya kak, lebih dari itu nanti hasil nya kurang optimal hihi</p>
                <div id="time-slots" class="time-slots-grid">
                    <!-- Time slots will be loaded here via JavaScript -->
                </div>
                <input type="hidden" id="selected_hours" name="selected_hours" required value="<?= htmlspecialchars(old('selected_hours', '')) ?>">
            </div>
            
            <div class="form-group">
                <label>Estimasi Total Bayar</label>
                <div class="price-display" id="total-display">Rp. 0</div>
                <small>*Harga: Rp. <?= number_format($studio['harga_per_jam'], 0, ',', '.') ?> per jam</small>
            </div>
            
            <div class="form-actions">
                <a href="/Studio-Music/public/index.php?url=user/dashboard" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Lanjut ke Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    const hargaPerJam = <?= $studio['harga_per_jam'] ?>;
    const studioId = <?= $studio['id_studio'] ?>;
    const tanggalInput = document.getElementById('tanggal_main');
    const timeSlotsContainer = document.getElementById('time-slots');
    const totalDisplay = document.getElementById('total-display');
    const selectedHoursInput = document.getElementById('selected_hours');
    
    let selectedSlots = [];
    let bookedHours = [];
    
    // Generate slot waktu dari 10:00 sampai 21:00
    const availableHours = [];
    for (let i = 10; i <= 21; i++) {
        availableHours.push(i);
    }
    
    function renderTimeSlots() {
        timeSlotsContainer.innerHTML = '';
        
        availableHours.forEach(hour => {
            const timeSlot = document.createElement('button');
            timeSlot.type = 'button';
            timeSlot.className = 'time-slot';
            timeSlot.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timeSlot.dataset.hour = hour;
            
            // Cek apakah jam ini sudah dibooking
            const isBooked = bookedHours.some(booked => {
                const bookedStart = parseInt(booked.start);
                const bookedEnd = parseInt(booked.end);
                return hour >= bookedStart && hour < bookedEnd;
            });
            
            if (isBooked) {
                timeSlot.classList.add('disabled');
                timeSlot.disabled = true;
            } else {
                timeSlot.addEventListener('click', () => toggleTimeSlot(hour, timeSlot));
            }
            
            timeSlotsContainer.appendChild(timeSlot);
        });
    }
    
    function toggleTimeSlot(hour, element) {
        const index = selectedSlots.indexOf(hour);
        
        if (index > -1) {
            selectedSlots.splice(index, 1);
            element.classList.remove('selected');
        } else {
            selectedSlots.push(hour);
            element.classList.add('selected');
        }
        
        selectedSlots.sort((a, b) => a - b);
        updateTotal();
        updateHiddenInput();
    }
    
    function updateTotal() {
        const total = selectedSlots.length * hargaPerJam;
        totalDisplay.textContent = 'Rp. ' + total.toLocaleString('id-ID');
    }
    
    function updateHiddenInput() {
        selectedHoursInput.value = JSON.stringify(selectedSlots);
    }
    
    async function loadBookedHours() {
        const tanggal = tanggalInput.value;
        if (!tanggal) {
            bookedHours = [];
            renderTimeSlots();
            return;
        }
        
        try {
            const response = await fetch(`/Studio-Music/public/index.php?url=user/getBookedHours&id_studio=${studioId}&tanggal=${tanggal}`);
            const data = await response.json();
            
            bookedHours = data.map(booking => ({
                start: parseInt(booking.jam_mulai.split(':')[0]),
                end: parseInt(booking.jam_selesai.split(':')[0])
            }));
            
            selectedSlots = [];
            renderTimeSlots();
            updateTotal();
            updateHiddenInput();
        } catch (error) {
            console.error('Error saat memuat jam yang sudah dibooking:', error);
            renderTimeSlots();
        }
    }
    
    tanggalInput.addEventListener('change', loadBookedHours);
    
    // Render awal
    renderTimeSlots();
    
    // Validasi form
    document.querySelector('.booking-form').addEventListener('submit', function(e) {
        if (selectedSlots.length === 0) {
            e.preventDefault();
            alert('Silakan pilih minimal 1 jam untuk booking');
        }
    });
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
