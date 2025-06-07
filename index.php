

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Charles Pura</title>

  <!-- Add your favicon -->
  <link rel="icon" type="image/png" href="pictures/logo.png" />

  <!-- Add Font Awesome CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Add your CSS file -->
  <link rel="stylesheet" href="style.css">


  <script>
    // Load theme before Tailwind runs
    if (localStorage.theme === 'dark') {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class', // Enable class-based dark mode
    };
  </script>
  <style>
html, body {
  width: 100%;
  overflow-x: hidden;
}

 
  @layer utilities {
    @keyframes blob {
      0%, 100% {
        transform: translate(0px, 0px) scale(1);
        border-radius: 50% 50% 50% 50%;
      }
      33% {
        transform: translate(10px, -20px) scale(1.05);
        border-radius: 60% 40% 30% 70%;
      }
      66% {
        transform: translate(-20px, 10px) scale(0.95);
        border-radius: 40% 60% 70% 30%;
      }
    }

    .animate-blob {
      animation: blob 8s infinite ease-in-out;
    }
  }


    html {
      scroll-behavior: smooth;
    }
    .theme-icon {
      transition: transform 0.3s ease, opacity 0.3s ease;
    }
  </style>
</head>

<body class="bg-white text-black transition-colors duration-300 dark:bg-black dark:text-white">

<!-- Include Tailwind CSS CDN in your <head> if not already -->
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet" />

<!-- Chatbot Widget -->
<div id="chatbot-container" class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-2">

  <!-- Chat icon button -->
  <button id="chatbot-toggle" 
          class="bg-blue-600 hover:bg-blue-700 text-white p-4 rounded-full shadow-lg focus:outline-none"
          aria-label="Toggle Chatbot">
    <!-- Chat icon SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" 
         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" 
            d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4-.86L3 20l1.86-4A8.972 8.972 0 013 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
  </button>

  <!-- Chat window, hidden by default -->
  <div id="chat-window" 
       class="hidden w-80 bg-white dark:bg-gray-900 rounded-lg shadow-xl border border-gray-300 dark:border-gray-700 flex flex-col"
       style="height: 400px;">

    <!-- Header -->
    <div class="bg-blue-600 text-white px-4 py-2 rounded-t-lg flex justify-between items-center">
      <h2 class="font-semibold">Chat with Gemini</h2>
      <button id="chat-close" aria-label="Close Chat" class="text-white hover:text-gray-200">&times;</button>
    </div>
 
    <!-- Chat messages container -->
    <div id="chat-messages" 
         class="flex-1 overflow-y-auto p-4 space-y-3 text-gray-800 dark:text-gray-200"
         style="scroll-behavior: smooth;"></div>

    <!-- Input area -->
    <form id="chat-form" class="flex border-t border-gray-300 dark:border-gray-700">
      <input type="text" id="chat-input" placeholder="Ask me something..." required
             class="flex-grow px-3 py-2 focus:outline-none bg-gray-100 dark:bg-gray-800 text-black dark:text-white" />
      <button type="submit" class="bg-blue-600 text-white px-4 hover:bg-blue-700 transition">Send</button>
    </form>
  </div>
</div>

<script>
  const toggleBtn = document.getElementById('chatbot-toggle');
  const chatWindow = document.getElementById('chat-window');
  const chatCloseBtn = document.getElementById('chat-close');
  const chatForm = document.getElementById('chat-form');
  const chatInput = document.getElementById('chat-input');
  const chatMessages = document.getElementById('chat-messages');

  // Toggle chat window visibility
  toggleBtn.addEventListener('click', () => {
    chatWindow.classList.toggle('hidden');
    if (!chatWindow.classList.contains('hidden')) {
      chatInput.focus();
    }
  });

  chatCloseBtn.addEventListener('click', () => {
    chatWindow.classList.add('hidden');
  });

  // Append message to chat with dark mode colors
  function appendMessage(sender, text) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('rounded-lg', 'p-2', 'max-w-[75%]', 'break-words');

    if(sender === 'user'){
      // User bubble: light blue in light mode, darker blue in dark mode
      messageDiv.classList.add(
        'self-end', 'text-right', 
        'bg-blue-100', 'dark:bg-blue-800', 'dark:text-blue-100'
      );
      messageDiv.textContent = text;
    } else {
      // Bot bubble: light gray in light mode, dark gray in dark mode
      messageDiv.classList.add(
        'self-start', 'text-left', 
        'bg-gray-100', 'dark:bg-gray-700', 'dark:text-gray-200'
      );
      messageDiv.textContent = text;
    }

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
  }

  // Handle form submission to send message
  chatForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const message = chatInput.value.trim();
    if (!message) return;

    appendMessage('user', message);
    chatInput.value = '';
    chatInput.disabled = true;

    try {
      const res = await fetch('chatbot.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams({message})
      });

      const data = await res.json();

      if(data.reply){
        appendMessage('bot', data.reply);
      } else {
        appendMessage('bot', 'Error: ' + (data.error || 'Unknown error'));
      }
    } catch (error) {
      appendMessage('bot', 'Network error. Please try again.');
    }

    chatInput.disabled = false;
    chatInput.focus();
  });
</script>

  <!-- Navbar --><header class="w-full fixed top-0 left-0 z-50 backdrop-blur-md bg-white/80 dark:bg-black/80 transition-colors duration-500">
  <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
  <div class="flex items-center">
  <img src="pictures/cplogo.png" alt="Charles Pura Logo" class="h-12 md:h-10 sm:h-9" />
</div>

    <nav class="hidden md:flex space-x-16 px-4 py-2 rounded-md">
      <a href="#about" class="hover:text-blue-600 dark:hover:text-blue-400 transition">About</a>
      <a href="#skills" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Skills</a>
      <a href="#projects" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Projects</a>
      <a href="#contact" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
    </nav>
 
    <!-- Theme toggle & Hamburger -->
    <div class="flex items-center space-x-4">
  <button id="theme-toggle" class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 transition relative">
<!-- Sun Icon for light mode (but shown only in dark mode to *switch* back to light) -->
<svg id="sun-icon" class="theme-icon w-6 h-6 hidden dark:inline" xmlns="http://www.w3.org/2000/svg"
  fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
  <circle cx="12" cy="12" r="5" fill="yellow"></circle>
  <path stroke="yellow" stroke-linecap="round" stroke-linejoin="round"
    d="M12 1v2m0 18v2m9-11h-2M5 12H3m15.36-7.36l-1.42 1.42M6.06 17.94l-1.42 1.42m12.72 0l1.42-1.42M6.06 6.06L4.64 4.64" />
