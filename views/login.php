
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: url('images/images (1).jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(8px);
        }
        .login-container {
            background: rgba(255,255,255,0.12);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.37);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.18);
            width: 350px;
            max-width: 90vw;
            margin: 60px auto;
            padding: 32px 28px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .login-container h2 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 24px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .input-group {
            width: 100%;
            margin-bottom: 18px;
            position: relative;
        }
        .input-group input {
            width: 100%;
            padding: 12px 40px 12px 16px;
            border-radius: 8px;
            border: none;
            background: rgba(255,255,255,0.25);
            color: #222;
            font-size: 1rem;
            outline: none;
        }
        .input-group .icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
            font-size: 1.2rem;
        }
        .options {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            font-size: 0.95rem;
            color: #fff;
        }
        .options label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .options a {
            color: #fff;
            text-decoration: underline;
            font-size: 0.95rem;
        }
        .login-btn {
            width: 100%;
            padding: 12px;
            border-radius: 24px;
            border: none;
            background: #fff;
            color: #cfcdbdff;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 12px;
            transition: background 0.2s;
        }
        .login-btn:hover {
            background: #e0e0e0;
        }
        .signup-link {
            color: #fff;
            font-size: 1rem;
            text-align: center;
        }
        .signup-link a {
            color: #fff;
            font-weight: bold;
            text-decoration: underline;
        }
        .error {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
        .errors {
            color: #ff6b6b;
            background: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .errors ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <form action="process_login.php" method="post" id="loginForm">
            <div class="input-group">
                <input type="email" name="email" placeholder="Email" required>
                <span class="icon"><i class="fa fa-user"></i></span>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
                <span class="icon"><i class="fa fa-lock"></i></span>
            </div>
            
            <!-- User Type Selection -->
            <div class="input-group">
                <select name="user_type" id="userType" style="width: 100%; padding: 12px 40px 12px 16px; border-radius: 8px; border: none; background: rgba(255,255,255,0.25); color: #222; font-size: 1rem; outline: none; text-align: center;" required>
                    <option value="">-- Select User Type --</option>
                    <option value="super_admin">Super Administrator</option>
                    <option value="sec_admin">Secretary Administrator</option>
                    <option value="admin">Administrator</option>
                </select>
                <span class="icon"><i class="fa fa-users"></i></span>
            </div>
            
            <!-- Street Selection (initially hidden) -->
            <div class="input-group" id="streetGroup" style="display: none;">
                <select name="street" id="street" style="width: 100%; padding: 12px 40px 12px 16px; border-radius: 8px; border: none; background: rgba(255,255,255,0.25); color: #222; font-size: 1rem; outline: none; text-align: center;">
                    <option value="">-- Choose your street --</option>
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
                <span class="icon"><i class="fa fa-map-marker"></i></span>
            </div>
            
            <div class="options">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                <a href="#">Forgot Password</a>
            </div>
            <input type="submit" name="login" value="Login" class="login-btn">
        </form>
        <div class="signup-link">
            <p style="margin: 0; color: #fff; font-size: 0.9rem;">Administrator Access Only</p>
        </div>

    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <script>
        // Get form elements
        const userTypeSelect = document.getElementById('userType');
        const streetGroup = document.getElementById('streetGroup');
        const streetSelect = document.getElementById('street');
        const loginForm = document.getElementById('loginForm');
        
        // Function to toggle street selection visibility
        function toggleStreetSelection() {
            const selectedUserType = userTypeSelect.value;
            
            if (selectedUserType === 'admin') {
                streetGroup.style.display = 'block';
                streetSelect.required = true;
            } else {
                streetGroup.style.display = 'none';
                streetSelect.required = false;
                streetSelect.value = ''; // Clear the selection
            }
        }
        
        // Add event listener to user type select
        userTypeSelect.addEventListener('change', toggleStreetSelection);
        
        // Form validation
        loginForm.addEventListener('submit', function(e) {
            const selectedUserType = userTypeSelect.value;
            
            // If admin is selected, street is required
            if (selectedUserType === 'admin' && !streetSelect.value) {
                e.preventDefault();
                alert('Please select a street for Administrator login.');
                return false;
            }
            
            // If no user type is selected
            if (!selectedUserType) {
                e.preventDefault();
                alert('Please select a user type.');
                return false;
            }
        });
        
        // Initialize on page load
        toggleStreetSelection();
    </script>
</body>
</html> 