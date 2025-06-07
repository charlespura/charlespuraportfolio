<section id="hero" class="relative h-screen flex flex-col justify-center items-center pt-25 bg-[#f5f5f5] dark:bg-gray-800 transition-colors duration-500 px-6 md:px-12">

  <!-- Spotify Now Playing - Left middle corner -->
  <div class="absolute top-1/2 left-8 w-80 rounded-lg overflow-hidden shadow-lg transform -translate-y-1/2 bg-white dark:bg-gray-700 p-4 hidden md:flex items-center space-x-4 text-gray-700 dark:text-gray-300">
    <img id="spotify-album-art" src="" alt="Album Art" class="w-16 h-16 rounded-md hidden" />
    <div class="flex flex-col">
      <p id="spotify-track" class="font-bold">Loading...</p>
      <p id="spotify-artist" class="text-sm"></p>
    </div>
  </div>

  <!-- Google Map in right middle corner -->
  <div class="absolute top-1/2 right-8 w-80 rounded-lg overflow-hidden shadow-lg transform -translate-y-1/2 bg-white dark:bg-gray-700 p-4 hidden md:block">
    <p class="text-md mb-2 text-center text-gray-800 dark:text-gray-300 font-semibold">
      Developer Base Location:<br>Quezon City, Philippines
    </p>
    <iframe
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3861.937739717867!2d121.04819611532651!3d14.676041989797725!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b7dbb3c083eb%3A0x72f4156836dbdc71!2sQuezon%20City!5e0!3m2!1sen!2sph!4v1696239515638!5m2!1sen!2sph"
      width="100%"
      height="200"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="Quezon City Map"
    ></iframe>
  </div>





<script>
async function fetchSpotifyStatus() {
  try {
    const response = await fetch('spotify_status.php');
    const data = await response.json();

    const track = document.getElementById('spotify-track');
    const artist = document.getElementById('spotify-artist');
    const albumArt = document.getElementById('spotify-album-art');

    if (data.track && data.track !== 'Nothing playing right now') {
      track.textContent = data.track;
      artist.textContent = data.artist;
      albumArt.src = data.album_art;
      albumArt.classList.remove('hidden');
    } else {
      track.textContent = 'Nothing playing right now';
      artist.textContent = '';
      albumArt.classList.add('hidden');
    }
  } catch (err) {
    console.error('Spotify status error:', err);
    // Optionally show a message on failure:
    document.getElementById('spotify-track').textContent = 'Failed to load Spotify status';
    document.getElementById('spotify-artist').textContent = '';
    document.getElementById('spotify-album-art').classList.add('hidden');
  }
}

// Initial fetch + repeat every 10 seconds
fetchSpotifyStatus();
setInterval(fetchSpotifyStatus, 10000);
</script>

  <h1 class="text-5xl font-bold mb-4 text-black dark:text-white text-center">WELCOME! I'M CHARLES PURA</h1>

  <p class="text-lg mb-6 text-center max-w-2xl text-gray-800 dark:text-gray-300">
    Passionate about web-based systems, Android Studio applications, and more.
  </p>


  
  <!-- Robot instead of Image -->
  <div class="mt-8 relative z-10"> <!-- Added relative and z-10 -->
    <div class="relative w-48 h-80 mx-auto">
      <!-- Head -->
      <div class="absolute top-0 left-1/2 transform -translate-x-1/2 w-32 h-32 bg-blue-400 dark:bg-gray-600 rounded-2xl flex justify-center items-center shadow-md border-4 border-blue-700 dark:border-gray-500">
        <!-- Eyes -->
        <div class="flex space-x-6">
          <div class="w-4 h-4 bg-black dark:bg-white rounded-full eye"></div>
          <div class="w-4 h-4 bg-black dark:bg-white rounded-full eye"></div>
        </div>
        <!-- Antenna -->
        <div class="absolute -top-6 w-2 h-6 bg-red-500 mx-auto rounded-full"></div>
      </div>

      <!-- Body -->
      <div class="absolute top-28 left-1/2 transform -translate-x-1/2 w-36 h-40 bg-blue-500 dark:bg-gray-700 rounded-2xl shadow-lg border-4 border-blue-700 dark:border-gray-500">
        <!-- Buttons -->
        <div class="flex flex-col items-center justify-center h-full space-y-2">
          <div class="w-4 h-4 bg-yellow-400 rounded-full"></div>
          <div class="w-4 h-4 bg-red-400 rounded-full"></div>
          <div class="w-4 h-4 bg-green-400 rounded-full"></div>
        </div>
      </div>

      <!-- Arms -->
      <div class="absolute top-32 w-full flex justify-between">
        <div class="w-4 h-24 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
        <div class="w-4 h-24 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
      </div>

      <!-- Legs -->
      <div class="absolute top-72 w-full flex justify-center space-x-6">
        <div class="w-4 h-12 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
        <div class="w-4 h-12 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
      </div>
    </div>
  </div>

   <div id="cv-button-wrapper" class="transition-transform duration-300 ease-in-out">
    <a href="Charles Pura CV 2025.pdf" download 
       class="inline-flex items-center bg-gradient-to-r from-blue-600 to-blue-800 text-white font-semibold px-6 py-3 rounded-full shadow-lg hover:from-blue-700 hover:to-blue-900 transition duration-300">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
      </svg>
      Download Resume
    </a>
  </div>

 