</svg>

  <!-- Moon Icon for dark mode (but shown only in light mode to *switch* to dark) -->
  <svg id="moon-icon" class="theme-icon w-6 h-6 text-gray-800 dark:hidden" xmlns="http://www.w3.org/2000/svg"
    fill="currentColor" viewBox="0 0 24 24">
    <path
      d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" />
  </svg>

  <span id="theme-tooltip"
    class="absolute top-1/2 left-full ml-2 transform -translate-y-1/2 px-2 py-1 text-xs rounded bg-gray-800 text-white opacity-0 pointer-events-none transition-opacity whitespace-nowrap">
    Dark Theme
  </span>
</button>

<style>
  #theme-toggle:hover #theme-tooltip {
    opacity: 1;
    pointer-events: auto;
  }

  #theme-toggle.preview-light {
    background-color: #f3f4f6;
  }
  #theme-toggle.preview-light .theme-icon {
    color: black !important;
  }

  #theme-toggle.preview-dark {
    background-color: #374151;
  }
  #theme-toggle.preview-dark .theme-icon {
    color: white !important;
  }
</style>
<script>
  const themeToggle = document.getElementById('theme-toggle');
  const themeTooltip = document.getElementById('theme-tooltip');
  const htmlElement = document.documentElement;

  function isDarkMode() {
    return htmlElement.classList.contains('dark');
  }

  function updateTooltipAndPreview() {
    if (isDarkMode()) {
      themeTooltip.textContent = 'Light Theme';
      if (themeToggle.matches(':hover')) {
        themeToggle.classList.add('preview-light');
        themeToggle.classList.remove('preview-dark');
      } else {
        themeToggle.classList.remove('preview-light', 'preview-dark');
      }
    } else {
      themeTooltip.textContent = 'Dark Theme';
      if (themeToggle.matches(':hover')) {
        themeToggle.classList.add('preview-dark');
        themeToggle.classList.remove('preview-light');
      } else {
        themeToggle.classList.remove('preview-light', 'preview-dark');
      }
    }
  }

  themeToggle.addEventListener('click', () => {
    // Toggle theme
    if (isDarkMode()) {
      htmlElement.classList.remove('dark');
      localStorage.setItem('theme', 'light');
    } else {
      htmlElement.classList.add('dark');
      localStorage.setItem('theme', 'dark');
    }

    updateTooltipAndPreview();

    // Mobile view logic: show tooltip and preview for 1 second
    if (window.innerWidth <= 768) {
      // Show tooltip
      themeTooltip.style.opacity = '1';
      themeTooltip.style.pointerEvents = 'auto';

      // Show hover preview effect
      if (isDarkMode()) {
        themeToggle.classList.add('preview-light');
        themeToggle.classList.remove('preview-dark');
      } else {
        themeToggle.classList.add('preview-dark');
        themeToggle.classList.remove('preview-light');
      }

      setTimeout(() => {
        // Hide tooltip
        themeTooltip.style.opacity = '0';
        themeTooltip.style.pointerEvents = 'none';
        // Remove hover preview classes
        themeToggle.classList.remove('preview-light', 'preview-dark');
      }, 1000); // 1 second
    }
  });

  themeToggle.addEventListener('mouseenter', updateTooltipAndPreview);
  themeToggle.addEventListener('mouseleave', () => {
    themeToggle.classList.remove('preview-light', 'preview-dark');
    updateTooltipAndPreview();
  });

  // Initialize theme on page load
  if (localStorage.getItem('theme') === 'dark') {
    htmlElement.classList.add('dark');
  } else {
    htmlElement.classList.remove('dark');
  }

  updateTooltipAndPreview();
</script>



   <button id="hamburger" 
        class="md:hidden p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300 ease-in-out group">
  <svg class="w-6 h-6 text-black dark:text-white transform transition-transform duration-300 group-hover:rotate-90" 
       xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M4 6h16M4 12h16M4 18h16" />
  </svg>
</button>

    </div>
  </div>

    <!-- Mobile Nav -->
    <nav id="mobile-menu" class="hidden md:hidden flex-col space-y-4 bg-white/90 dark:bg-black/90 backdrop-blur p-4 rounded-md mx-4 mt-2 text-sm">
      <a href="#about" class="block hover:text-blue-600 dark:hover:text-blue-400 transition">About</a>
      <a href="#skills" class="block hover:text-blue-600 dark:hover:text-blue-400 transition">Skills</a>
      <a href="#projects" class="block hover:text-blue-600 dark:hover:text-blue-400 transition">Projects</a>
      <a href="#contact" class="block hover:text-blue-600 dark:hover:text-blue-400 transition">Contact</a>
    </nav>
</header>

<!-- JS Script -->
<script>
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const navLinks = document.querySelectorAll('nav a');

  hamburger.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
    const svg = hamburger.querySelector('svg');
    if (mobileMenu.classList.contains('hidden')) {
      // Show hamburger icon
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
    } else {
      // Show close (X) icon
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
    }
    // Add a quick scale animation for a more dynamic effect
    svg.classList.add('scale-110');
    setTimeout(() => {
      svg.classList.remove('scale-110');
    }, 200);
  });

  navLinks.forEach(link => {
    link.addEventListener('click', () => {
      navLinks.forEach(nav => {
        nav.classList.remove('text-blue-600', 'dark:text-blue-400');
      });
      link.classList.add('text-blue-600', 'dark:text-blue-400');

      if (!mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('hidden');
        // Reset hamburger icon when closing mobile menu
        const svg = hamburger.querySelector('svg');
        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />';
      }
    });
  });
</script>

<?php
include("robot.php");
?>


