<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistemas SATI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class', // Habilita o modo escuro baseado em classes
        };
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img src="{{ asset('images/LogoSATI.png') }}" alt="Logo SATI" class="h-10 w-auto">
                </div>
                <div>
                    <button onclick="toggleDarkMode()"
                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        aria-label="Toggle dark mode">
                        <!-- Ícone de sol (modo claro) -->
                        <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Ícone de lua (modo escuro) -->
                        <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                    Sistemas SATI
                </h1>
                <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 dark:text-gray-300 sm:mt-4">
                    Selecione o sistema que deseja acessar
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2">
                <!-- SatiAPP Card -->
                <a href="http://192.168.3.142:8990" class="group">
                    <div
                        class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                        <div class="flex-shrink-0 bg-blue-500 p-6 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    SatiAPP
                                </h3>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-300">
                                    Sistema de gerenciamento de aplicações SATI
                                </p>
                            </div>
                            <div class="mt-6">
                                <div
                                    class="flex items-center text-blue-600 dark:text-blue-400 group-hover:text-blue-800 dark:group-hover:text-blue-300">
                                    <span>Acessar sistema</span>
                                    <svg class="ml-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- Relogio Ponto Card -->
                <a href="/login" class="group">
                    <div
                        class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-all duration-200 transform hover:scale-105 hover:shadow-xl">
                        <div class="flex-shrink-0 bg-green-500 p-6 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-white" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                    Relógio Ponto
                                </h3>
                                <p class="mt-3 text-base text-gray-500 dark:text-gray-300">
                                    Sistema de registro e controle de ponto
                                </p>
                            </div>
                            <div class="mt-6">
                                <div
                                    class="flex items-center text-green-600 dark:text-green-400 group-hover:text-green-800 dark:group-hover:text-green-300">
                                    <span>Acessar sistema</span>
                                    <svg class="ml-2 w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>&copy; 2025 SATI. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Função para alternar entre modo escuro e claro
        function toggleDarkMode() {
            const htmlElement = document.documentElement;
            const isDarkMode = htmlElement.classList.toggle('dark');
            localStorage.theme = isDarkMode ? 'dark' : 'light';

            // Atualiza os ícones
            updateIcons(isDarkMode);
        }

        // Função para atualizar os ícones de sol e lua
        function updateIcons(isDarkMode) {
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            if (isDarkMode) {
                sunIcon.classList.remove('hidden');
                moonIcon.classList.add('hidden');
            } else {
                sunIcon.classList.add('hidden');
                moonIcon.classList.remove('hidden');
            }
        }

        // Verifica o tema salvo no localStorage ou a preferência do sistema
        function applySavedTheme() {
            const savedTheme = localStorage.theme;
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark');
                updateIcons(true);
            } else {
                document.documentElement.classList.remove('dark');
                updateIcons(false);
            }
        }

        // Aplica o tema ao carregar a página
        applySavedTheme();
    </script>
</body>

</html>
