</main> <!-- End Main Content Area -->

<!-- Footer Selalu Menempel di Bagian Bawah -->
<footer class="px-4 sm:px-6 lg:px-8 py-4 bg-slate-100 border-t border-slate-200/80 text-xs text-slate-500 flex flex-col sm:flex-row gap-2 justify-between items-start sm:items-center shrink-0 mt-auto">
    <p>© 2026 <strong>SMK Kesehatan Surya Medika</strong>. All rights reserved.</p>
    <p class="text-slate-400">Sistem Informasi Laboratorium Keperawatan v1.0 (CodeIgniter 3)</p>
</footer>

</div> <!-- End Area Kanan -->
</div> <!-- End Wrapper Utama -->

<script>
    const sidebar = document.getElementById('main-sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarToggle.setAttribute('aria-expanded', 'false');
        sidebarOverlay.classList.add('hidden');
    }

    function toggleSidebar() {
        const isOpen = !sidebar.classList.contains('-translate-x-full');
        sidebar.classList.toggle('-translate-x-full', isOpen);
        sidebarToggle.setAttribute('aria-expanded', String(!isOpen));
        sidebarOverlay.classList.toggle('hidden', isOpen);
    }

    sidebarToggle.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', closeSidebar);
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeSidebar();
        }
    });
</script>

</body>

</html>