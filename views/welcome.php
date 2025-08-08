<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Dashboard</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <style>
            html {
    scroll-behavior: smooth;
    }

    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background-color: #f3f4f6;
    }

    .sidebar {
    width: 250px;
    background-color: #ffffff;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    transition: left 0.3s ease;
    z-index: 10;
    }


    .sidebar.hidden {
    left: -250px;
    }

    .toggle-btn {
    position: absolute;
    top: 50%;
    left: 250px;
    transform: translateY(-50%);
    z-index: 20;
    color: black;
    border: none;
    padding: 10px;
    border-radius: 50%;
    cursor: pointer;
    transition: left 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .sidebar.hidden + .toggle-btn-closed {
    display: block;
    }

    .toggle-btn-closed {
    position: fixed;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    z-index: 30;
    color: black;
    border: none;
    padding: 10px;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    display: none;
    }

    .sidebar-header {
        background-color: #b91c1c;
        color: white;
        padding: 35px;
        font-size: 13px;
        font-weight: bold;
        letter-spacing: 2px;
        text-align: center;
    
    }

    .sidebar ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar ul li a {
        display: block;
        padding: 12px 20px;
        color: #333;
        text-decoration: none;
        transition: background 0.2s;
    }

    .sidebar ul li a:hover {
        background-color: #f3f4f6;
    }

    .main {
        margin-left: 250px;
        padding: 0; /* Remove default padding */
        background-color: #ffffff;
        min-height: 100vh;
        transition: margin-left 0.3s ease;
    }

    .sidebar.hidden ~ .main {
        margin-left: 0;
    }

   .header-img {
    display: block;
    width: 100%;
    max-width: none;
    height: 270px; /* or your preferred height */
    object-fit: cover;
    border: none;
    box-shadow: none;
    margin: 0; /* Remove any margin */
    }

    .header-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .btn {
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-add {
        color: white;
        margin-right: 8px;
        border: none;
    }
    .btn-delete {
        background-color: #dc2626; /* red-600 */
        color: white;
        border: none;
    }

    .btn-delete:hover {
        background-color: #b91c1c;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    th, td {
        border: 1px solid #e5e7eb;
        padding: 10px;
        text-align: left;
        font-size: 14px;
    }

    th {
        background-color: #f9fafb;
    }

    .btn-option {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 13px;
        margin-right: 5px;
        border: none;
    }

    .btn-edit {
        background-color: #2563eb;
        color: white;
    }

    .btn-edit:hover {
        background-color: #1d4ed8;
    }

    .btn-end {
        background-color: #dc2626;
        color: white;
    }

    .btn-end:hover {
        background-color: #b91c1c;
    }

.signup-btn {
    position: absolute;
    top: 20px;
    right: 30px;
    background-color: transparent;
    color: white;
    border: 2px solid white;
    padding: 10px 22px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    z-index: 100;
    transition: background 0.2s, color 0.2s;
}

.home-logo {
    display: block;
    margin: 10px 0 20px 0;
    width: 200px; 
    max-width: 90vw; 
    height: 500px;
    }
.header-subtext {
    font-size: 3.2rem;
    font-weight: 800;
    color: black;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin: 30px 0 10px 0;
    text-align: center;
}

.header-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: black;
    margin-bottom: 30px;
    text-align: center;
    letter-spacing: 1px;
    padding-bottom: 80px;
}
.about-title {
    text-align: center;
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 18px;
    width: 100%;
}
.about-row {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    gap: 24px; /* space between sentences */
    margin-top: 20px;
    flex-wrap: wrap;
    padding-top: 120px;

}

.about-row p {
    font-family: 'Nunito', 'Segoe UI', Arial, sans-serif;
    font-size: 1.1rem;
    color: #222;
    max-width: 220px;
    text-align: left;
    margin: 0;
}

.chart-wrapper {
    display: flex;
    justify-content: center;
}

.about-safety{
    text-align: center;
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 18px;
    width: 100%;
    padding-top: 100px;

}
.chart-container {
    width: 100%;
    max-width: 700px;
    min-height: 300px;
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    resize: both;
    overflow: auto;
}

.filter-buttons {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-top: 15px;
}

.filter-buttons button {
    padding: 10px 18px;
    font-size: 16px;
    font-weight: bold;
    border: 1px solid;
    background-color: white;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.btn-critical {
    border-color: #ff4c4c;
    color: #ff4c4c;
}
.btn-critical:hover {
    background-color: #ff4c4c;
    color: white;
}
.btn-moderate {
    border-color: #ffa500;
    color: #ffa500;
}
.btn-moderate:hover {
    background-color: #ffa500;
    color: white;
}
.btn-safe {
    border-color: #4caf50;
    color: #4caf50;
}
.btn-safe:hover {
    background-color: #4caf50;
    color: white;
}

.safety h3 {
    text-align: center;
}
.map-container {
    display: flex;
    justify-content: center;
  width: 70%;
  height: 350px;
  transition: margin-left 0.3s ease;
  margin: 0 auto;
  display: block;
    padding-bottom: 200px;
}
.about-map {
    text-align: center;
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 18px;
    width: 100%;
    padding-top: 70px;
} 
.blur-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    background-color: rgba(0, 0, 0, 0.3);
    z-index: 999;
    display: none;
    justify-content: center;
    align-items: center;
  }

  #tipFormContainer {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 600px;
    z-index: 1000;
  }

  #tipForm input,
  #tipForm button {
    width: 100%;
    margin: 10px 0;
    padding: 12px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ccc;
  }

  #tipForm button[type="submit"] {
    background-color: #007bff;
    color: white;
    font-weight: bold;
    border: none;
    cursor: pointer;
  }

  #tipForm .cancel-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    font-weight: bold;
    cursor: pointer;
  }

  #tipForm button:hover {
    opacity: 0.9;
  }

  .about-safety {
    text-align: center;
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
  }

  .btn.btn-add {
    color: black;
  }

  #tipMessage {
    margin-top: 15px;
    color: green;
    text-align: center;
  }