<section id="about" class="min-h-screen flex flex-col items-center justify-start bg-white dark:bg-black px-6 md:px-10 py-16">
  <h2 class="text-3xl font-bold mt-0 mb-8 text-black dark:text-white text-center">About</h2>

  <div class="w-full max-w-6xl mx-auto flex flex-col-reverse md:flex-row items-start gap-10">
    <!-- Left: Text -->
    <div class="md:flex-1">
      <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 font-mono text-justify animate-on-scroll">
        I am Charles Pura, a Full-Stack Developer and IT student at Bestlink College of the Philippines. I have built over 100 customized projects using technologies like HTML, CSS, JavaScript, PHP, Bootstrap, Tailwind, and Android Studio with Java.
      </p>
      <br>
      <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 font-mono text-justify animate-on-scroll">
        I specialize in creating efficient, user-friendly web and mobile applications. I enjoy turning ideas into scalable and secure solutions that meet real-world needs.
      </p>
      <br>
      <p class="text-base leading-relaxed text-gray-700 dark:text-gray-300 font-mono text-justify animate-on-scroll">
        I aim to grow by working on innovative projects that challenge me and make a positive impact for users and businesses.
      </p>
    </div>

    <!-- Right: Profile Image -->
    <div class="relative flex justify-center md:justify-end w-full md:w-auto">
      <!-- Liquid blob background -->
      <div class="absolute -top-6 -right-6 w-56 h-56 sm:w-72 sm:h-72 bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 opacity-70 blur-3xl animate-blob z-0"></div>
      <!-- Profile image -->
      <img src="profile.jpg" alt="Profile Picture" id="profile-picture" class="relative z-10 w-24 h-24 sm:w-48 sm:h-48 rounded-full object-cover shadow-2xl border-4 border-white dark:border-gray-800 transition-transform duration-500 ease-in-out">
    </div>
  </div> 
<style>@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.8s ease forwards;
}
</style>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const observerOptions = {
      threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-fadeInUp");
          observer.unobserve(entry.target); // Animate only once
        }
      });
    }, observerOptions);

    document.querySelectorAll(".animate-on-scroll").forEach(el => {
      // Initial state: hidden and shifted down
      el.style.opacity = 0;
      el.style.transform = "translateY(20px)";
      observer.observe(el);
    });
  });
</script>
<!-- My Journey Timeline -->
<section id="journey" class="mt-20 w-full max-w-4xl">
  <h3 class="text-2xl font-bold text-center text-black dark:text-white mb-8">My Journey</h3>
  <div class="relative">
    <!-- Vertical timeline line -->
    <div class="absolute left-1/2 transform -translate-x-1/2 h-full w-0.5 bg-gray-300 dark:bg-gray-700"></div>

    <!-- Timeline items -->
    <div class="space-y-10">

      <!-- October 2023 -->
      <div class="timeline-item opacity-0 translate-x-10 transition duration-700 ease-out transform flex flex-col sm:flex-row items-center">
        <div class="flex-1 sm:pr-10 text-right">
          <div class="p-4 bg-blue-100 dark:bg-blue-900 rounded-lg shadow-sm">
            <h4 class="font-semibold text-blue-700 dark:text-blue-300">October 2023</h4>
            <p class="text-gray-600 dark:text-gray-300">HTML, CSS, JS - Simple website portfolio</p>
            <div class="flex justify-end gap-2 mt-2 text-xl text-blue-500 dark:text-blue-300">
              <div class="relative group">
                <i class="fab fa-html5"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  HTML5
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-css3-alt"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  CSS3
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-js-square"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  JavaScript
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Updated tooltip for laptop-code icon -->
        <div
          class="relative group w-10 h-10 rounded-full bg-blue-500 border-4 border-white dark:border-gray-800 flex items-center justify-center z-10">
          <i class="fas fa-laptop-code text-white text-lg"></i>
          <span
            class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
            Web Development
          </span>
        </div>

        <div class="flex-1"></div>
      </div>

      <!-- June 2024 -->
      <div class="timeline-item opacity-0 -translate-x-10 transition duration-700 ease-out transform relative flex flex-col sm:flex-row items-center">
        <div class="flex-1"></div>
        <div
          class="relative group w-10 h-10 rounded-full bg-green-500 border-4 border-white dark:border-gray-800 flex items-center justify-center z-10">
          <i class="fas fa-database text-white text-lg"></i>
          <span
            class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
            Database
          </span>
        </div>

        <div class="flex-1 sm:pl-10">
          <div class="p-4 bg-green-100 dark:bg-green-900 rounded-lg shadow-sm">
            <h4 class="font-semibold text-green-700 dark:text-green-300">June 2024</h4>
            <p class="text-gray-600 dark:text-gray-300">Learned simple CRUD functions - Over 100+ projects done in PHP, HTML, CSS, JS</p>
            <div class="flex gap-2 mt-2 text-xl text-green-500 dark:text-green-300">
              <div class="relative group">
                <i class="fab fa-php"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  PHP
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-html5"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  HTML5
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-css3-alt"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  CSS3
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-js-square"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  JavaScript
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- October 2024 -->
      <div class="timeline-item opacity-0 translate-x-10 transition duration-700 ease-out transform relative flex flex-col sm:flex-row items-center">
        <div class="flex-1 sm:pr-10 text-right">
          <div class="p-4 bg-yellow-100 dark:bg-yellow-900 rounded-lg shadow-sm">
            <h4 class="font-semibold text-yellow-700 dark:text-yellow-300">October 2024</h4>
            <p class="text-gray-600 dark:text-gray-300">Learned about Git and GitHub</p>
            <div class="flex justify-end gap-2 mt-2 text-xl text-yellow-500 dark:text-yellow-300">
              <div class="relative group">
                <i class="fab fa-git-alt"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  Git
                </span>
              </div>
              <div class="relative group">
                <i class="fab fa-github"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  GitHub
                </span>
              </div>
            </div>
          </div>
        </div>
        <div
          class="relative group w-10 h-10 rounded-full bg-yellow-500 border-4 border-white dark:border-gray-800 flex items-center justify-center z-10">
          <i class="fas fa-code-branch text-white text-lg"></i>
          <span
            class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
            Version Control
          </span>
        </div>
        <div class="flex-1"></div>
      </div>

      <!-- December 2024 -->
      <div class="timeline-item opacity-0 -translate-x-10 transition duration-700 ease-out transform relative flex flex-col sm:flex-row items-center">
        <div class="flex-1"></div>
        <div
          class="relative group w-10 h-10 rounded-full bg-purple-500 border-4 border-white dark:border-gray-800 flex items-center justify-center z-10">
          <i class="fas fa-upload text-white text-lg"></i>
          <span
            class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
            Deploy
          </span>
        </div>
        <div class="flex-1 sm:pl-10">
          <div class="p-4 bg-purple-100 dark:bg-purple-900 rounded-lg shadow-sm">
            <h4 class="font-semibold text-purple-700 dark:text-purple-300">December 2024</h4>
            <p class="text-gray-600 dark:text-gray-300">Learned to deploy simple projects using GitHub Pages</p>
            <div class="flex gap-2 mt-2 text-xl text-purple-500 dark:text-purple-300">
              <div class="relative group">
                <i class="fab fa-github"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  GitHub
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- February 2025 -->
      <div class="timeline-item opacity-0 translate-x-10 transition duration-700 ease-out transform relative flex flex-col sm:flex-row items-center">
        <div class="flex-1 sm:pr-10 text-right">
          <div class="p-4 bg-indigo-100 dark:bg-indigo-900 rounded-lg shadow-sm">
            <h4 class="font-semibold text-indigo-700 dark:text-indigo-300">February 2025</h4>
            <p class="text-gray-600 dark:text-gray-300">Learned Android Studio - Completed 3 apps</p>
            <div class="flex justify-end gap-2 mt-2 text-xl text-indigo-500 dark:text-indigo-300">
              <div class="relative group">
                <i class="fab fa-android"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  Android
                </span>
              </div>
              <div class="relative group">
                <i class="fas fa-mobile-alt"></i>
                <span
                  class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
                  Mobile
                </span>
              </div>
            </div>
          </div>
        </div>
        <div
          class="relative group w-10 h-10 rounded-full bg-indigo-500 border-4 border-white dark:border-gray-800 flex items-center justify-center z-10">
          <i class="fas fa-mobile text-white text-lg"></i>
          <span
            class="absolute bottom-full mb-1 hidden group-hover:flex items-center justify-center px-2 py-1 text-xs bg-gray-700 text-white rounded shadow transition-opacity duration-300">
            Mobile Apps
          </span>
        </div>
        <div class="flex-1"></div>
      </div>

    </div>
  </div>
