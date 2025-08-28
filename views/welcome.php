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
    padding: 20px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    width: 90%;
    max-width: 500px;
    z-index: 1000;
    margin: auto;
  }

  #tipForm input,
  #tipForm button {
    width: 100%;
    margin: 10px 0;
    padding: 12px;
    font-size: 16px;
    border-radius: 8px;
    border: 1px solid #ccc;
    box-sizing: border-box;
  }
  #tipForm input[type="file"] {
    padding: 6px;
    background: #f9f9f9;
    display: block;
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
     align-items: flex-end;
    justify-content: center;
    z-index: 9999;
     padding-bottom: 50px;
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
.custom-select {
  position: relative;
  font-family: Arial;
}

.custom-select select {
  display: none; /*hide original SELECT element: */
}

.select-selected {
  background-color: DodgerBlue;
}

/* Style the arrow inside the select element: */
.select-selected:after {
  position: absolute;
  content: "";
  top: 14px;
  right: 10px;
  width: 0;
  height: 0;
  border: 6px solid transparent;
  border-color: #fff transparent transparent transparent;
}

/* Point the arrow upwards when the select box is open (active): */
.select-selected.select-arrow-active:after {
  border-color: transparent transparent #fff transparent;
  top: 7px;
}

/* style the items (options), including the selected item: */
.select-items div,.select-selected {
  color: #ffffff;
  padding: 8px 16px;
  border: 1px solid transparent;
  border-color: transparent transparent rgba(0, 0, 0, 0.1) transparent;
  cursor: pointer;
}

/* Style items (options): */
.select-items {
  position: absolute;
  background-color: DodgerBlue;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 99;
  max-height: 220px; /* Set max height for scroll */
  overflow-y: auto;   /* Enable vertical scrollbar */
}

/* Hide the items when the select box is closed: */
.select-hide {
  display: none;
}

