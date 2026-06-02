<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 bg-gray-900 border-b border-gray-800">
            <ApplicationLogo class="block h-10 w-auto" />

            <h1 class="mt-8 text-2xl font-medium text-white">
                Welcome to FoxSlash!
            </h1>

            <p class="mt-4 text-gray-400 leading-relaxed">
                You're logged in. Use the navigation above to manage your profile and account settings.
            </p>
        </div>

        <div class="bg-gray-900 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-orange-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-white">
                        <a :href="route('profile.show')" class="hover:text-orange-400 transition">Profile</a>
                    </h2>
                </div>

                <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                    Update your name, email address, profile photo, and manage your connected accounts and security settings.
                </p>

                <p class="mt-4 text-sm">
                    <a :href="route('profile.show')" class="inline-flex items-center font-semibold text-orange-400 hover:text-orange-300 transition">
                        Manage profile

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-orange-400">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>

            <div>
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-orange-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <h2 class="ms-3 text-xl font-semibold text-white">
                        Security
                    </h2>
                </div>

                <p class="mt-4 text-gray-400 text-sm leading-relaxed">
                    Keep your account safe with two-factor authentication, manage active browser sessions, and update your password.
                </p>

                <p class="mt-4 text-sm">
                    <a :href="route('profile.show')" class="inline-flex items-center font-semibold text-orange-400 hover:text-orange-300 transition">
                        Security settings

                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="ms-1 size-5 fill-orange-400">
                            <path fill-rule="evenodd" d="M5 10a.75.75 0 01.75-.75h6.638L10.23 7.29a.75.75 0 111.04-1.08l3.5 3.25a.75.75 0 010 1.08l-3.5 3.25a.75.75 0 11-1.04-1.08l2.158-1.96H5.75A.75.75 0 015 10z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </p>
            </div>
        </div>
    </div>
</template>
