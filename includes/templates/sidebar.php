<?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">DTR System</h2>
    </div>

    <nav class="p-4 space-y-2 text-sm">
        <!-- Dashboard -->
        <a
            href="<?= e(BASE_URL); ?>"
            class="flex items-center px-4 py-2 rounded gap-2
            <?= $currentPage === 'index.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?>">
            <!-- Home Icon -->
            <svg class="w-5 h-5 <?= $currentPage === 'index.php' ? 'text-white' : 'text-gray-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6" />
            </svg>
            Dashboard
        </a>

        <!-- Daily Time Record -->
        <a href="<?= e(BASE_URL . '/daily-time-record.php'); ?>"
            class="flex items-center px-4 py-2 rounded gap-2
            <?= $currentPage === 'daily-time-record.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?>">
            <!-- Clock Icon -->
            <svg class="w-5 h-5 <?= $currentPage === 'daily-time-record.php' ? 'text-white' : 'text-gray-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Daily Time Record
        </a>

        <!-- Schedule -->
        <a
            href="<?= e(BASE_URL . '/schedule.php'); ?>"
            class="flex items-center px-4 py-2 rounded gap-2
            <?= $currentPage === 'schedule.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?>">
            <!-- Calendar Icon -->
            <svg class="w-5 h-5 <?= $currentPage === 'schedule.php' ? 'text-white' : 'text-gray-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Schedule
        </a>

        <!-- Profile -->
        <a href="<?= e(BASE_URL . '/profile.php'); ?>"
            class="flex items-center px-4 py-2 rounded gap-2
            <?= $currentPage === 'profile.php' ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' ?>">
            <!-- User Icon -->
            <svg class="w-5 h-5 <?= $currentPage === 'profile.php' ? 'text-white' : 'text-gray-600' ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5.121 17.804A7 7 0 0112 15a7 7 0 016.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Profile
        </a>
    </nav>
</aside>