.select-items div:hover, .same-as-selected {
   background-color: rgba(0, 0, 0, 0.1);
 }

 /* Street Selection Overlay Styles */
 .street-selection-overlay {
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   backdrop-filter: blur(8px);
   -webkit-backdrop-filter: blur(8px);
   background-color: rgba(0, 0, 0, 0.5);
   z-index: 9999;
   display: flex;
   justify-content: center;
   align-items: center;
 }

   .street-selection-container {
    background: white;
    padding: 40px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    text-align: center;
    max-width: 500px;
    width: 90%;
  }

  .street-selection-container .custom-select {
    margin: 0 auto;
    display: block;
  }

 .street-selection-container h2 {
   color: #333;
   font-size: 28px;
   margin-bottom: 10px;
   font-weight: bold;
 }

 .street-selection-container p {
   color: #666;
   font-size: 16px;
   margin-bottom: 30px;
 }

 .continue-btn {
   background-color: #007bff;
   color: white;
   border: none;
   padding: 12px 30px;
   border-radius: 8px;
   font-size: 16px;
   font-weight: bold;
   cursor: pointer;
   margin-top: 20px;
   transition: background-color 0.3s ease;
 }

 .continue-btn:hover:not(:disabled) {
   background-color: #0056b3;
 }

 .continue-btn:disabled {
   background-color: #ccc;
   cursor: not-allowed;
 }

 /* Hide main content initially */
 .main {
   filter: blur(0px);
   transition: filter 0.3s ease;
 }

 .main.blurred {
   filter: blur(8px);
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


<div id="streetSelectionOverlay" class="street-selection-overlay">
  <div class="street-selection-container">
    <h2>Welcome to Barangay Commonwealth</h2>
    <p>Please select your street to continue:</p>
    <div class="custom-select" style="width:300px;">
      <select id="streetSelect">
        <option value="0">Select your street...</option>
        <option value="1">A. Bonifacio</option>
        <option value="2">Abelardo</option>
        <option value="3">Adarna ST</option>
        <option value="4">Aguinaldo</option>
        <option value="5">Apple St</option>
        <option value="6">Bacer St</option>
        <option value="7">Bach</option>
        <option value="8">Batasan Rd</option>
        <option value="9">Bato-Bato St</option>
        <option value="10">Beethoven</option>
        <option value="11">Bicoleyte</option>
        <option value="12">Brahms</option>
        <option value="13">Caridad</option>
        <option value="14">Chopin</option>
        <option value="15">Commonwealth Ave</option>
        <option value="16">Cuenco St</option>
        <option value="17">D. Carmencita</option>
        <option value="18">Dear St</option>
        <option value="19">Debussy</option>
        <option value="20">Don Benedicto</option>
        <option value="21">Don Desiderio Ave</option>
        <option value="22">Don Espejo Ave</option>
        <option value="23">Don Fabian</option>
        <option value="24">Don Jose Ave</option>
        <option value="25">Don Macario</option>
        <option value="26">Dona Adaucto</option>
        <option value="27">Dona Agnes</option>
        <option value="28">Dona Ana Candelaria</option>
        <option value="29">Dona Carmen Ave</option>
        <option value="30">Dona Cynthia</option>
        <option value="31">Dona Fabian Castillo</option>
        <option value="32">Dona Juliana</option>
        <option value="33">Dona Lucia</option>
        <option value="34">Dona Maria</option>
        <option value="35">Dona Severino</option>
        <option value="36">Ecol St</option>
        <option value="37">Elliptical Rd</option>
        <option value="38">Elma St</option>
        <option value="39">Ernestine</option>
        <option value="40">Ernestito</option>
        <option value="41">Eulogio St</option>
        <option value="42">Freedom Park</option>
        <option value="43">Gen. Evangelista</option>
        <option value="44">Gen. Ricarte</option>
        <option value="45">Geraldine St</option>
        <option value="46">Gold St</option>
        <option value="47">Grapes St</option>
        <option value="48">Handel</option>
        <option value="49">Hon. B. Soliven</option>
        <option value="50">Jasmin St</option>
        <option value="51">Johan St</option>
        <option value="52">John Street</option>
        <option value="53">Julius</option>
        <option value="54">June June</option>
        <option value="55">Kalapati St</option>
        <option value="56">Kamagong St</option>
        <option value="57">Kasoy St</option>
        <option value="58">Kasunduan</option>
        <option value="59">Katibayan St</option>
        <option value="60">Katipunan St</option>
        <option value="61">Katuparan</option>
        <option value="62">Kaunlaran</option>
        <option value="63">Kilyawan St</option>
        <option value="64">La Mesa Drive</option>
        <option value="65">Laurel St</option>
        <option value="66">Lawin St</option>
        <option value="67">Liszt</option>
        <option value="68">Lunas St</option>
        <option value="69">Ma Theresa</option>
        <option value="70">Mango</option>
        <option value="71">Manila Gravel Pit Rd</option>
        <option value="72">Mark Street</option>
        <option value="73">Markos Rd</option>
        <option value="74">Martan St</option>
        <option value="75">Martirez St</option>
        <option value="76">Matthew St</option>
        <option value="77">Melon</option>
        <option value="78">Mozart</option>
        <option value="79">Obanc St</option>
        <option value="80">Ocampo Ave</option>
        <option value="81">Odigal</option>
        <option value="82">Pacamara St</option>
        <option value="83">Pantaleona</option>
        <option value="84">Paul St</option>
        <option value="85">Payatas Rd</option>
        <option value="86">Perez St</option>
        <option value="87">Pilot Drive</option>
        <option value="88">Pineapple St</option>
        <option value="89">Pres. Osmena</option>
        <option value="90">Pres. Quezon</option>
        <option value="91">Pres. Roxas</option>
        <option value="92">Pugo St</option>
        <option value="93">Republic Ave</option>
        <option value="94">Riverside Ext</option>
        <option value="95">Riverside St</option>
        <option value="96">Rose St</option>
        <option value="97">Rossini</option>
        <option value="98">Saint Anthony Street</option>
        <option value="99">Saint Paul Street</option>
        <option value="100">San Andres St</option>
        <option value="101">San Diego St</option>
        <option value="102">San Miguel St</option>
        <option value="103">San Pascual</option>
        <option value="104">San Pedro</option>
        <option value="105">Sanchez St</option>
        <option value="106">Santo Nino Street</option>
        <option value="107">Santo Rosario Street</option>
        <option value="108">Schubert</option>
        <option value="109">Simon St</option>
        <option value="110">Skinita Shortcut</option>
        <option value="111">Steve St</option>
        <option value="112">Sto. Nino</option>
        <option value="113">Strauss</option>
        <option value="114">Sumapi Drive</option>
        <option value="115">Tabigo St</option>
        <option value="116">Thomas St</option>
        <option value="117">Verdi</option>
        <option value="118">Villonco</option>
        <option value="119">Wagner</option>
      </select>
    </div>
         <button id="continueBtn" class="continue-btn">Continue</button>
  </div>
</div>


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
        <input type="text" id="address" readonly style="text-align: center; font-weight: bold; font-size: 1.1rem; color: #bbbbbb9c; border: none; background: none; box-shadow: none;">
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
            <p id="feedbackStreet" style="font-size: 1.1rem; color: #555; font-weight: bold; margin-bottom: 2px; text-align: center;"></p>
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
                <input type="hidden" name="street" id="feedbackStreetHidden" value="">
                <textarea name="message" rows="5" placeholder="" required></textarea>
                <div id="feedbackMsg" style="text-align:center;color:#065f46;font-weight:600;margin-top:6px;"></div>
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
   <div class="speech-bubble-welcome" id="welcomeBubble" style="display: none;">
      Welcome, Citizen of Commonwealth!
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

  tipForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const picture = document.getElementById("picture").files[0];
    const video = document.getElementById("video").files[0];
    const address = document.getElementById("address").value;
    const name = (document.getElementById("name").value || '').trim();

    if (!picture || !video || address.trim() === "") {
      alert("Please fill in all required fields.");
      return;
    }

    const fd = new FormData();
    fd.append('action','submit_tip');
    fd.append('name', name);
    fd.append('address', address);
    fd.append('picture', picture);
    fd.append('video', video);

    tipMessage.textContent = "Submitting...";
    try {
      const res = await fetch('/capstone/views/api/notifications.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data && data.success) {
        tipMessage.textContent = "Tip submitted successfully!";
        tipForm.reset();
        setTimeout(() => { blurOverlay.style.display = "none"; tipMessage.textContent = ""; }, 1500);
      } else {
        tipMessage.textContent = (data && data.message) ? data.message : 'Submission failed';
      }
    } catch (err) {
      tipMessage.textContent = 'Network error';
    }
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

    const feedbackMsg = document.getElementById('feedbackMsg');
    const feedbackStreetHidden = document.getElementById('feedbackStreetHidden');

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

    // Handle feedback submit
    const feedbackForm = document.getElementById('feedbackForm');
    if (feedbackForm) {
        feedbackForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const rating = parseInt(document.getElementById('ratingValue').value || '0', 10);
            const messageEl = feedbackForm.querySelector('textarea[name="message"]');
            const streetVal = (document.getElementById('feedbackStreetHidden')?.value || '').trim();
            const msg = (messageEl?.value || '').trim();
            if (!rating || !msg) {
                alert('Please select a rating and enter your message.');
                return;
            }
            const fd = new FormData();
            fd.append('action', 'submit_feedback');
            fd.append('rating', String(rating));
            fd.append('message', msg);
            fd.append('street', streetVal);
            try {
                const res = await fetch('/capstone/views/api/notifications.php', { method: 'POST', body: fd });
                const data = await res.json();
                if (data && data.success) {
                    alert('Feedback success!');
                    // Reset stars
                    stars.forEach(s => s.classList.remove('active'));
                    ratingInput.value = '0';
                    if (messageEl) messageEl.value = '';
                    // Close overlay
                    const feedbackSection = document.getElementById('feedbackSection');
                    if (feedbackSection) feedbackSection.style.display = 'none';
                } else {
                    alert(data && data.message ? data.message : 'Submission failed');
                }
            } catch (err) {
                alert('Network error');
            }
        });
    }
});
</script>
 <script>
 // Street Selection Overlay Functionality
 document.addEventListener('DOMContentLoaded', function() {
     const streetSelect = document.getElementById('streetSelect');
     const continueBtn = document.getElementById('continueBtn');
     const streetSelectionOverlay = document.getElementById('streetSelectionOverlay');
     const mainContent = document.querySelector('.main');
     const welcomeBubble = document.getElementById('welcomeBubble');

     // Add blur class to main content initially
     mainContent.classList.add('blurred');

           // Reset street selection on page reload
      localStorage.removeItem('selectedStreet');
      localStorage.removeItem('selectedStreetName');
      
      // Always show the street selection overlay on page load
      streetSelectionOverlay.style.display = 'flex';
      mainContent.classList.add('blurred');

           // Enable/disable continue button based on selection
      streetSelect.addEventListener('change', function() {
          if (this.value !== '0') {
              continueBtn.disabled = false;
          } else {
              continueBtn.disabled = true;
          }
      });

             // Handle continue button click
       continueBtn.addEventListener('click', function() {
            const selectedValue = streetSelect.value;
            const selectedText = streetSelect.options[streetSelect.selectedIndex].text;

            if (selectedValue !== '0') {
                // Store the selection in localStorage
                localStorage.setItem('selectedStreet', selectedValue);
                localStorage.setItem('selectedStreetName', selectedText);

                // Hide overlay and show main content
                streetSelectionOverlay.style.display = 'none';
                mainContent.classList.remove('blurred');

                // Show and update welcome bubble with selected street
                if (welcomeBubble) {
                    welcomeBubble.textContent = `Welcome, Citizen of ${selectedText} in Commonwealth!`;
                    welcomeBubble.style.display = 'flex';

                    // Auto-hide welcome bubble after 5 seconds
                    setTimeout(() => {
                        welcomeBubble.style.display = 'none';
                    }, 5000);
                }

                // Auto-fill address/location in tip form
                const addressInput = document.getElementById('address');
        if (addressInput) {
            addressInput.value = `-- ${selectedText} --`;
        }

        // Set street name in feedback form
        const feedbackStreet = document.getElementById('feedbackStreet');
        if (feedbackStreet) {
            feedbackStreet.textContent = `-- from Citizen of ${selectedText} --`;
        }
        const feedbackStreetHidden = document.getElementById('feedbackStreetHidden');
        if (feedbackStreetHidden) {
            feedbackStreetHidden.value = selectedText;
        }
            } else {
                // If no street is selected, show default message
                if (welcomeBubble) {
                    welcomeBubble.textContent = `Welcome, Citizen of Commonwealth!`;
                    welcomeBubble.style.display = 'flex';

                    // Auto-hide welcome bubble after 5 seconds
                    setTimeout(() => {
                        welcomeBubble.style.display = 'none';
                    }, 5000);
                }
                // Clear street name in feedback form
                const feedbackStreet = document.getElementById('feedbackStreet');
                if (feedbackStreet) {
                    feedbackStreet.textContent = '';
                }
                const feedbackStreetHidden = document.getElementById('feedbackStreetHidden');
                if (feedbackStreetHidden) {
                    feedbackStreetHidden.value = '';
                }
            }
       });
 });
 </script>
<script>
    var x, i, j, l, ll, selElmnt, a, b, c;
/* Look for any elements with the class "custom-select": */
x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /* For each element, create a new DIV that will act as the selected item: */
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /* For each element, create a new DIV that will contain the option list: */
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    /* For each option in the original select element,
    create a new DIV that will act as an option item: */
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /* When an item is clicked, update the original select box,
        and the selected item: */
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);
  a.addEventListener("click", function(e) {
    /* When the select box is clicked, close any other select boxes,
    and open/close the current select box: */
    e.stopPropagation();
    closeAllSelect(this);
    this.nextSibling.classList.toggle("select-hide");
    this.classList.toggle("select-arrow-active");
  });
}

function closeAllSelect(elmnt) {
  /* A function that will close all select boxes in the document,
  except the current select box: */
  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}

/* If the user clicks anywhere outside the select box,
then close all select boxes: */
document.addEventListener("click", closeAllSelect);
</script>

</body>
</html>