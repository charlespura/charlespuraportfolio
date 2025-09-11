


<!-- Spotify Now Playing - Left middle corner -->
<section id="hero" class="relative h-screen flex flex-col justify-center items-center pt-25 bg-[#f5f5f5] dark:bg-gray-800 transition-colors duration-500 px-6 md:px-12">



<!-- Mobile rectangular widget -->
<a
  id="spotify-link-mobile"
  href="#"
  target="_blank"
  class="absolute top-1/2 left-4 w-24 h-56 rounded-md overflow-hidden shadow-lg bg-white dark:bg-gray-700 flex flex-col items-center justify-start md:hidden transform -translate-y-1/2 p-2"
>
  <img
    id="spotify-album-art-mobile"
    src=""
    alt="Album Art"
    class="w-full h-28 object-cover mb-2 rounded"
  />
  
  <p id="spotify-track-mobile" class="text-xs font-bold text-center truncate w-full text-black dark:text-white">Loading...</p>
  <p id="spotify-artist-mobile" class="text-xs text-center truncate w-full text-gray-700 dark:text-gray-300"></p>
  
  <!-- Mobile Progress Bar --><div class="relative w-full h-1 rounded-full overflow-hidden bg-gray-300 dark:bg-gray-700 mt-2">
  <div
    id="progress-bar-mobile"
    class="absolute top-0 left-0 h-full bg-[#1DB954] transition-all duration-300"
    style="width: 0%"
  ></div>
</div>


  <p id="progress-time-mobile" class="text-[10px] text-center text-gray-700 dark:text-gray-300 mt-1"></p>

  <img
    id="spotify-logo-mobile"
    src="https://upload.wikimedia.org/wikipedia/commons/8/84/Spotify_icon.svg"
    alt="Spotify Logo"
    class="w-6 h-6 mt-2"
  />
  
</a>

<!-- Desktop widget as a more defined box -->
<a
  id="spotify-link"
  href="#"
  target="_blank"
  class="absolute top-1/2 left-4 w-96 max-w-full rounded-xl overflow-hidden shadow-xl transform -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 p-4 hidden md:flex flex-col space-y-2 text-gray-700 dark:text-gray-300"
>
Developer Listening to Spotify
  <!-- Progress Bar -->
  <div class="relative w-full h-1 rounded-full overflow-hidden bg-gray-300 dark:bg-gray-700 mt-2">
    <div
      id="progress-bar"
      class="absolute top-0 left-0 h-full bg-[#1DB954] transition-all duration-300"
      style="width: 0%"
    ></div>
  </div>

  <!-- Time Display -->
  <p id="progress-time" class="text-xs"></p>

  <div class="flex items-center space-x-4">
    <img
      src="https://upload.wikimedia.org/wikipedia/commons/8/84/Spotify_icon.svg"
      alt="Spotify Logo"
      class="w-8 h-8 flex-shrink-0"
    />
    <img
      id="spotify-album-art"
      src=""
      alt="Album Art"
      class="w-32 h-32 rounded-md hidden"
    />
    <div class="flex flex-col overflow-hidden">
      <p id="spotify-track" class="font-bold truncate">Loading...</p>
      <p id="spotify-artist" class="text-sm truncate"></p>
    </div>
  </div>
