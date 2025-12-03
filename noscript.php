<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwind CDN (works even without JS) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

    <title>JavaScript Disabled</title>
      <!-- Add your favicon -->
  <link rel="icon" type="image/png" href="pictures/logo.png" />

</head>

<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center min-h-screen">

    <!-- Glass morph container -->
    <div
        class="p-8 rounded-xl bg-white/30 dark:bg-black/30 backdrop-blur-md border border-white/20 dark:border-black/20 shadow-xl flex flex-col items-center text-center max-w-md mx-4">

     

        <!-- Title -->
        <h1 class="text-2xl font-semibold text-red-700 dark:text-red-400 mb-3">
            JavaScript is Disabled
        </h1>

        <!-- Message -->
        <p class="text-gray-800 dark:text-gray-300 text-lg leading-relaxed">
            This website needs JavaScript to function correctly.<br />
            Please enable JavaScript in your browser settings.
        </p>

        <!-- Button -->
        <div class="mt-6">
            <a href="index.php"
                class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition shadow-md">
                Go Back
            </a>
        </div>
    </div>

</body>
</html>