</section>

<script>
  // Scroll animation using Intersection Observer
  document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.timeline-item');

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.remove('opacity-0', 'translate-x-10', '-translate-x-10');
          entry.target.classList.add('opacity-100', 'translate-x-0');
          observer.unobserve(entry.target); // Animate only once
        }
      });
    }, {
      threshold: 0.2
    });

    items.forEach(item => {
      observer.observe(item);
    });
  });
</script>

  <!-- Animate profile picture -->
  <script>
    const profilePicture = document.getElementById('profile-picture');
    const aboutSection = document.getElementById('about');
    function animateProfilePicture() {
      profilePicture.classList.add('scale-110');
      setTimeout(() => {
        profilePicture.classList.remove('scale-110');
      }, 1000);
    }
    let observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateProfilePicture();
        }
      });
    }, { threshold: 0.5 });
    observer.observe(aboutSection);
  </script>

  <style>
    #profile-picture.scale-110 {
      transform: scale(1.1);
    }
  </style>
</section>

  <!-- View My Projects Button and Vertical Line -->
  <div class="flex flex-col items-center mt-8 relative">
    <!-- Button -->
    <a href="#projects" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-400 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-300 z-10">
      View My Projects
    </a>

    <!-- Vertical Line & Arrow (connector) -->
    <a href="#projects" class="flex flex-col items-center mt-2 group">
      <div class="w-1 h-32 bg-blue-600 dark:bg-blue-500 transition-transform group-hover:scale-y-110"></div>
    </a>
    <script>
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
          e.preventDefault();
          const target = document.querySelector(this.getAttribute('href'));
          if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
          }
        });
      });
    </script>
  </div>
<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


<!-- Skills Section -->
<section id="skills" class="min-h-screen flex flex-col items-center justify-start text-center bg-[#f5f5f5] dark:bg-gray-800 px-4 transition-colors duration-500 py-16">
  <h2 class="text-3xl font-bold mt-0 mb-8 text-black dark:text-white text-center">Skills</h2>
  <p class="text-lg mb-10 text-gray-700 dark:text-gray-300">These are the skills I excel in:</p>

  <!-- Scrollable Skills Row (Top: Left to Right) -->
  <div class="relative w-full overflow-hidden">
    <div class="flex w-max animate-scroll-right gap-8">
      <div class="flex gap-8">
        <!-- Skills -->
<div class="skill-card bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-gray-600 dark:text-gray-300 w-[500px] py-12 px-15">
  <h3 class="text-4xl font-semibold text-blue-600 dark:text-blue-400 mb-4 flex items-center gap-4">
    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5 Logo" class="h-12 w-12 object-contain" />
    HTML
  </h3>
  <p class="text-lg">Structure and markup for web pages.</p>
</div>

        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-gray-600 dark:text-gray-300 min-w-[350px]">
          <h3 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3 Logo" class="h-6 w-6 object-contain" />
            CSS
          </h3>
          <p>Styling and layout of web pages.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-yellow-500 dark:text-yellow-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript Logo" class="h-6 w-6 object-contain" />
            JavaScript
          </h3>
          <p>Interactivity and logic for websites.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-purple-600 dark:text-purple-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP Logo" class="h-6 w-6 object-contain" />
            PHP
          </h3>
          <p>Server-side scripting for web apps.</p>
        </div>
      </div>
      <!-- Duplicate for loop -->
      <div class="flex gap-8">
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-gray-600 dark:text-gray-300 min-w-[350px]">
          <h3 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5 Logo" class="h-6 w-6 object-contain" />
            HTML
          </h3>
          <p>Structure and markup for web pages.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-gray-600 dark:text-gray-300 min-w-[350px]">
          <h3 class="text-2xl font-semibold text-blue-600 dark:text-blue-400 mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3 Logo" class="h-6 w-6 object-contain" />
            CSS
          </h3>
          <p>Styling and layout of web pages.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-yellow-500 dark:text-yellow-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" alt="JavaScript Logo" class="h-6 w-6 object-contain" />
            JavaScript
          </h3>
          <p>Interactivity and logic for websites.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-purple-600 dark:text-purple-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP Logo" class="h-6 w-6 object-contain" />
            PHP
          </h3>
          <p>Server-side scripting for web apps.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Scrollable Skills Row (Bottom: Right to Left) -->
  <div class="relative w-full overflow-hidden mt-8">
    <div class="flex w-max animate-scroll-left gap-8">
      <div class="flex gap-8">
        <!-- Skills -->
       <div class="skill-card bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-green-500 dark:text-green-400 w-[500px] py-12 px-15">
  <h3 class="text-4xl font-semibold mb-4 flex items-center gap-4">
    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="SQL Logo" class="h-12 w-12 object-contain" />
    SQL
  </h3>
  <p class="text-lg">Database management and queries.</p>
