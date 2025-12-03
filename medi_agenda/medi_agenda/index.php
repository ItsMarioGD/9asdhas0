<?php include 'includes/header.php'; ?>

<div class="relative overflow-hidden rounded-3xl bg-white shadow-2xl mb-16">
    <div class="absolute inset-0">
        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80"
            alt="Medical Background"
            class="h-full w-full object-cover opacity-10">
        <div class="absolute inset-0 bg-gradient-to-r from-white via-white/80 to-transparent"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 md:py-32">
        <div class="md:w-2/3">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 border border-blue-100 text-blue-600 font-semibold text-sm mb-6">
                <i class="fa-solid fa-star"></i> Tecnología Médica de Vanguardia
            </div>
            <h1 class="text-5xl md:text-7xl font-bold text-gray-900 mb-6 tracking-tight">
                El Futuro de la <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-violet-600">Medicina es Ahora</span>
            </h1>
            <p class="text-xl text-gray-600 mb-10 max-w-2xl leading-relaxed">
                Transforma tu clínica con Inteligencia Artificial. Desde agendamiento automático hasta diagnósticos asistidos por voz.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="reception.php" class="px-8 py-4 bg-blue-600 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:bg-blue-700 transition transform hover:-translate-y-1 flex items-center gap-3">
                    <i class="fa-brands fa-whatsapp text-xl"></i>
                    Probar Recepción IA
                </a>
                <a href="consultation.php" class="px-8 py-4 bg-white text-gray-700 border border-gray-200 rounded-xl font-bold shadow-sm hover:bg-gray-50 transition transform hover:-translate-y-1 flex items-center gap-3">
                    <i class="fa-solid fa-microphone text-blue-500"></i>
                    Demo Consulta
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Features Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
    <!-- Card 1 -->
    <div class="glass-panel p-8 rounded-3xl hover:shadow-xl transition duration-300 group">
        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
            <i class="fa-solid fa-calendar-check text-2xl text-blue-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Recepción 24/7</h3>
        <p class="text-gray-500 leading-relaxed">
            Olvídate de las llamadas perdidas. Nuestra IA procesa mensajes de texto natural y gestiona tu agenda sin errores de duplicidad.
        </p>
    </div>

    <!-- Card 2 -->
    <div class="glass-panel p-8 rounded-3xl hover:shadow-xl transition duration-300 group">
        <div class="w-14 h-14 bg-violet-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
            <i class="fa-solid fa-brain text-2xl text-violet-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Asistente Clínico</h3>
        <p class="text-gray-500 leading-relaxed">
            Dicta tus consultas y deja que la IA estructure los síntomas, diagnósticos y recetas automáticamente.
        </p>
    </div>

    <!-- Card 3 -->
    <div class="glass-panel p-8 rounded-3xl hover:shadow-xl transition duration-300 group">
        <div class="w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
            <i class="fa-solid fa-chart-line text-2xl text-emerald-600"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-3">Analytics Pro</h3>
        <p class="text-gray-500 leading-relaxed">
            Toma decisiones basadas en datos reales. Visualiza el crecimiento de tu clínica y recupera pacientes inactivos.
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>