</a>
<script>
  let currentProgress = 0;
  let duration = 0;
  let isPlaying = false;

  // Function to get dominant color from an image
  function getDominantColor(imageUrl, callback) {
    const img = new Image();
    img.crossOrigin = 'Anonymous'; // Needed for cross-origin images
    img.src = imageUrl;
    img.onload = () => {
      const canvas = document.createElement('canvas');
      canvas.width = img.width;
      canvas.height = img.height;
      const ctx = canvas.getContext('2d');
      ctx.drawImage(img, 0, 0);
      const data = ctx.getImageData(0, 0, img.width, img.height).data;

      let r = 0, g = 0, b = 0, count = 0;
      for (let i = 0; i < data.length; i += 4) {
        r += data[i];
        g += data[i + 1];
        b += data[i + 2];
        count++;
      }

      r = Math.floor(r / count);
      g = Math.floor(g / count);
      b = Math.floor(b / count);

      callback(`rgb(${r},${g},${b})`);
    };
  }

  async function fetchSpotifyStatus() {
    try {
      const response = await fetch('spotify-status.php');
      const data = await response.json();

      const track = document.getElementById('spotify-track');
      const artist = document.getElementById('spotify-artist');
      const albumArt = document.getElementById('spotify-album-art');
      const link = document.getElementById('spotify-link');

      // Mobile elements
      const mobileLink = document.getElementById('spotify-link-mobile');
      const mobileAlbumArt = document.getElementById('spotify-album-art-mobile');
      const mobileLogo = document.getElementById('spotify-logo-mobile');
      const mobileTrack = document.getElementById('spotify-track-mobile');
      const mobileArtist = document.getElementById('spotify-artist-mobile');

      if (data.track && data.track !== 'Nothing playing right now') {
        // Desktop
        track.textContent = data.track + " (Listening now)";
        artist.textContent = data.artist;
        albumArt.src = data.album_art;
        albumArt.classList.remove('hidden');
        link.classList.remove('hidden');

        // Mobile
        mobileAlbumArt.src = data.album_art;
        mobileAlbumArt.classList.remove('hidden');
        mobileTrack.textContent = data.track;
        mobileArtist.textContent = data.artist;
        mobileLink.classList.remove('hidden');

        if (data.url) {
          link.href = data.url;
          link.target = '_blank';
          link.classList.remove('pointer-events-none');

          mobileLink.href = data.url;
          mobileLink.target = '_blank';
          mobileLink.classList.remove('pointer-events-none');
        } else {
          link.removeAttribute('href');
          link.classList.add('pointer-events-none');

          mobileLink.removeAttribute('href');
          mobileLink.classList.add('pointer-events-none');
        }

        // **Dynamic background based on album art**
        getDominantColor(data.album_art, (color) => {
          link.style.background = color;        // Desktop
          mobileLink.style.background = color;  // Mobile
        });

        // Save progress and duration for smooth animation
        if (data.progress_ms && data.duration_ms) {
          currentProgress = data.progress_ms;
          duration = data.duration_ms;
          isPlaying = true;
        } else {
          isPlaying = false;
        }
      } else {
        // Not listening
        track.textContent = "Not listening";
        artist.textContent = "";
        albumArt.classList.add('hidden');
        document.getElementById('progress-bar').style.width = `0%`;
        document.getElementById('progress-time').textContent = '';

        // Make both desktop and mobile links unclickable when not playing
        link.removeAttribute('href');
        link.classList.add('pointer-events-none');

        mobileLink.removeAttribute('href');
        mobileLink.classList.add('pointer-events-none');

        mobileAlbumArt.classList.add('hidden');
        mobileLogo.classList.remove('hidden');
        mobileTrack.textContent = '';
        mobileArtist.textContent = '';

        isPlaying = false;
      }
    } catch (err) {
      console.error('Spotify status error:', err);
      document.getElementById('spotify-link').classList.add('hidden');
      document.getElementById('spotify-link-mobile').classList.add('hidden');
      isPlaying = false;
    }
  }

  setInterval(() => {
    if (isPlaying && duration > 0) {
      currentProgress += 100; // 100ms increment
      if (currentProgress > duration) {
        currentProgress = duration;
      }

      const percent = (currentProgress / duration) * 100;

      // Update desktop progress bar
      document.getElementById('progress-bar').style.width = `${percent}%`;

      // Update mobile progress bar
      document.getElementById('progress-bar-mobile').style.width = `${percent}%`;

      const formatTime = (ms) => {
        const totalSeconds = Math.floor(ms / 1000);
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        return `${minutes}:${seconds.toString().padStart(2, '0')}`;
      };

      const formattedTime = `${formatTime(currentProgress)} / ${formatTime(duration)}`;

      // Update desktop time
      document.getElementById('progress-time').textContent = formattedTime;

      // Update mobile time
      document.getElementById('progress-time-mobile').textContent = formattedTime;
    }
  }, 100);

  // Fetch data every 10 seconds
  fetchSpotifyStatus();
  setInterval(fetchSpotifyStatus, 10000);
</script>


<script>
  function toggleSpotifyViews() {
    const mobile = document.getElementById('spotify-link-mobile');
    const desktop = document.getElementById('spotify-link');
    if (window.innerWidth < 768) {
      // Mobile screen
      mobile.style.display = 'flex';
      desktop.style.display = 'none';
    } else {
      // Desktop screen
      mobile.style.display = 'none';
      desktop.style.display = 'flex';
    }
  }

  window.addEventListener('resize', toggleSpotifyViews);
  window.addEventListener('load', toggleSpotifyViews);