</div>

        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-teal-500 dark:text-teal-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/android/android-original.svg" alt="Android Studio Logo" class="h-6 w-6 object-contain" />
            Android Studio
          </h3>
          <p>Building Android mobile applications.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-red-500 dark:text-red-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java Logo" class="h-6 w-6 object-contain" />
            Java
          </h3>
          <p>Programming language for Android apps.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-indigo-600 dark:text-indigo-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-plain.svg" alt="Bootstrap Logo" class="h-6 w-6 object-contain" />
            Bootstrap
          </h3>
          <p>CSS framework for responsive design.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-blue-500 dark:text-blue-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Tailwind_CSS_Logo.svg/250px-Tailwind_CSS_Logo.svg.png" alt="Tailwind CSS Logo" class="h-6 w-6 object-contain" />
            Tailwind CSS
          </h3>
          <p>Utility-first CSS framework for custom designs.</p>
        </div>
      </div>
      <!-- Duplicate -->
      <div class="flex gap-8">
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-green-500 dark:text-green-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="SQL Logo" class="h-6 w-6 object-contain" />
            SQL
          </h3>
          <p>Database management and queries.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-teal-500 dark:text-teal-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/android/android-original.svg" alt="Android Studio Logo" class="h-6 w-6 object-contain" />
            Android Studio
          </h3>
          <p>Building Android mobile applications.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-red-500 dark:text-red-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java Logo" class="h-6 w-6 object-contain" />
            Java
          </h3>
          <p>Programming language for Android apps.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-indigo-600 dark:text-indigo-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/bootstrap/bootstrap-plain.svg" alt="Bootstrap Logo" class="h-6 w-6 object-contain" />
            Bootstrap
          </h3>
          <p>CSS framework for responsive design.</p>
        </div>
        <div class="skill-card p-6 bg-white dark:bg-gray-700 rounded-xl shadow-md hover:shadow-lg transition hover:scale-105 text-blue-500 dark:text-blue-400 min-w-[350px]">
          <h3 class="text-2xl font-semibold mb-2 flex items-center gap-2">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Tailwind_CSS_Logo.svg/250px-Tailwind_CSS_Logo.svg.png" alt="Tailwind CSS Logo" class="h-6 w-6 object-contain" />
            Tailwind CSS
          </h3>
          <p>Utility-first CSS framework for custom designs.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<style>
  @keyframes scroll-left {
    0% { transform: translateX(0%); }
    100% { transform: translateX(-50%); }
  }

  @keyframes scroll-right {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0%); }
  }

  .animate-scroll-left {
    animation: scroll-left 25s linear infinite;
  }

  .animate-scroll-right {
    animation: scroll-right 25s linear infinite;
  }

  .scrollbar-hide::-webkit-scrollbar {
    display: none;
  }

  .scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
  }

  /* Pause animation only on hover */
  .animate-scroll-left:hover,
  .animate-scroll-right:hover {
    animation-play-state: paused;
    cursor: pointer;
  }
</style>

  <!-- Vertical Line & Arrow (connector) -->
  <a href="#projects" class="flex flex-col items-center mt-2 group">
    <div class="w-1 h-16 bg-blue-600 dark:bg-blue-500 transition-transform group-hover:scale-y-110"></div>
    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-blue-600 dark:text-blue-500 mt-1 group-hover:translate-y-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
  </a>
</div>


