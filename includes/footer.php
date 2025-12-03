    </main>

    <footer class="bg-white mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">© 2025 Medi-Agenda AI. Powered by Gemini.</p>
        </div>
    </footer>
    
    <script>
    // ========================================
    // DARK MODE
    // ========================================
    function toggleDarkMode() {
        const html = document.documentElement;
        const icon = document.getElementById('theme-icon');
        
        if (html.classList.contains('dark')) {
            html.classList.remove('dark');
            icon.classList.remove('fa-sun');
            icon.classList.add('fa-moon');
            localStorage.setItem('theme', 'light');
        } else {
            html.classList.add('dark');
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
            localStorage.setItem('theme', 'dark');
        }
    }
    
    // Load theme on page load
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.classList.add('dark');
        const icon = document.getElementById('theme-icon');
        if (icon) {
            icon.classList.remove('fa-moon');
            icon.classList.add('fa-sun');
        }
    }
    
    // ========================================
    // NOTIFICATIONS
    // ========================================
    let notifications = [];
    
    async function loadNotifications() {
        try {
            const res = await fetch('api/get_notifications.php');
            const data = await res.json();
            notifications = data;
            updateNotificationBadge();
        } catch (e) {
            console.error('Error loading notifications:', e);
        }
    }
    
    function updateNotificationBadge() {
        const badge = document.getElementById('notif-badge');
        const unread = notifications.filter(n => !n.read).length;
        
        if (unread > 0) {
            badge.textContent = unread;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    
    function toggleNotifications() {
        const dropdown = document.getElementById('notif-dropdown');
        const list = document.getElementById('notif-list');
        
        if (dropdown.classList.contains('hidden')) {
            // Show
            dropdown.classList.remove('hidden');
            
            if (notifications.length === 0) {
                list.innerHTML = '<p class="p-4 text-gray-500 text-center">No hay notificaciones</p>';
            } else {
                list.innerHTML = notifications.map(n => `
                    <div class="p-4 hover:bg-gray-50 border-b cursor-pointer ${n.read ? 'opacity-60' : ''}">
                        <div class="flex items-start gap-3">
                            <i class="${n.icon} text-blue-600 text-xl"></i>
                            <div class="flex-1">
                                <p class="font-semibold text-sm">${n.title}</p>
                                <p class="text-xs text-gray-600">${n.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${n.time}</p>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        } else {
            // Hide
            dropdown.classList.add('hidden');
        }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('notif-dropdown');
        const btn = e.target.closest('button[onclick="toggleNotifications()"]');
        if (!btn && !dropdown?.contains(e.target)) {
            dropdown?.classList.add('hidden');
        }
    });
    
    // Load notifications every 30 seconds
    loadNotifications();
    setInterval(loadNotifications, 30000);
    
    // ========================================
    // GLOBAL SEARCH (Ctrl+K)
    // ========================================
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            openGlobalSearch();
        }
    });
    
    function openGlobalSearch() {
        // Create modal if doesn't exist
        if (!document.getElementById('global-search-modal')) {
            const modal = document.createElement('div');
            modal.id = 'global-search-modal';
            modal.className = 'fixed inset-0 bg-black/50 flex items-start justify-center pt-20 z-[100] hidden backdrop-blur-sm';
            modal.innerHTML = `
                <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl animate-fade-in">
                    <div class="p-4 border-b flex items-center gap-3">
                        <i class="fa-solid fa-search text-gray-400"></i>
                        <input 
                            type="text" 
                            id="global-search-input"
                            placeholder="Buscar pacientes, citas, consultas... (Ctrl+K)" 
                            class="flex-1 outline-none text-lg"
                            oninput="performGlobalSearch(this.value)"
                        >
                        <button onclick="closeGlobalSearch()" class="text-gray-400 hover:text-gray-600">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                    <div id="global-search-results" class="max-h-96 overflow-y-auto p-4">
                        <p class="text-gray-500 text-center py-8">Escribe para buscar...</p>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeGlobalSearch();
            });
        }
        
        const modal = document.getElementById('global-search-modal');
        modal.classList.remove('hidden');
        document.getElementById('global-search-input').focus();
    }
    
    function closeGlobalSearch() {
        document.getElementById('global-search-modal').classList.add('hidden');
    }
    
    let searchTimeout;
    async function performGlobalSearch(query) {
        clearTimeout(searchTimeout);
        
        if (!query) {
            document.getElementById('global-search-results').innerHTML = '<p class="text-gray-500 text-center py-8">Escribe para buscar...</p>';
            return;
        }
        
        document.getElementById('global-search-results').innerHTML = '<p class="text-gray-500 text-center py-8"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i>Buscando...</p>';
        
        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`api/global_search.php?q=${encodeURIComponent(query)}`);
                const results = await res.json();
                
                if (results.length === 0) {
                    document.getElementById('global-search-results').innerHTML = '<p class="text-gray-500 text-center py-8">No se encontraron resultados</p>';
                } else {
                    document.getElementById('global-search-results').innerHTML = results.map(r => `
                        <a href="${r.url}" class="block p-3 hover:bg-gray-50 rounded-lg border-b">
                            <div class="flex items-center gap-3">
                                <i class="${r.icon} text-blue-600"></i>
                                <div class="flex-1">
                                    <p class="font-semibold">${r.title}</p>
                                    <p class="text-sm text-gray-600">${r.subtitle}</p>
                                </div>
                                <span class="text-xs text-gray-400">${r.type}</span>
                            </div>
                        </a>
                    `).join('');
                }
            } catch (e) {
                document.getElementById('global-search-results').innerHTML = '<p class="text-red-500 text-center py-8">Error en la búsqueda</p>';
            }
        }, 300);
    }
    </script>
</body>

</html>
