<?php include 'includes/header.php'; ?>

<div class="max-w-3xl mx-auto">
    <div class="glass-panel rounded-3xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                    <i class="fa-brands fa-whatsapp text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">Recepción Inteligente</h2>
                    <p class="text-blue-100">Agenda citas desde mensajes de WhatsApp</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <!-- Tabs -->
            <div class="flex gap-2 mb-6 border-b border-gray-200">
                <button onclick="switchTab('whatsapp')" id="tab-whatsapp" class="tab-btn px-6 py-3 font-bold text-blue-600 border-b-2 border-blue-600">
                    <i class="fa-brands fa-whatsapp mr-2"></i>WhatsApp
                </button>
                <button onclick="switchTab('manual')" id="tab-manual" class="tab-btn px-6 py-3 font-bold text-gray-500 border-b-2 border-transparent hover:text-gray-700">
                    <i class="fa-solid fa-keyboard mr-2"></i>Manual
                </button>
            </div>

            <!-- WhatsApp Tab -->
            <div id="whatsapp-tab" class="tab-content">
                <div class="mb-8">
                    <label for="magic-input" class="block text-sm font-bold text-gray-700 mb-3 uppercase tracking-wide">
                        Input Mágico
                    </label>
                    <div class="relative">
                        <textarea id="magic-input" rows="5"
                            class="block w-full rounded-2xl border-gray-200 shadow-inner focus:border-blue-500 focus:ring-blue-500 p-6 bg-gray-50 resize-none leading-relaxed"
                            placeholder="Ej: Hola, quiero cita para mi papá Juan Pérez el viernes a las 3pm por dolor de espalda"></textarea>
                        <div class="absolute bottom-3 right-3">
                            <button id="process-btn" onclick="processMagicInput()"
                                class="flex items-center gap-2 px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition transform hover:scale-105">
                                <i id="btn-icon" class="fa-solid fa-magic-wand-sparkles"></i>
                                <span id="btn-text">Procesar con IA</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-3 flex items-center gap-2">
                        <i class="fa-solid fa-lightbulb text-yellow-500"></i>
                        Pega un mensaje de WhatsApp y la IA extraerá automáticamente nombre, fecha y motivo
                    </p>
                </div>
            </div>

            <!-- Manual Tab -->
            <div id="manual-tab" class="tab-content hidden">
                <form id="manual-form" onsubmit="submitManualForm(event)">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="manual_patient_name" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-user mr-1 text-blue-600"></i> Nombre del Paciente
                            </label>
                            <input type="text" name="patient_name" id="manual_patient_name" required
                                class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="manual_patient_phone" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-phone mr-1 text-blue-600"></i> Teléfono
                            </label>
                            <input type="tel" name="patient_phone" id="manual_patient_phone" placeholder="Ej: 5551234567"
                                class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="manual_doctor_id" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-user-doctor mr-1 text-blue-600"></i> Doctor Asignado
                            </label>
                            <select id="manual_doctor_id" name="doctor_id" required
                                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                                <!-- Populated by JS -->
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="manual_date_time" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-calendar-days mr-1 text-blue-600"></i> Fecha y Hora
                            </label>
                            <input type="datetime-local" name="date_time" id="manual_date_time" required
                                class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                        </div>

                        <div class="sm:col-span-2">
                            <label for="manual_appointment_type" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-briefcase-medical mr-1 text-blue-600"></i> Tipo de Cita
                            </label>
                            <select id="manual_appointment_type" name="appointment_type" required
                                class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                                <option value="consulta">Consulta Médica</option>
                                <option value="laboratorio">Verificación de Laboratorio</option>
                                <option value="chequeo">Chequeo Común</option>
                            </select>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="manual_reason" class="block text-sm font-bold text-gray-700 mb-2">
                                <i class="fa-solid fa-notes-medical mr-1 text-blue-600"></i> Motivo de la Consulta
                            </label>
                            <input type="text" name="reason" id="manual_reason" required
                                class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                        </div>
                    </div>

                    <div class="mt-8 flex gap-4">
                        <button type="button" onclick="location.reload()"
                            class="flex-1 py-3 px-4 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition">
                            <i class="fa-solid fa-rotate-right mr-2"></i>Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 py-3 px-4 bg-green-600 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 hover:bg-green-700 transition transform hover:-translate-y-1">
                            <i class="fa-solid fa-check mr-2"></i>Agendar Cita
                        </button>
                    </div>
                </form>
            </div>

            <div id="result-area" class="hidden space-y-6 animate-fade-in">
                <div class="border-t-2 border-gray-100 pt-6">
                    <div class="flex items-center gap-2 mb-6">
                        <i class="fa-solid fa-check-circle text-green-500 text-xl"></i>
                        <h3 class="text-lg font-bold text-gray-900">Datos Extraídos por IA</h3>
                    </div>
                    <form id="confirm-form" onsubmit="confirmAppointment(event)">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label for="patient_name" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-user mr-1 text-blue-600"></i> Nombre del Paciente
                                </label>
                                <input type="text" name="patient_name" id="patient_name" required
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="patient_phone" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-phone mr-1 text-blue-600"></i> Teléfono
                                </label>
                                <input type="tel" name="patient_phone" id="patient_phone" placeholder="Ej: 5551234567"
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="doctor_id" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-user-doctor mr-1 text-blue-600"></i> Doctor Asignado
                                </label>
                                <select id="doctor_id" name="doctor_id" required
                                    class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                                    <!-- Populated by JS -->
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="date_time" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-calendar-days mr-1 text-blue-600"></i> Fecha y Hora
                                </label>
                                <input type="datetime-local" name="date_time" id="date_time" required
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                            </div>

                            <div class="sm:col-span-2">
                                <label for="appointment_type" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-briefcase-medical mr-1 text-blue-600"></i> Tipo de Cita
                                </label>
                                <select id="appointment_type" name="appointment_type" required
                                    class="block w-full rounded-xl border-gray-200 bg-white shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                                    <option value="consulta">Consulta Médica</option>
                                    <option value="laboratorio">Verificación de Laboratorio</option>
                                    <option value="chequeo">Chequeo Común</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label for="reason" class="block text-sm font-bold text-gray-700 mb-2">
                                    <i class="fa-solid fa-notes-medical mr-1 text-blue-600"></i> Motivo de la Consulta
                                </label>
                                <input type="text" name="reason" id="reason" required
                                    class="block w-full rounded-xl border-gray-200 shadow-sm focus:ring-blue-500 focus:border-blue-500 py-3 px-4">
                            </div>
                        </div>

                        <div class="mt-8 flex gap-4">
                            <button type="button" onclick="location.reload()"
                                class="flex-1 py-3 px-4 border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition">
                                <i class="fa-solid fa-rotate-right mr-2"></i>Cancelar
                            </button>
                            <button type="submit"
                                class="flex-1 py-3 px-4 bg-green-600 text-white rounded-xl font-bold shadow-lg shadow-green-500/30 hover:bg-green-700 transition transform hover:-translate-y-1">
                                <i class="fa-solid fa-check mr-2"></i>Confirmar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-6 flex gap-4">
        <a href="calendar.php" class="flex-1 glass-panel p-4 rounded-2xl hover:shadow-lg transition flex items-center gap-3 justify-center">
            <i class="fa-solid fa-calendar-days text-blue-600 text-2xl"></i>
            <div>
                <div class="font-bold text-gray-900">Ver Calendario</div>
                <div class="text-xs text-gray-500">Revisa citas agendadas</div>
            </div>
        </a>
        <a href="consultation.php" class="flex-1 glass-panel p-4 rounded-2xl hover:shadow-lg transition flex items-center gap-3 justify-center">
            <i class="fa-solid fa-stethoscope text-violet-600 text-2xl"></i>
            <div>
                <div class="font-bold text-gray-900">Ir a Consulta</div>
                <div class="text-xs text-gray-500">Atender pacientes</div>
            </div>
        </a>
    </div>