.about-safety-tips {
    text-align: center;
    font-weight: bold;
    font-size: 1.5rem;
    margin-bottom: 18px;
    width: 100%;
    letter-spacing: 2px;
  }
  .safety-banner-container {
  max-width: 300px;
  margin: 40px auto;
  overflow: hidden;
  align-items: center;
  padding-top:100px;
}

.safety-banner {
  display: flex;
  transition: transform 0.6s ease-in-out;
}

.slide {
  min-width: 100%;
  display: none;
  justify-content: center;
  align-items: center;
  background: #fff;
}

.slide img {
  width: 100%;
  height: auto;
  border-radius: 0;
}

.slide.active {
  display: flex;
}

.controls {
  text-align: center;
  margin-top: 10px;
  padding-bottom: 50px;
}

.controls button {
  color: black;
  border: none;
  padding: 10px 16px;
  font-size: 16px;
  cursor: pointer;
  border-radius: 5px;
  margin: 0 5px;
}
.bottom-section {
    background-color: blue;
    padding: 50px 20px;
    display: flex;
    justify-content: space-between; /* spread left and right */
    align-items: center;
    flex-wrap: wrap; /* allow wrapping on smaller screens */
    color: white;
}

.show-feedback-btn {
    font-size: 1.8rem;
    font-family: serif;
    background: none;
    border: none;
    color: white;
    cursor: pointer;
    font-weight: bold;
    transition: color 0.3s ease;
}

.show-feedback-btn:hover {
    color: #007bff;
}

.emergency-contact {
    text-align: right;
    font-family: Arial, sans-serif;
    font-size: 14px;
}