<!-- Projects section -->
<section id="projects" class="min-h-screen flex flex-col items-center justify-center text-center bg-white dark:bg-black px-4 transition-colors duration-500 py-16">
  <h2 class="text-3xl font-bold mb-4 text-black dark:text-white">Projects</h2>
  <p class="text-lg text-gray-700 dark:text-gray-300 max-w-2xl mb-12">
    I've created <span class="font-semibold text-blue-600 dark:text-blue-400">173+</span> and counting — including <strong>Diagrams</strong>, <strong>Web Systems</strong>, <strong>CRUD Operations</strong>, <strong>Android Apps</strong>, and more. Check out some of my featured work below!
  </p>
  <p class="text-md text-gray-500 dark:text-gray-400 mb-6">
    You can drag and swap any of the project cards below to rearrange them as you like.
  </p>

  <!-- Draggable Containers -->
  <div id="project-grid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-8 mb-16 transition-opacity duration-500 opacity-100">

    <!-- Logistics System -->
    <div class="project-card bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 cursor-move opacity-0 translate-y-8 transition-all duration-700 ease-out">
      <button onclick="openImage('pictures/logistic1.png')">
        <img src="pictures/logistic1.png" alt="Logistics System" class="rounded-lg mb-4 w-full object-cover h-64 cursor-pointer" />
      </button>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Logistics System</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-4">An efficient system for managing logistics, inventory, and delivery tracking.</p>

      <!-- Logos container -->
      <div class="custom-tooltip-container flex space-x-3 items-center">
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5" class="w-6 h-6" />
          <span class="tooltip-text">HTML5</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3" class="w-6 h-6" />
          <span class="tooltip-text">CSS3</span>
        </div>
        <div class="tooltip">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Tailwind_CSS_Logo.svg/250px-Tailwind_CSS_Logo.svg.png" alt="Tailwind CSS" class="w-6 h-6" />
          <span class="tooltip-text">Tailwind CSS</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" class="w-6 h-6" />
          <span class="tooltip-text">PHP</span>
        </div>
      </div>
    </div>

    <!-- Threat Management System -->
    <div class="project-card bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 cursor-move opacity-0 translate-y-8 transition-all duration-700 ease-out">
      <button onclick="openImage('pictures/threatmanagement.png')">
        <img src="pictures/threatmanagement.png" alt="Threat Management System" class="rounded-lg mb-4 w-full object-cover h-64 cursor-pointer" />
      </button>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Threat Management System</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-3">
        A security-based system for managing and reporting threats effectively.
      </p>

      <!-- Language Logos with Tooltips -->
      <div class="flex space-x-3 items-center">
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" alt="HTML5" class="w-6 h-6" />
          <span class="tooltip-text">HTML5</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" alt="CSS3" class="w-6 h-6" />
          <span class="tooltip-text">CSS3</span>
        </div>
        <div class="tooltip">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Tailwind_CSS_Logo.svg/250px-Tailwind_CSS_Logo.svg.png" alt="Tailwind CSS" class="w-6 h-6" />
          <span class="tooltip-text">Tailwind CSS</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" class="w-6 h-6" />
          <span class="tooltip-text">PHP</span>
        </div>
      </div>
    </div>

    <!-- PetFinder App -->
    <div class="project-card bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 cursor-move opacity-0 translate-y-8 transition-all duration-700 ease-out">
      <button onclick="openImage('pictures/Petfinder1.png')">
        <img src="pictures/Petfinder1.png" alt="PetFinder App" class="rounded-lg mb-2 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/petfinder3.png')">
        <img src="pictures/petfinder3.png" alt="PetFinder App" class="rounded-lg mb-2 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/petfinder2.png')">
        <img src="pictures/petfinder2.png" alt="PetFinder App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/petfinder4.png')">
        <img src="pictures/petfinder4.png" alt="PetFinder App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">PetFinder App</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-3">Generates pet info and scans QR codes to display pet profiles.</p>

      <!-- Android Studio & Java Logos with Tooltips -->
      <div class="flex space-x-3 items-center">
        <div class="tooltip">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Android_Studio_icon_%282023%29.svg/2048px-Android_Studio_icon_%282023%29.svg.png" alt="Android Studio" class="w-6 h-6" />
          <span class="tooltip-text">Android Studio</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="w-6 h-6" />
          <span class="tooltip-text">Java</span>
        </div>
      </div>
    </div>

    <!-- Kids Learning App -->
    <div class="project-card bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 cursor-move opacity-0 translate-y-8 transition-all duration-700 ease-out">
      <button onclick="openImage('pictures/kidslearningapp1.png')">
        <img src="pictures/kidslearningapp1.png" alt="Kids Learning App" class="rounded-lg mb-2 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/kidslearningapp2.png')">
        <img src="pictures/kidslearningapp2.png" alt="Kids Learning App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Kids Learning App</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-3">Features fingerprint login, videos, and fun trivia quizzes for kids.</p>

      <!-- Tech Logos with Tooltips -->
      <div class="flex space-x-3 items-center">
        <div class="tooltip">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Android_Studio_icon_%282023%29.svg/2048px-Android_Studio_icon_%282023%29.svg.png" alt="Android Studio" class="w-6 h-6" />
          <span class="tooltip-text">Android Studio</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="w-6 h-6" />
          <span class="tooltip-text">Java</span>
        </div>
        <div class="tooltip">
          <img src="https://cdn-icons-png.flaticon.com/512/1041/1041889.png" alt="Android Biometric" class="w-6 h-6 rounded" />
          <span class="tooltip-text">Android Biometric</span>
        </div>
      </div>
    </div>

    <!-- Quiz App -->
    <div class="project-card bg-white dark:bg-gray-700 rounded-xl shadow-md p-4 cursor-move opacity-0 translate-y-8 transition-all duration-700 ease-out">
      <button onclick="openImage('pictures/Quizapp.png')">
        <img src="pictures/Quizapp.png" alt="Quiz App" class="rounded-lg mb-2 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/quizapp2.png')">
        <img src="pictures/quizapp2.png" alt="Quiz App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/quizapp3.png')">
        <img src="pictures/quizapp3.png" alt="Quiz App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/quizapp4.png')">
        <img src="pictures/quizapp4.png" alt="Quiz App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <button onclick="openImage('pictures/quizapp5.png')">
        <img src="pictures/quizapp5.png" alt="Quiz App" class="rounded-lg mb-4 w-full h-40 object-cover cursor-pointer" />
      </button>
      <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Quiz App</h3>
      <p class="text-gray-600 dark:text-gray-300 mb-3">Allows registration with email verification, password reset, and includes a leaderboard.</p>

      <!-- Tech Logos with Tooltips -->
      <div class="flex space-x-3 items-center">
        <!-- Android Studio -->
        <div class="tooltip">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Android_Studio_icon_%282023%29.svg/2048px-Android_Studio_icon_%282023%29.svg.png" alt="Android Studio" class="w-6 h-6" />
          <span class="tooltip-text">Android Studio</span>
        </div>
        <!-- Java -->
        <div class="tooltip">
          <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/java/java-original.svg" alt="Java" class="w-6 h-6" />
          <span class="tooltip-text">Java</span>
        </div>
        <!-- Firebase Authentication -->
        <div class="tooltip">
          <img src="https://www.vectorlogo.zone/logos/firebase/firebase-icon.svg" alt="Firebase Authentication" class="w-6 h-6" />
          <span class="tooltip-text">Firebase Authentication</span>
        </div>
      </div>
    </div>

  </div>

  <!-- Tooltip styles (keep them) -->
  <style>
    /* Tooltip container */
    .tooltip {
      position: relative;
      display: inline-block;
    }

    /* Tooltip text */
    .tooltip .tooltip-text {
      visibility: hidden;
      width: max-content;
      background-color: #333;
      color: #fff;
      text-align: center;
      border-radius: 4px;
      padding: 4px 8px;
      position: absolute;
      z-index: 10;
      bottom: 125%; /* show above the icon */
      left: 50%;
      transform: translateX(-50%);
      opacity: 0;
      transition: opacity 0.3s;
      white-space: nowrap;
      font-size: 0.75rem; /* smaller text */
    }

    /* Show tooltip text on hover */
    .tooltip:hover .tooltip-text {
      visibility: visible;
      opacity: 1;
    }

    /* Tooltip arrow */
    .tooltip .tooltip-text::after {
      content: '';
      position: absolute;
      top: 100%; /* arrow below tooltip */
      left: 50%;
      margin-left: -5px;
      border-width: 5px;
      border-style: solid;
      border-color: #333 transparent transparent transparent;
    }
  </style>



<script>
  // Scroll animation using Intersection Observer for project cards
  document.addEventListener('DOMContentLoaded', () => {
    const projectCards = document.querySelectorAll('.project-card');

    const observer = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.remove('opacity-0', 'translate-y-8');
          entry.target.classList.add('opacity-100', 'translate-y-0');
          observer.unobserve(entry.target); // Animate only once
        }
      });
    }, {
      threshold: 0.15
    });

    projectCards.forEach(card => {
      observer.observe(card);
    });
  });

  // Function to open images in a modal or new tab (you can customize this)
  function openImage(src) {
    window.open(src, '_blank');
  }
</script>

  
  <!-- Pagination Controls -->
  <div id="pagination" class="flex justify-center gap-4"></div>
</section>

<!-- Fullscreen Image Viewer -->
<div id="fullscreenViewer" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 hidden" onclick="closeImage()">
  <img id="viewerImage" src="" alt="Full Image" class="max-h-[90%] max-w-[90%] rounded-lg shadow-xl border-4 border-white">