</script>




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

  <style>
  .scramble-text {
    font-family: monospace;
    white-space: pre-wrap;
    word-break: break-word;
  }
</style>

<h1 id="scramble" class="text-xl sm:text-2xl md:text-3xl lg:text-5xl font-bold text-center scramble-text px-2"></h1>

<script>
  const element = document.getElementById('scramble');
  const fullText = "WELCOME! I'M CHARLES PURA";
  const splitIndex = fullText.indexOf("CHARLES PURA");
  const chars = "!@#$%^&*()_+-=[]{}|;:',.<>?/ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  let frame = 0;

  function scrambleText(text, iterations = 55, delay = 55) {
    const length = text.length;
    const original = text.split("");
    const output = new Array(length).fill("");

    const interval = setInterval(() => {
      for (let i = 0; i < length; i++) {
        if (frame > iterations / length * i) {
          output[i] = original[i];
        } else {
          output[i] = chars[Math.floor(Math.random() * chars.length)];
        }
      }

      const firstPart = output.slice(0, splitIndex).join("");
      const secondPart = output.slice(splitIndex).join("");

      element.innerHTML = `${firstPart}<span class="ml-1 text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">${secondPart}</span>`;

      frame++;

      if (frame > iterations) {
        clearInterval(interval);
        element.innerHTML = `WELCOME! I'M <span class="ml-1 text-blue-600 dark:text-blue-400 hover:underline whitespace-nowrap">CHARLES PURA</span>`;
      }
    }, delay);
  }

  setTimeout(() => {
    scrambleText(fullText, 55, 55);
  }, 500);
</script>

  <p class="text-lg mb-6 text-center max-w-2xl text-gray-800 dark:text-gray-300">
    Passionate about web-based systems, Android Studio applications, and more.
  <!-- Robot instead of Image -->
  <div class="mt-8 relative z-10"> <!-- fades robot on mobile -->

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
      <div class="flex flex-col items-center justify-center h-full space-y-2 z-20">
     <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 tracking-wide">Connect with Me</p>

        <!-- Facebook -->
        <a href="https://web.facebook.com/charlespuracp" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="pointer-events-auto text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-600 transition">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M22 12a10 10 0 10-11.5 9.87v-6.98H8v-2.9h2.5V9.4c0-2.48 1.49-3.86 3.77-3.86 1.09 0 2.23.2 2.23.2v2.46h-1.25c-1.23 0-1.62.77-1.62 1.56v1.87h2.77l-.44 2.9h-2.33v6.98A10 10 0 0022 12z"/>
        </svg>
        </a>

        <!-- GitHub -->
        <a href="https://github.com/charlespura" target="_blank" rel="noopener noreferrer" aria-label="GitHub" class="pointer-events-auto text-gray-800 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.166 6.84 9.49.5.09.68-.217.68-.483 0-.237-.008-.868-.013-1.703-2.78.603-3.37-1.342-3.37-1.342-.454-1.15-1.11-1.457-1.11-1.457-.908-.62.069-.608.069-.608 1.004.07 1.532 1.032 1.532 1.032.892 1.528 2.34 1.087 2.91.832.09-.647.35-1.088.636-1.34-2.22-.252-4.555-1.112-4.555-4.945 0-1.092.39-1.984 1.03-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.338 1.91-1.296 2.75-1.026 2.75-1.026.546 1.377.202 2.394.1 2.647.64.7 1.028 1.59 1.028 2.683 0 3.842-2.337 4.688-4.566 4.937.36.31.68.92.68 1.85 0 1.335-.012 2.414-.012 2.74 0 .268.18.58.69.48A10.007 10.007 0 0022 12c0-5.52-4.48-10-10-10z"/>
        </svg>
        </a>

        <!-- Email -->
        <a href="mailto:charles051902pura@gmail.com" aria-label="Email" class="pointer-events-auto text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-600 transition">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M20 4H4c-1.1 0-2 .9-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
        </svg>
        </a>
      </div>
      </div>

      <!-- Arms -->
      <div class="absolute top-32 w-full flex justify-between pointer-events-none">
      <div class="w-4 h-24 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
      <div class="w-4 h-24 bg-blue-500 dark:bg-gray-600 rounded-full"></div>
      </div>

      <!-- Legs -->
      <div class="absolute top-72 w-full flex justify-center space-x-6 pointer-events-none">
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

<script src="https://unpkg.com/color-thief-browser/dist/color-thief.umd.js"></script>