.feedback-blur-bg {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    backdrop-filter: blur(5px);
    background: rgba(0,0,0,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.feedback-container {
    background: #e4eaf3; /* Updated background color to match the image */
    padding: 30px;
    border-radius: 12px;
    width: 90%;
    max-width: 450px; /* Reduced max-width for a more compact look */
    box-shadow: 0 0 15px rgba(0,0,0,0.2);
    max-height: 90vh;
    overflow-y: auto;
    text-align: center; /* Center-align all text content */
}

.feedback-title {
    color: #333;
    font-size: 24px;
    margin-bottom: 5px; /* Adjust spacing */
}

.feedback-text-small {
    color: #555;
    font-size: 14px;
    margin: 5px 0 15px;
}

.rating-stars {
    margin: 10px 0 25px;
}

.rating-stars .fa-star {
    font-size: 30px;
    color: #ccc; /* Inactive star color */
    cursor: pointer;
    transition: color 0.2s;
}

.rating-stars .fa-star:hover,
.rating-stars .fa-star.active {
    color: #ffc107; /* Gold color for active stars */
}

.feedback-container textarea {
    width: 100%;
    padding: 15px;
    margin: 10px 0 20px;
    border: 1px solid #ccc;
    border-radius: 8px;
    resize: vertical;
    box-sizing: border-box;
    font-size: 16px;
}

.feedback-actions {
    display: flex;
    flex-direction: column; /* Stack buttons vertically */
    gap: 10px;
}

.feedback-actions button {
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    width: 100%; /* Full width buttons */
}

.submit-button {
    background: #2e4d77; /* Darker blue to match image */
    color: white;
}

.submit-button:hover {
    background: #233e5c;
}

.cancel-button {
    background: #6c757d;
    color: white;
}

.cancel-button:hover {
    background: #5a6268;
}

.speech-bubble-welcome {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 200px;
    max-width: 280px;
    padding: 14px 22px;
    background: #eacc23ef;
    font-size: 1rem;
    color: #222;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-weight: 600;
    z-index: 2000;
    box-shadow: 0 4px 18px rgba(0,0,0,0.10);
    display: flex;
    align-items: center;
    height: 40px;
}

</style>

<div class="sidebar" id="sidebar">          
    <button id="toggleSidebar" class="toggle-btn">
        <i class="fa-solid fa-angle-left"></i>
    </button>
    <div class="sidebar-header">Barangay.Commonwealth</div>
    <ul>
    <li><a href="#home">HOME</a></li>
    <li><a href="#about">ABOUT</a></li>
    <li><a href="#safety-status">SAFETY STATUS</a></li>
    <li><a href="#tip">TIP</a></li>
    <li><a href="#map">MAP</a></li>
    <li><a href="#safety-tips">SAFETY TIPS</a></li>
    <li><a href="#feedback">FEEDBACK</a></li>
</ul>
</div>
<button id="toggleSidebarClosed" class="toggle-btn-closed" style="display:none;">
    <i class="fa-solid fa-angle-right"></i>
</button>

<body><div class="main">
    <img src="images/baramgay.jpg" alt="Header Image" class="header-img" id="home">
    <div id="home" class="section">
        <div class="header-subtext">
            WELCOME TO BARANGAY COMMONWEALTH!
        </div>
        <div class="header-text">
            28 Commonwealth Ave, Katuparan, Quezon City, 1121 Metro Manila
        </div>
    </div>
    <div id="about" class="about-row">
    <div class="about-title">ABOUT US</div>
    <p>Barangay Commonwealth is a progressive and community-driven barangay in Quezon City committed to ensuring the safety, well-being, and empowerment of its residents.</p>
    <p>With a focus on transparency, innovation, and community engagement, we aim to create a harmonious and secure environment for all.</p>
    <p>Our mission is to enhance the quality of life for our constituents through effective governance, responsive services, and active participation in community development.</p>
    <p>Through innovative programs and modern surveillance technologies, we strive to maintain peace and order, promote transparency, and foster strong partnerships between citizens and local authorities.</p>
    <p>Guided by unity and service, Barangay Commonwealth remains dedicated to building a secure and resilient community for all.</p>
    </div>

<div id="safety-status" class="safety">
    <h1 class="about-safety">SAFETY STATUS</h1>
    <h3>Safety Status Overview (2000–2025)</h3>
    <div class="chart-wrapper">
        <div class="chart-container">
            <canvas id="safetyBarChart"></canvas>
        </div>
    </div>
    <div class="filter-buttons">
        <button class="btn-critical" onclick="showOnlyDataset(0)">Critical Years</button>
        <button class="btn-moderate" onclick="showOnlyDataset(1)">Moderate Safety</button>
        <button class="btn-safe" onclick="showOnlyDataset(2)">Safe Years</button>
    </div>
</div>
<div id="tip" class="section">
  <h1 class="about-safety"></h1>

  <div style="text-align: center; margin-bottom: 20px;">
    <button id="showTipForm" class="btn btn-add">Report a Tip?</button>
  </div>

  <!-- Blurred overlay and modal form -->
  <div id="blurOverlay" class="blur-overlay">
    <div id="tipFormContainer">
      <form id="tipForm">
        <input type="text" id="name" placeholder="Your name (optional)">
        <input type="file" id="picture" accept="image/*" required>
        <input type="file" id="video" accept="video/*" required>
        <input type="text" id="address" placeholder="Enter address or location" required>
        <button type="submit">Submit Tip</button>
        <button type="button" class="cancel-btn" id="cancelTipForm">Cancel</button>
      </form>
      <p id="tipMessage"></p>
    </div>
  </div>
</div>

<div id="map" class="map-container">
    <div style="padding: 20px;">
        <h1 class="about-map">MAP</h1>
        <h2 style="font-size: 20px; font-weight: bold; margin-bottom: 10px; text-align: center;">Barangay Commonwealth Map</h2>
        <div id="googleMap" style="width: 100%; height: 400px;"></div>
    </div>
    </div>
    <div id="safety-tips" class ="tips">
        <div class="safety-banner-container">
            <h1 class="about-safety-tips">SAFETY TIPS FROM YOUR BARANGAY </h1>
  <div class="safety-banner" id="safetyBanner">
    
    <div class="slide active">
      <img src="images/emergency.png" alt="Stay Alert in Public">
    </div>
    <div class="slide">
      <img src="images/alert.png" alt="Lock Doors and Windows">
    </div>
    <div class="slide">
      <img src="images/value.png" alt="Keep Emergency Kit">
    </div>
    <div class="slide">
      <img src="images/participate.png" alt="Emergency Contacts">
    </div>
    <div class="slide">
      <img src="images/prepare.png" alt="Community Watch">
    </div>
  </div>

  <div class="controls">
    <button onclick="prevSlide()">❮</button>
    <button onclick="nextSlide()">❯</button>
  </div>
    </div>
<div id="feedback" class="bottom-section">
    <button id="showFeedbackBtn" class="show-feedback-btn">SHARE YOUR FEEDBACK?</button>
    <div class="emergency-contact">
  <p><strong>EMERGENCY HOTLINE:</strong> 932-7552</p>
  <p><strong>CONTACT US:</strong> 8-283-9695 | 8-932-2395 | 427-9210</p>
    </div>
    <div id="feedbackSection" class="feedback-blur-bg" style="display: none;">
        <div class="feedback-container">
            <h2 class="feedback-title">We value your opinion.</h2>
            <p class="feedback-text-small">How would you rate your overall experience?</p>
            <div class="rating-stars">
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
                <span class="fa fa-star"></span>
            </div>
            <p class="feedback-text-small">Kindly take a moment to tell us what you think.</p>
            <form id="feedbackForm" method="POST" action="#" enctype="multipart/form-data">
                <input type="hidden" name="rating" id="ratingValue" value="0">

                <textarea name="message" rows="5" placeholder="" required></textarea>

                <div class="feedback-actions">
                    <button type="submit" class="submit-button">Share my feedback</button>
                    <button type="button" id="cancelFeedbackButton" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>


</div>
</div>
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>      
</div>
<div class="speech-bubble-welcome" id="welcomeBubble">
    Welcome, Citizen of CommonWealth!
</div>
<a href="login" class="signup-btn">Login</a>
<script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const toggleBtnClosed = document.getElementById('toggleSidebarClosed');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.add('hidden');
        toggleBtn.style.display = 'none';
        toggleBtnClosed.style.display = 'block';
    });

    toggleBtnClosed.addEventListener('click', () => {
        sidebar.classList.remove('hidden');
        toggleBtn.style.display = 'block';
        toggleBtnClosed.style.display = 'none';
    });
