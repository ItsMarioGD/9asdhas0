<?php include 'includes/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-2xl shadow-sm">
                <i class="fa-solid fa-calendar-days text-3xl text-blue-600"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Calendario de Citas</h2>
                <p class="text-gray-500">Visualiza y gestiona todas las citas programadas</p>
            </div>
        </div>
        <div class="flex gap-3">
            <button onclick="openBlockModal()" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-bold shadow-lg hover:bg-gray-900 transition flex items-center gap-2">
                <i class="fa-solid fa-ban"></i>
                Bloquear Horario
            </button>
            <a href="reception.php" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                Nueva Cita
            </a>
        </div>
    </div>

    <!-- Calendar Navigation -->
    <div class="glass-panel rounded-3xl shadow-lg p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <button onclick="changeMonth(-1)" class="p-3 rounded-xl hover:bg-gray-100 transition">
                <i class="fa-solid fa-chevron-left text-xl"></i>
            </button>
            <h3 id="current-month" class="text-2xl font-bold text-gray-900"></h3>
            <button onclick="changeMonth(1)" class="p-3 rounded-xl hover:bg-gray-100 transition">
                <i class="fa-solid fa-chevron-right text-xl"></i>
            </button>
        </div>

        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 gap-2 mb-4">
            <div class="text-center font-bold text-gray-500 py-2">Dom</div>
            <div class="text-center font-bold text-gray-500 py-2">Lun</div>
            <div class="text-center font-bold text-gray-500 py-2">Mar</div>
            <div class="text-center font-bold text-gray-500 py-2">Mié</div>
            <div class="text-center font-bold text-gray-500 py-2">Jue</div>
            <div class="text-center font-bold text-gray-500 py-2">Vie</div>
            <div class="text-center font-bold text-gray-500 py-2">Sáb</div>
        </div>
        <div id="calendar-grid" class="grid grid-cols-7 gap-2">
            <!-- Populated by JS -->
        </div>
    </div>

    <!-- Appointments List for Selected Day -->
    <div id="day-appointments" class="hidden glass-panel rounded-3xl shadow-lg p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
            <i class="fa-solid fa-clock text-blue-600"></i>
            Citas del <span id="selected-day-text"></span>
        </h3>
        <div id="appointments-list" class="space-y-4">
            <!-- Populated by JS -->
        </div>
    </div>
</div>

<!-- Block Slot Modal -->
<div id="block-modal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl animate-fade-in">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-ban text-red-500"></i> Bloquear Horario
        </h3>
        
        <form id="block-form" onsubmit="submitBlock(event)" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Doctor</label>
                <select id="block_doctor" class="w-full rounded-xl border-gray-200 py-3 px-4 bg-gray-50" required>
                    <!-- Populated by JS -->
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Inicio</label>
                    <input type="datetime-local" id="block_start" class="w-full rounded-xl border-gray-200 py-3 px-4 bg-gray-50" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Fin</label>
                    <input type="datetime-local" id="block_end" class="w-full rounded-xl border-gray-200 py-3 px-4 bg-gray-50" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Motivo</label>
                <input type="text" id="block_reason" placeholder="Ej: Almuerzo, Emergencia" class="w-full rounded-xl border-gray-200 py-3 px-4 bg-gray-50" required>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeBlockModal()" class="flex-1 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-500/30">
                    Bloquear
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentDate = new Date();
let allAppointments = [];