</section>
 <!-- Scroll Down Animation -->
  <div class="absolute bottom-10 left-1/2 transform -translate-x-1/2">
    <div class="w-6 h-10 border-2 border-gray-700 dark:border-gray-300 rounded-full flex justify-center items-start overflow-hidden">
      <div class="w-2 h-2 bg-gray-700 dark:bg-gray-300 rounded-full animate-scroll-dot mt-1"></div>
    </div>
  </div>
<!-- CSS for eye follow effect -->
<style>
    .eye {
        position: relative;
        width: 2rem; /* increased size */
        height: 2rem; /* increased size */
        border-radius: 50%;
        background-color: #000;
        overflow: hidden;
        transform-origin: center;
        animation: blink 5s infinite;
    }

    .eye::before {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 1rem;
        height: 1rem;
        background-color: #fff;
        border-radius: 50%;
        transform: translate(calc(-50% + var(--x, 0)), calc(-50% + var(--y, 0)));
        transition: background-color 0.3s ease;
    }

    /* Dark mode: pupil turns black */
    .dark .eye::before {
        background-color: #000;
    }

    @keyframes blink {
        0%, 90%, 100% {
            transform: scaleY(1);
        }
        92%, 94% {
            transform: scaleY(0.1);
        }
    }
</style>

<!-- JS for eye follow cursor -->
<script>
  const eyes = document.querySelectorAll('.eye');
  document.addEventListener('mousemove', (e) => {
    eyes.forEach(eye => {
      const rect = eye.getBoundingClientRect();
      const eyeCenterX = rect.left + rect.width / 4;
      const eyeCenterY = rect.top + rect.height / 4;
      const deltaX = e.clientX - eyeCenterX;
      const deltaY = e.clientY - eyeCenterY;
      const angle = Math.atan2(deltaY, deltaX);
      const distance = Math.min(10, Math.hypot(deltaX, deltaY) / 15);

      const x = Math.cos(angle) * distance;
      const y = Math.sin(angle) * distance;

      // Move the pupil using custom properties for smoother animation
      eye.style.setProperty('--x', `${x}px`);
      eye.style.setProperty('--y', `${y}px`);
    });
  });

  // CV button animation
  const cvButton = document.getElementById('cv-button-wrapper');
  window.addEventListener('scroll', () => {
    const scrolled = window.scrollY;
    const maxScroll = 200;
    const maxTranslate = 50;
    const translateY = Math.min((scrolled / maxScroll) * maxTranslate, maxTranslate);
    cvButton.style.transform = `translateY(${translateY}px)`;
  });
</script>

<!-- Tailwind Animation for Scroll Dot -->
<style>
  @keyframes scroll-dot {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(6px); }
  }
  .animate-scroll-dot {
    animation: scroll-dot 1.5s infinite;
  }
</style>

<!-- Scroll animation style -->
<style>
  @keyframes scrollDot {
    0% {
      transform: translateY(0);
      opacity: 1;
    }
    50% {
      transform: translateY(1.5rem);
      opacity: 0.6;
    }
    100% {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .animate-scroll-dot {
    animation: scrollDot 1.2s infinite ease-in-out;
  }
</style>