</div>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
  // Function to handle drag-swap disabling and hiding paragraph
  function handleResponsiveDragSwap() {
    const infoParagraph = document.querySelector("#projects p.text-md.text-gray-500");
    const projectCards = document.querySelectorAll("#project-grid > div");

    if (window.innerWidth <= 640) { // e.g., 640px = Tailwind's sm breakpoint
      // Hide the paragraph
      if (infoParagraph) {
        infoParagraph.style.display = "none";
      }

      // Remove drag-swap classes
      projectCards.forEach(card => {
        card.classList.remove("cursor-move");
      });

      // Destroy Sortable instance if it exists
      if (sortableInstance) {
        sortableInstance.destroy();
        sortableInstance = null;
      }
    } else {
      // Show the paragraph
      if (infoParagraph) {
        infoParagraph.style.display = "block";
      }

      // Re-add drag-swap classes
      projectCards.forEach(card => {
        if (!card.classList.contains("cursor-move")) {
          card.classList.add("cursor-move");
        }
      });

      // Re-initialize Sortable if not already initialized
      if (!sortableInstance) {
        initDragDrop();
      }
    }
  }

  // Drag & Drop Init
  let sortableInstance;
  function initDragDrop() {
    sortableInstance = new Sortable(document.getElementById('project-grid'), {
      animation: 150,
      ghostClass: 'opacity-50',
    });
  }

  // Image Viewer
  function openImage(src) {
    const viewer = document.getElementById('fullscreenViewer');
    const viewerImage = document.getElementById('viewerImage');
    viewerImage.src = src;
    viewer.classList.remove('hidden');
  }

  function closeImage() {
    const viewer = document.getElementById('fullscreenViewer');
    viewer.classList.add('hidden');
    document.getElementById('viewerImage').src = '';
  }

  // Pagination variables
  const projectsPerPage = 4;
  const projectGrid = document.getElementById('project-grid');
  const projects = Array.from(projectGrid.children);
  const paginationContainer = document.getElementById('pagination');
  let currentPage = 1;
  const totalPages = Math.ceil(projects.length / projectsPerPage);

  // Show projects for the current page with slide animation (left to right)
  function showProjects(page) {
    currentPage = page;

    // Slide out to left
    projectGrid.style.transition = 'transform 0.3s ease, opacity 0.3s ease';
    projectGrid.style.transform = 'translateX(-100%)';
    projectGrid.style.opacity = '0';

    setTimeout(() => {
      // Hide all projects first
      projects.forEach((proj) => {
        proj.style.display = 'none';
      });

      // Calculate start and end indices
      const start = (page - 1) * projectsPerPage;
      const end = start + projectsPerPage;

      // Show only projects for this page
      projects.slice(start, end).forEach(proj => {
        proj.style.display = '';
      });

      // Immediately set grid to offscreen right and visible
      projectGrid.style.transition = 'none';
      projectGrid.style.transform = 'translateX(100%)';
      projectGrid.style.opacity = '1';

      // Force a reflow so the browser acknowledges the transform change
      projectGrid.offsetHeight; 

      // Slide in from right
      projectGrid.style.transition = 'transform 0.3s ease';
      projectGrid.style.transform = 'translateX(0)';

      // Update pagination buttons highlight
      updatePagination();
    }, 300);
  }

  // Update pagination buttons
  function updatePagination() {
    paginationContainer.innerHTML = '';

    // Prev button
    const prevBtn = document.createElement('button');
    prevBtn.innerHTML = `<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"></path></svg>`;
    prevBtn.className = 'px-3 py-2 rounded-full border border-n-4 cursor-pointer text-n-5 bg-none';
    if (currentPage === 1) {
      prevBtn.classList.add('cursor-not-allowed', 'text-n-5');
      prevBtn.disabled = true;
    }
    prevBtn.onclick = () => {
      if (currentPage > 1) showProjects(currentPage - 1);
    };
    paginationContainer.appendChild(prevBtn);

    // Page number buttons
    for (let i = 1; i <= totalPages; i++) {
      const pageBtn = document.createElement('button');
      pageBtn.textContent = i;
      pageBtn.className = 'px-4 py-2 rounded-full border border-n-4 cursor-pointer text-n-5 bg-none';

      if (i === currentPage) {
        pageBtn.classList.remove('bg-none', 'text-n-5');
        pageBtn.classList.add('bg-[#f5f5dc]', 'text-gray-800');
        pageBtn.disabled = true;
        pageBtn.classList.remove('cursor-pointer');
        pageBtn.classList.add('cursor-default');
      }
      pageBtn.onclick = () => {
        if (currentPage !== i) showProjects(i);
      };
      paginationContainer.appendChild(pageBtn);
    }

    // Next button
    const nextBtn = document.createElement('button');
    nextBtn.innerHTML = `<svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 24 24" height="1em" width="1em" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h24v24H0z"></path><path d="m12 4-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"></path></svg>`;
    nextBtn.className = 'px-3 py-2 rounded-full border border-n-4 cursor-pointer text-n-5 bg-none';
    if (currentPage === totalPages) {
      nextBtn.classList.add('cursor-not-allowed', 'text-n-5');
      nextBtn.disabled = true;
    }
    nextBtn.onclick = () => {
      if (currentPage < totalPages) showProjects(currentPage + 1);
    };
    paginationContainer.appendChild(nextBtn);
  }

  // Run on page load
  window.addEventListener("load", () => {
    handleResponsiveDragSwap();
    showProjects(1);
  });

  // Run on window resize
  window.addEventListener("resize", () => {
    handleResponsiveDragSwap();
  });