</div>

<script>
    // Tab switching
    function switchTab(tab) {
        const tabs = ['whatsapp', 'manual'];
        tabs.forEach(t => {
            const tabEl = document.getElementById(`tab-${t}`);
            const contentEl = document.getElementById(`${t}-tab`);
            
            if (t === tab) {
                tabEl.classList.add('text-blue-600', 'border-blue-600');
                tabEl.classList.remove('text-gray-500', 'border-transparent');
                contentEl.classList.remove('hidden');
            } else {
                tabEl.classList.remove('text-blue-600', 'border-blue-600');
                tabEl.classList.add('text-gray-500', 'border-transparent');
                contentEl.classList.add('hidden');
            }
        });
    }

    // Load doctors for manual form
    async function loadDoctors() {
        const manualSelect = document.getElementById('manual_doctor_id');
        
        // Show loading state
        manualSelect.innerHTML = '<option>Cargando doctores...</option>';
        
        try {
            const response = await fetch('api/get_doctors_list.php');
            const doctors = await response.json();
            
            // Clear loading and populate
            manualSelect.innerHTML = '';
            
            if (doctors.length === 0) {
                manualSelect.innerHTML = '<option>No hay doctores disponibles</option>';
                return;
            }
            
            doctors.forEach(doc => {
                const option = document.createElement('option');
                option.value = doc.id;
                option.textContent = `${doc.name} (${doc.specialty})`;
                manualSelect.appendChild(option);
            });
        } catch (e) {
            console.error('Error loading doctors:', e);
            manualSelect.innerHTML = '<option>Error al cargar doctores</option>';
        }
    }

    // Manual form submission
    async function submitManualForm(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('¡Cita agendada con éxito!');
                window.location.reload();
            } else {
                alert('Error: ' + result.error);
            }
        } catch (e) {
            alert('Error de conexión: ' + e.message);
        }
    }

    // Load doctors on page load
    loadDoctors();
    async function processMagicInput() {
        const text = document.getElementById('magic-input').value;
        const btn = document.getElementById('process-btn');
        const btnText = document.getElementById('btn-text');

        if (!text) return;

        // Loading state
        btn.disabled = true;
        btnText.textContent = 'Analizando...';

        try {
            const response = await fetch('api/parse_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text: text })
            });

            const data = await response.json();

            if (data.error) {
                alert(data.error);
                btn.disabled = false;
                btnText.textContent = 'Procesar con IA';
                return;
            }

            // Auto-book if flag is set (single doctor clinic)
            if (data.auto_booked) {
                btnText.textContent = 'Agendando...';
                
                const bookResponse = await fetch('api/book_appointment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        patient_name: data.patient_name,
                        doctor_id: data.suggested_doctor_id,
                        date_time: data.date_time,
                        reason: data.reason,
                        appointment_type: data.appointment_type || 'consulta'
                    })
                });

                const bookResult = await bookResponse.json();

                if (bookResult.success) {
                    alert('✅ ¡Cita agendada automáticamente!\n\n' +
                          'Paciente: ' + data.patient_name + '\n' +
                          'Fecha: ' + data.date_time + '\n' +
                          'Motivo: ' + data.reason);
                    window.location.reload();
                } else {
                    alert('Error al agendar: ' + bookResult.error);
                }
                return;
            }

            // Fallback: Show form for confirmation (multiple doctors)
            document.getElementById('patient_name').value = data.patient_name || '';
            document.getElementById('reason').value = data.reason || '';
            document.getElementById('appointment_type').value = data.appointment_type || 'consulta';

            // Handle date (convert to local datetime-local format)
            if (data.date_time) {
                const date = new Date(data.date_time);
                const localIso = new Date(date.getTime() - (date.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                document.getElementById('date_time').value = localIso;
            }

            // Populate doctors
            const doctorSelect = document.getElementById('doctor_id');
            doctorSelect.innerHTML = '';
            data.doctors.forEach(doc => {
                const option = document.createElement('option');
                option.value = doc.id;
                option.textContent = doc.name + ' (' + doc.specialty + ')';
                if (data.suggested_doctor_id == doc.id) option.selected = true;
                doctorSelect.appendChild(option);
            });

            document.getElementById('result-area').classList.remove('hidden');

        } catch (e) {
            console.error(e);
            alert('Error al procesar la solicitud: ' + e.message);
        } finally {
            btn.disabled = false;
            btnText.textContent = 'Procesar con IA';
        }
    }

    async function confirmAppointment(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/book_appointment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('¡Cita agendada con éxito!');
                window.location.reload();
            } else {
                alert('Error: ' + result.error);
            }
        } catch (e) {
            alert('Error de conexión');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
