<?php include 'includes/header.php'; ?>

<div class="max-w-6xl mx-auto">
    <div class="glass-panel rounded-3xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-violet-600 to-purple-600 p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                    <i class="fa-solid fa-stethoscope text-3xl"></i>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">Consulta Médica</h2>
                    <p class="text-violet-100">Asistente de voz inteligente</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <div class="mb-8">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Seleccionar Paciente</label>
                    <a href="calendar.php" class="text-sm text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-calendar-days"></i>
                        Ver calendario
                    </a>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-user-injured text-gray-400"></i>
                    </div>
                    <select id="appointment_id" class="pl-10 block w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-violet-500 focus:border-violet-500 transition-colors py-3">
                        <option value="">Cargando citas...</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fa-solid fa-info-circle"></i> 
                    Solo aparecen citas pendientes. Si no ves ninguna cita, <a href="reception.php" class="text-blue-600 underline">crea una nueva</a>.
                </p>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Input Section -->
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wide">Dictado del Doctor</label>
                        <button id="mic-btn" onclick="toggleRecording()" class="group flex items-center gap-2 px-5 py-2 bg-red-50 text-red-600 rounded-full hover:bg-red-100 transition-all border border-red-100">
                            <div id="mic-icon-container" class="relative flex items-center justify-center w-6 h-6">
                                <i class="fa-solid fa-microphone"></i>
                                <span id="pulse-ring" class="absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-0 transition-opacity"></span>
                            </div>
                            <span id="mic-status" class="font-semibold">Iniciar Grabación</span>
                        </button>
                    </div>
                    
                    <div class="relative">
                        <textarea id="dictation-area" rows="12" 
                            class="block w-full rounded-2xl border-gray-200 shadow-inner focus:border-violet-500 focus:ring-violet-500 text-gray-600 p-6 bg-gray-50 resize-none leading-relaxed" 
                            placeholder="Presione el micrófono y empiece a hablar..."></textarea>
                        <div class="absolute bottom-4 right-4 text-gray-400 text-xs">
                            <i class="fa-brands fa-markdown mr-1"></i>Soporta dictado continuo
                        </div>
                    </div>

                    <button onclick="processConsultation()" class="w-full py-4 bg-violet-600 text-white rounded-xl font-bold shadow-lg shadow-violet-500/30 hover:bg-violet-700 transition transform hover:-translate-y-1 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                        Procesar y Guardar
                    </button>
                </div>

                <!-- Output Section -->
                <div class="space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-medical text-violet-500"></i>
                        Información Clasificada
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Symptoms -->
                        <div class="bg-amber-50 p-6 rounded-2xl border border-amber-100 transition hover:shadow-md">
                            <h4 class="font-bold text-amber-800 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-triangle-exclamation"></i> Síntomas
                            </h4>
                            <div id="symptoms-output" class="text-gray-700 min-h-[40px] italic">Esperando datos...</div>
                        </div>

                        <!-- Diagnosis -->
                        <div class="bg-red-50 p-6 rounded-2xl border border-red-100 transition hover:shadow-md">
                            <h4 class="font-bold text-red-800 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-heart-crack"></i> Diagnóstico
                            </h4>
                            <div id="diagnosis-output" class="text-gray-700 min-h-[40px] italic">Esperando datos...</div>
                        </div>

                        <!-- Medication -->
                        <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100 transition hover:shadow-md">
                            <h4 class="font-bold text-emerald-800 mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-pills"></i> Medicamentos
                            </h4>
                            <div id="medication-output" class="text-gray-700 min-h-[40px] italic">Esperando datos...</div>
                        </div>
                    </div>
                    
                    <div id="prescription-actions" class="hidden pt-4 animate-fade-in">
                        <a id="download-pdf-btn" href="#" target="_blank" class="block w-full py-4 bg-gray-900 text-white text-center rounded-xl font-bold shadow-xl hover:bg-black transition transform hover:scale-[1.02] flex justify-center items-center gap-3">
                            <i class="fa-solid fa-file-pdf text-red-400"></i>
                            Descargar Receta PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse-ring {
        0% { transform: scale(0.8); opacity: 0.5; }
        100% { transform: scale(2); opacity: 0; }
    }
    .animate-pulse-ring {
        animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }
</style>

<script>
    // Load appointments on start
    fetch('api/get_appointments.php')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('appointment_id');
            select.innerHTML = ''; // Clear loading message
            
            if (data.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = 'No hay citas pendientes - Crea una en Recepción';
                select.appendChild(opt);
            } else {
                // Type icons
                const typeIcons = {
                    'consulta': '🩺',
                    'laboratorio': '🔬',
                    'chequeo': '✅'
                };
                
                data.forEach(app => {
                    const opt = document.createElement('option');
                    opt.value = app.id;
                    const icon = typeIcons[app.appointment_type] || '📋';
                    opt.textContent = `${icon} ${app.date_time} - ${app.patient_name} (${app.type_label})`;
                    select.appendChild(opt);
                });
            }
        });

    let recognition;
    let isRecording = false;

    if ('webkitSpeechRecognition' in window) {
        recognition = new webkitSpeechRecognition();
        recognition.continuous = true;
        recognition.interimResults = true;
        recognition.lang = 'es-ES';

        recognition.onresult = function (event) {
            let finalTranscript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                }
            }
            if (finalTranscript) {
                const textarea = document.getElementById('dictation-area');
                textarea.value += finalTranscript + ' ';
            }
        };
    } else {
        alert('Web Speech API no soportada en este navegador. Use Chrome.');
    }

    function toggleRecording() {
        if (!recognition) return;

        const btn = document.getElementById('mic-btn');
        const status = document.getElementById('mic-status');
        const pulseRing = document.getElementById('pulse-ring');

        if (isRecording) {
            recognition.stop();
            btn.classList.replace('bg-red-600', 'bg-red-50');
            btn.classList.replace('text-white', 'text-red-600');
            status.textContent = 'Iniciar Grabación';
            pulseRing.classList.remove('animate-pulse-ring');
            pulseRing.classList.add('opacity-0');
        } else {
            recognition.start();
            btn.classList.replace('bg-red-50', 'bg-red-600');
            btn.classList.replace('text-red-600', 'text-white');
            status.textContent = 'Detener Grabación';
            pulseRing.classList.remove('opacity-0');
            pulseRing.classList.add('animate-pulse-ring');
        }
        isRecording = !isRecording;
    }

    async function processConsultation() {
        const text = document.getElementById('dictation-area').value;
        const appointmentId = document.getElementById('appointment_id').value;

        if (!text || !appointmentId) {
            alert('Por favor seleccione una cita y dicte la consulta.');
            return;
        }

        try {
            const response = await fetch('api/process_consultation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text, appointment_id: appointmentId })
            });

            const data = await response.json();

            if (data.error) {
                alert(data.error);
                return;
            }

            document.getElementById('symptoms-output').textContent = data.symptoms;
            document.getElementById('diagnosis-output').textContent = data.diagnosis;
            document.getElementById('medication-output').textContent = data.medication;

            const pdfBtn = document.getElementById('download-pdf-btn');
            pdfBtn.href = `api/generate_prescription.php?id=${data.consultation_id}`;
            document.getElementById('prescription-actions').classList.remove('hidden');

        } catch (e) {
            alert('Error al procesar');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