</script>
<!-- Contact Section -->
<section id="contact" class="min-h-screen flex flex-col items-center justify-center text-center bg-[#f5f5f5] dark:bg-gray-800 px-4 transition-colors duration-500 py-16">
  <h2 class="text-3xl font-bold mb-4 text-black dark:text-white">Contact</h2>
  <p class="text-lg text-gray-700 dark:text-gray-300 mb-12">
    I'd love to hear from you! Fill out the form below or use the links to connect with me.
  </p>

  <div class="flex flex-col md:flex-row gap-12 mb-6 w-full max-w-6xl px-4">
    <!-- Left: Social Icons with Text -->
    <div class="w-full md:w-1/2 flex flex-col items-center md:items-start text-center md:text-left space-y-6">
      <div>
        <p class="text-lg text-gray-700 dark:text-gray-300">Feel free to drop me an email:</p>
        <a href="mailto:charles051902pura@gmail.com" class="text-blue-600 hover:text-blue-800 text-3xl transition-all duration-300" title="Send Email">
          <i class="fas fa-envelope"></i>
        </a>
      </div>

      <div>
        <p class="text-lg text-gray-700 dark:text-gray-300">Feel free to connect with me on social media:</p>
        <a href="https://web.facebook.com/charlespuracp" target="_blank" class="text-blue-700 hover:text-blue-900 text-3xl transition-all duration-300" title="Facebook">
          <i class="fab fa-facebook"></i>
        </a>
      </div>

      <div>
        <p class="text-lg text-gray-700 dark:text-gray-300">Check out my GitHub:</p>
        <a href="https://github.com/charlespura" target="_blank" class="text-gray-800 dark:text-white hover:text-gray-600 text-3xl transition-all duration-300" title="GitHub">
          <i class="fab fa-github"></i>
        </a>
      </div>
    </div>

    <!-- Right: Form -->
    <div class="w-full md:w-1/2">
      <form id="contact-form" action="contact.php" method="POST" class="space-y-6 bg-white dark:bg-gray-700 p-6 rounded-xl shadow-md">
        <div class="flex flex-col space-y-3">
          <!-- First Name -->
          <div class="flex items-center border-b-2 border-gray-300 dark:border-gray-500">
            <i class="fas fa-user text-gray-500 dark:text-gray-300 mr-3"></i>
            <input type="text" name="first_name" placeholder="First Name" class="w-full py-2 px-4 bg-transparent text-gray-700 dark:text-gray-300 border-none focus:outline-none" required>
          </div>
          <!-- Last Name -->
          <div class="flex items-center border-b-2 border-gray-300 dark:border-gray-500">
            <i class="fas fa-user text-gray-500 dark:text-gray-300 mr-3"></i>
            <input type="text" name="last_name" placeholder="Last Name" class="w-full py-2 px-4 bg-transparent text-gray-700 dark:text-gray-300 border-none focus:outline-none" required>
          </div>
          <!-- Email -->
          <div class="flex items-center border-b-2 border-gray-300 dark:border-gray-500">
            <i class="fas fa-envelope text-gray-500 dark:text-gray-300 mr-3"></i>
            <input type="email" name="email" placeholder="Email" class="w-full py-2 px-4 bg-transparent text-gray-700 dark:text-gray-300 border-none focus:outline-none" required>
          </div>
          <!-- Phone (Optional) -->
          <div class="flex items-center border-b-2 border-gray-300 dark:border-gray-500">
            <i class="fas fa-phone-alt text-gray-500 dark:text-gray-300 mr-3"></i>
            <input type="tel" name="phone" placeholder="Phone (Optional)" class="w-full py-2 px-4 bg-transparent text-gray-700 dark:text-gray-300 border-none focus:outline-none">
          </div>
          <!-- Message -->
          <div class="flex items-center border-b-2 border-gray-300 dark:border-gray-500">
            <i class="fas fa-comment-dots text-gray-500 dark:text-gray-300 mr-3"></i>
            <textarea name="message" placeholder="Your Message" class="w-full py-2 px-4 bg-transparent text-gray-700 dark:text-gray-300 border-none focus:outline-none" rows="4" required></textarea>
          </div>

          <!-- Google reCAPTCHA widget -->
          <div class="g-recaptcha" data-sitekey="6LcR6FYrAAAAAJSvMc6lYhnTDao8kKfyy8lBfTJC"></div>

          <!-- Submit Button -->
          <button type="submit" id="submit-btn" class="w-full py-2 px-4 bg-blue-600 text-white rounded-md hover:bg-blue-800 transition-all duration-300">
            Send Message
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- SweetAlert2 CDN -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Google reCAPTCHA script -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <script>
    const form = document.getElementById('contact-form');
    const submitBtn = document.getElementById('submit-btn');

    // Function to detect if dark mode is enabled (using 'dark' class on html or body)
    function isDarkMode() {
      return document.documentElement.classList.contains('dark') || document.body.classList.contains('dark');
    }

    // Show SweetAlert2 with Tailwind styling and theme aware colors
    function showAlert(type, message) {
      const dark = isDarkMode();

      Swal.fire({
        icon: type, // 'success', 'error', 'warning', 'info', 'question'
        title: message,
        background: dark ? '#1f2937' : '#fff', // Tailwind slate-800 for dark, white for light
        color: dark ? '#f9fafb' : '#111827', // Tailwind gray-50 for dark, gray-900 for light
        confirmButtonColor: dark ? '#2563eb' : '#3b82f6', // blue-600 dark / blue-500 light
        customClass: {
          popup: 'rounded-xl shadow-lg',
          title: 'text-lg font-semibold',
          confirmButton: 'py-2 px-4 rounded-md'
        },
        timer: type === 'success' ? 3000 : undefined, // auto close success after 3s
        timerProgressBar: type === 'success'
      });
    }

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const recaptchaResponse = grecaptcha.getResponse();
      if (!recaptchaResponse) {
        showAlert('error', 'Please complete the reCAPTCHA.');
        return;
      }

      const formData = new FormData(form);
      formData.append('g-recaptcha-response', recaptchaResponse);

      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';

      try {
        const response = await fetch('contact.php', {
          method: 'POST',
          body: formData
        });

        const result = await response.json();

        if (result.success) {
          showAlert('success', result.success);
          form.reset();
          grecaptcha.reset();
        } else {
          showAlert('error', result.error || 'Oops! Something went wrong.');
          grecaptcha.reset();
        }
      } catch (error) {
        console.error('AJAX error:', error);
        showAlert('error', 'Error sending message. Please try again.');
        grecaptcha.reset();
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
      }
    });
  </script>
</section>


  
  
  <!-- Footer -->
  <footer class="bg-white dark:bg-black text-black dark:text-white py-4">
    <div class="max-w-7xl mx-auto px-4 text-center">
      <p>&copy; 2025 Charles Pura. All rights reserved.</p>
      <div class="flex justify-center space-x-4 mt-2">

      </div>
    </div>
  </footer>

  <!-- Theme Toggle Script -->
  <script>
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = document.getElementById('theme-icon');

    function setDarkMode(isDark) {
      document.documentElement.classList.toggle('dark', isDark);
      localStorage.theme = isDark ? 'dark' : 'light';
      themeIcon.innerHTML = isDark
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.293 14.293a8 8 0 11-11.586 0 8.001 8.001 0 0111.586 0z" />`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M12 3v1m0 16v1m8.66-8.66h-1M4.34 12H3m15.66 4.66l-.7-.7M6.34 6.34l-.7-.7m12.02 12.02l-.7-.7M6.34 17.66l-.7-.7M12 5a7 7 0 100 14 7 7 0 000-14z" />`;
    }

    // Button click
    themeToggle.addEventListener('click', () => {
      const isCurrentlyDark = document.documentElement.classList.contains('dark');
      setDarkMode(!isCurrentlyDark);
    });

    // On load: Set correct icon
    window.addEventListener('DOMContentLoaded', () => {
      const isDark = localStorage.theme === 'dark';
      setDarkMode(isDark);
    });
  </script>

</body>
</html>
