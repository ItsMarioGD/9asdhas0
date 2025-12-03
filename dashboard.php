<?php include 'includes/header.php'; ?>

<div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 flex items-center gap-3">
        <span class="text-4xl">📊</span> Dashboard de Rendimiento
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Pacientes Atendidos por Mes</h3>
            <canvas id="patientsChart"></canvas>
        </div>


        <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Diagnósticos Comunes</h3>
            <canvas id="diseasesChart"></canvas>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
        <h3 class="text-lg font-bold text-gray-800 mb-4 text-red-600">Pacientes "Perdidos" (Sin visita en 6 meses)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última Visita</th>
                    </tr>
                </thead>
                <tbody id="lost-patients-table" class="bg-white divide-y divide-gray-200">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    fetch('api/dashboard_data.php')
        .then(r => r.json())
        .then(data => {
            // Chart 1
            new Chart(document.getElementById('patientsChart'), {
                type: 'line',
                data: {
                    labels: data.monthly_stats.map(d => d.month),
                    datasets: [{
                        label: 'Pacientes',
                        data: data.monthly_stats.map(d => d.count),
                        borderColor: 'rgb(59, 130, 246)',
                        tension: 0.1
                    }]
                }
            });

            // Chart 2
            new Chart(document.getElementById('diseasesChart'), {
                type: 'doughnut',
                data: {
                    labels: data.top_diseases.map(d => d.diagnosis),
                    datasets: [{
                        data: data.top_diseases.map(d => d.count),
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)',
                            'rgb(255, 205, 86)',
                            'rgb(75, 192, 192)',
                            'rgb(153, 102, 255)'
                        ]
                    }]
                }
            });

            const table = document.getElementById('lost-patients-table');
            data.lost_patients.forEach(p => {
                const row = `
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${p.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.phone}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.last_visit || 'Nunca'}</td>
                </tr>
            `;
                table.innerHTML += row;
            });
        });
</script>

<?php include 'includes/footer.php'; ?>