</script>
<script>
    const ctx = document.getElementById('safetyBarChart').getContext('2d');

    const datasets = [
        {
            label: 'Critical',
            data: [8, 6, 4, 3, 2, 1],
            backgroundColor: 'rgba(255, 76, 76, 0.8)',
            hidden: false
        },
        {
            label: 'Moderate',
            data: [5, 6, 7, 6, 4, 3],
            backgroundColor: 'rgba(255, 165, 0, 0.8)',
            hidden: true
        },
        {
            label: 'Safe',
            data: [2, 3, 5, 8, 10, 12],
            backgroundColor: 'rgba(76, 175, 80, 0.8)',
            hidden: true
        }
    ];

    const safetyBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [2000, 2005, 2010, 2015, 2020, 2025],
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    title: {
                        display: true,
                        text: 'Year'
                    }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Incidents'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    function showOnlyDataset(indexToShow) {
        safetyBarChart.data.datasets.forEach((ds, index) => {
            ds.hidden = index !== indexToShow;
        });
        safetyBarChart.update();
    }
</script>
<script>
let map; // global
const commonwealth = { lat: 14.6971, lng: 121.0877 };
function initMap() {
  map = new google.maps.Map(document.getElementById("googleMap"), {
    zoom: 15,
    center: commonwealth,
  });

  new google.maps.Marker({
    position: commonwealth,
    map: map,
    title: "Barangay Commonwealth",
  });
}
</script>
<script async defer
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBWPzFBvajQU6B85oLrwFFlSXkz9UcAkZk&callback=initMap">
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggleBtn = document.getElementById('toggleSidebar');
  const toggleBtnClosed = document.getElementById('toggleSidebarClosed');
  const sidebar = document.getElementById('sidebar');

  toggleBtn.addEventListener('click', () => {
    sidebar.classList.add('hidden');
    toggleBtn.style.display = 'none';
    toggleBtnClosed.style.display = 'block';

    setTimeout(() => {
      google.maps.event.trigger(map, 'resize');
      map.setCenter(commonwealth);
    }, 350);
  });

  toggleBtnClosed.addEventListener('click', () => {
    sidebar.classList.remove('hidden');
    toggleBtn.style.display = 'block';
    toggleBtnClosed.style.display = 'none';

    setTimeout(() => {
      google.maps.event.trigger(map, 'resize');
      map.setCenter(commonwealth);
    }, 350);
  });
});
</script>
<script>
  const showTipFormBtn = document.getElementById("showTipForm");
  const blurOverlay = document.getElementById("blurOverlay");
  const tipForm = document.getElementById("tipForm");
  const tipMessage = document.getElementById("tipMessage");
  const cancelBtn = document.getElementById("cancelTipForm");

  showTipFormBtn.addEventListener("click", () => {
    blurOverlay.style.display = "flex";
  });

  cancelBtn.addEventListener("click", () => {
    blurOverlay.style.display = "none";
    tipForm.reset();
    tipMessage.textContent = "";
  });

  tipForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const picture = document.getElementById("picture").files[0];
    const video = document.getElementById("video").files[0];
    const address = document.getElementById("address").value;

    if (!picture || !video || address.trim() === "") {
      alert("Please fill in all required fields.");
      return;
    }

    tipMessage.textContent = "Tip submitted successfully!";
    tipForm.reset();

    setTimeout(() => {
      blurOverlay.style.display = "none";
      tipMessage.textContent = "";
    }, 2000);
  });
