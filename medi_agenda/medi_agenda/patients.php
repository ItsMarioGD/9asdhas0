<?php include 'includes/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-2xl shadow-sm">
                <i class="fa-solid fa-users text-3xl text-blue-600"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Directorio de Pacientes</h2>
                <p class="text-gray-500">Gestiona expedientes e historiales médicos</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="glass-panel rounded-3xl p-6 mb-8">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fa-solid fa-search text-gray-400"></i>
            </div>
            <input type="text" id="search-patient" 
                class="block w-full pl-11 pr-4 py-4 rounded-xl border-gray-200 bg-gray-50/50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none text-lg"
                placeholder="Buscar por nombre o teléfono..." onkeyup="filterPatients()">
        </div>
    </div>

    <!-- Patients Grid -->
    <div id="patients-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading State -->
        <div class="col-span-full text-center py-12">
            <i class="fa-solid fa-circle-notch fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-gray-500">Cargando pacientes...</p>
        </div>
    </div>
</div>

<script>
let allPatients = [];

async function loadPatients() {
    try {
        const response = await fetch('api/get_patients.php');
        const data = await response.json();
        allPatients = data;
        renderPatients(data);
    } catch (e) {
        console.error('Error:', e);
        document.getElementById('patients-grid').innerHTML = `
            <div class="col-span-full text-center py-12 text-red-500">
                <i class="fa-solid fa-triangle-exclamation text-4xl mb-4"></i>
                <p>Error al cargar pacientes</p>
            </div>
        `;
    }
}

function renderPatients(patients) {
    const grid = document.getElementById('patients-grid');
    
    if (patients.length === 0) {
        grid.innerHTML = `
            <div class="col-span-full text-center py-12 glass-panel rounded-3xl">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-user-slash text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900">No se encontraron pacientes</h3>
                <p class="text-gray-500 mt-2">Intenta con otra búsqueda o registra una cita nueva.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = patients.map(p => `
        <div class="glass-panel rounded-2xl p-6 hover:shadow-xl transition group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                <i class="fa-solid fa-user-circle text-8xl text-blue-600 transform translate-x-4 -translate-y-4"></i>
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        ${p.name.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 line-clamp-1">${p.name}</h3>
                        <p class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fa-solid fa-phone text-xs"></i> ${p.phone || 'Sin teléfono'}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-blue-600">${p.total_appointments || 0}</div>
                        <div class="text-xs text-blue-600 font-semibold uppercase">Citas</div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-3 text-center">
                        <div class="text-2xl font-bold text-green-600">${p.last_visit ? timeAgo(new Date(p.last_visit)) : '-'}</div>
                        <div class="text-xs text-green-600 font-semibold uppercase">Última Visita</div>
                    </div>
                </div>

                <a href="patient_history.php?id=${p.id}" class="block w-full py-3 bg-gray-900 text-white text-center rounded-xl font-bold hover:bg-blue-600 transition shadow-lg">
                    Ver Expediente <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    `).join('');
}

function filterPatients() {
    const term = document.getElementById('search-patient').value.toLowerCase();
    const filtered = allPatients.filter(p => 
        p.name.toLowerCase().includes(term) || 
        (p.phone && p.phone.includes(term))
    );
    renderPatients(filtered);
}

function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    let interval = seconds / 31536000;
    if (interval > 1) return Math.floor(interval) + " años";
    interval = seconds / 2592000;
    if (interval > 1) return Math.floor(interval) + " m";
    interval = seconds / 86400;
    if (interval > 1) return Math.floor(interval) + " d";
    return "Hoy";
}

loadPatients();
</script>

<?php include 'includes/footer.php'; ?>