async function loadAppointments() {
    try {
        const response = await fetch('api/get_appointments.php');
        const data = await response.json();
        allAppointments = data;
        renderCalendar();
    } catch (e) {
        console.error('Error loading appointments:', e);
    }
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update month display
    const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    document.getElementById('current-month').textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and total days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    
    const grid = document.getElementById('calendar-grid');
    grid.innerHTML = '';
    
    // Empty cells before first day
    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'p-4';
        grid.appendChild(emptyCell);
    }
    
    // Days of month
    for (let day = 1; day <= daysInMonth; day++) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const appointmentsForDay = allAppointments.filter(apt => apt.date_time.startsWith(dateStr));
        
        const dayCell = document.createElement('div');
        dayCell.className = 'p-4 rounded-xl border border-gray-200 hover:bg-blue-50 cursor-pointer transition min-h-[100px] relative';
        
        if (appointmentsForDay.length > 0) {
            dayCell.classList.add('bg-blue-50', 'border-blue-300');
        }
        
        const isToday = new Date().toDateString() === new Date(year, month, day).toDateString();
        if (isToday) {
            dayCell.classList.add('ring-2', 'ring-blue-600');
        }
        
        dayCell.innerHTML = `
            <div class="text-sm font-bold text-gray-900 mb-2">${day}</div>
            ${appointmentsForDay.length > 0 ? `
                <div class="absolute bottom-2 right-2 bg-blue-600 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center font-bold">
                    ${appointmentsForDay.length}
                </div>
            ` : ''}
        `;
        
        dayCell.onclick = () => showDayAppointments(dateStr, appointmentsForDay);
        grid.appendChild(dayCell);
    }
}

function showDayAppointments(dateStr, appointments) {
    const container = document.getElementById('day-appointments');
    const list = document.getElementById('appointments-list');
    const dateText = document.getElementById('selected-day-text');
    
    const date = new Date(dateStr + 'T00:00:00');
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    dateText.textContent = date.toLocaleDateString('es-ES', options);
    
    if (appointments.length === 0) {
        list.innerHTML = '<p class="text-gray-500 italic">No hay citas para este día</p>';
    } else {
        list.innerHTML = appointments.map(apt => {
            const time = new Date(apt.date_time).toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
            const statusColors = {
                'scheduled': 'bg-blue-100 text-blue-800',
                'completed': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            const statusColor = statusColors[apt.status] || 'bg-gray-100 text-gray-800';
            
            const isCancellable = apt.status === 'scheduled';
            
            return `
                <div class="bg-white p-6 rounded-xl border border-gray-200 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fa-solid fa-user text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">${apt.patient_name}</h4>
                                <p class="text-sm text-gray-500">Dr. ${apt.doctor_name}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 ${statusColor} rounded-lg text-xs font-semibold uppercase">
                                ${apt.status}
                            </span>
                            ${isCancellable ? `
                                <button onclick="cancelAppointment(${apt.id})" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition" title="Cancelar Cita">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fa-solid fa-clock"></i>
                            <span>${time}</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fa-solid fa-notes-medical"></i>
                            <span>${apt.reason || 'Sin motivo'}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
    
    container.classList.remove('hidden');
    container.scrollIntoView({ behavior: 'smooth' });
}

function changeMonth(delta) {
    currentDate.setMonth(currentDate.getMonth() + delta);
    renderCalendar();
}

async function cancelAppointment(id) {
    if (!confirm('¿Estás seguro de que deseas cancelar esta cita?')) return;
    
    try {
        const response = await fetch('api/cancel_appointment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Cita cancelada correctamente');
            loadAppointments();
            document.getElementById('day-appointments').classList.add('hidden');
        } else {
            alert('Error al cancelar: ' + result.error);
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

async function openBlockModal() {
    const modal = document.getElementById('block-modal');
    const select = document.getElementById('block_doctor');
    
    if (select.children.length === 0) {
        try {
            const res = await fetch('api/get_doctors_list.php');
            const doctors = await res.json();
            
            select.innerHTML = doctors.map(d => `<option value="${d.id}">${d.name}</option>`).join('');
        } catch (e) {
            alert('Error cargando doctores');
            return;
        }
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeBlockModal() {
    document.getElementById('block-modal').classList.add('hidden');
    document.getElementById('block-modal').classList.remove('flex');
}

async function submitBlock(e) {
    e.preventDefault();
    
    const data = {
        doctor_id: document.getElementById('block_doctor').value,
        start_time: document.getElementById('block_start').value,
        end_time: document.getElementById('block_end').value,
        reason: document.getElementById('block_reason').value
    };
    
    try {
        const res = await fetch('api/block_slot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        
        if (result.success) {
            alert('Horario bloqueado correctamente');
            closeBlockModal();
            loadAppointments();
        } else {
            alert('Error: ' + result.error);
        }
    } catch (e) {
        alert('Error de conexión');
    }
}

// Load on page load
loadAppointments();
</script>

<?php include 'includes/footer.php'; ?>