</script>
<script>
  let currentSlide = 0;
  const slides = document.querySelectorAll(".slide");

  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.remove("active");
      if (i === index) {
        slide.classList.add("active");
      }
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
  }

  // Optional auto-play
  setInterval(nextSlide, 6000);
</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const showBtn = document.getElementById('showFeedbackBtn');
    const cancelBtn = document.getElementById('cancelFeedbackButton');
    const feedbackSection = document.getElementById('feedbackSection');
    const stars = document.querySelectorAll('.rating-stars .fa-star');
    const ratingInput = document.getElementById('ratingValue');

    // Show feedback form
    if (showBtn && feedbackSection) {
        showBtn.addEventListener('click', () => {
            feedbackSection.style.display = 'flex';
        });
    }

    // Cancel feedback form
    if (cancelBtn && feedbackSection) {
        cancelBtn.addEventListener('click', () => {
            feedbackSection.style.display = 'none';
        });
    }

    // Star rating logic
    stars.forEach((star, index) => {
        star.addEventListener('click', () => {
            // Clear previous active states
            stars.forEach(s => s.classList.remove('active'));

            // Activate stars up to the clicked one
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('active');
            }

            // Set hidden input value
            ratingInput.value = index + 1;
        });
    });
});
</script>
<script>
setTimeout(() => {
    document.getElementById('welcomeBubble').style.display = 'none';
}, 5000);
</script>


</body>
</html>