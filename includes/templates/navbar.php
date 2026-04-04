<!-- Top Navbar -->
<header class="bg-white shadow rounded-lg mb-6">
    <div class="flex items-center justify-between px-6 py-3">

        <!-- Left -->
        <div class="flex items-center gap-4">

            <!-- Sidebar Toggle -->
            <button class="p-2 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>

        </div>

        <!-- Right -->
        <div class="flex items-center gap-4">

            <!-- Notifications -->
            <button class="text-gray-600 hover:text-gray-800 cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    class="w-6 h-6">
                    <path stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11
                        a6.002 6.002 0 00-4-5.659V5
                        a2 2 0 10-4 0v.341
                        C7.67 6.165 6 8.388 6 11v3.159
                        c0 .538-.214 1.055-.595 1.436L4 17h5
                        m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- User Dropdown -->
            <div class="relative">
                <?php
                $first = $intern['first_name'] ?? '';
                $last  = $intern['last_name'] ?? '';

                $initials = strtoupper(
                    substr($first, 0, 1) .
                        substr($last, 0, 1)
                );
                ?>
                <!-- Trigger -->
                <button id="userMenuBtn"
                    class="flex items-center focus:outline-none transition duration-200 hover:opacity-80 cursor-pointer">

                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-500 text-white text-sm font-semibold">
                        <?= e($initials); ?>
                    </div>
                </button>

                <!-- Dropdown -->
                <div id="userDropdown"
                    class="hidden absolute right-0 mt-3 w-56 bg-white border border-gray-200 rounded-lg shadow-lg py-2">

                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-200">
                        <div class="text-sm font-semibold text-gray-800">
                            <?= e($intern['first_name'] ?? ''); ?>
                            <?= e($intern['middle_name'] ?? ''); ?>
                            <?= e($intern['last_name'] ?? ''); ?>
                        </div>

                        <div class="text-xs text-gray-500 truncate">
                            <?= e($intern['email'] ?? ''); ?>
                        </div>
                    </div>

                    <!-- Profile -->
                    <a href="<?= e(BASE_URL . '/profile.php') ?>"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">

                        <!-- Profile Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 text-gray-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5.121 17.804A9 9 0 1118.88 17.804 M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>

                        Profile
                    </a>

                    <!-- Logout -->
                    <a href="<?= e(BASE_URL . '/login.php') ?>"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">

                        <!-- Logout Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor">

                            <path stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7 m6 4v1a2 2 0 01-2 2H6 a2 2 0 01-2-2V7 a2 2 0 012-2h5 a2 2 0 012 2v1" />
                        </svg>

                        Logout
                    </a>

                </div>
            </div>

        </div>
    </div>
</header>