<?php
// Start session for login state management
session_start();

// Define credentials
define('LOGIN_USERNAME', 'asbit');
define('LOGIN_PASSWORD', 'asb@123');

// Check if user is already logged in
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Handle login form submission
if (isset($_POST['login_submit'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username === LOGIN_USERNAME && $password === LOGIN_PASSWORD) {
        $_SESSION['logged_in'] = true;
        $isLoggedIn = true;
    } else {
        $login_error = "Invalid username or password. Please try again.";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// If not logged in, show login page
if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>ASB Loyalty Vault — Secure Access</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
    * { font-family: 'Inter', sans-serif; }
    body {
        background: linear-gradient(145deg, #fff5f5 0%, #ffffff 100%);
        min-height: 100vh;
    }
    .login-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 2rem;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        border: 1px solid rgba(220, 38, 38, 0.2);
    }
    .input-focus:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
        border-color: #dc2626;
        outline: none;
    }
    @keyframes gentlePulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        70% { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }
    .lock-icon {
        animation: gentlePulse 2s infinite;
    }
</style>
</head>
<body class="flex items-center justify-center p-5">
    <div class="max-w-md w-full">
        <!-- Logo/Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center gap-2 bg-red-50 rounded-full px-5 py-2 shadow-sm mb-4">
                <i class="fa-regular fa-star text-red-500 text-sm"></i>
                <span class="text-xs font-bold text-red-700 uppercase tracking-wide">ASB FASHION | ASB GLAMOUR</span>
                <i class="fa-regular fa-gem text-red-500 text-sm"></i>
            </div>
            <h1 class="text-4xl font-black bg-gradient-to-r from-red-800 via-red-600 to-red-500 bg-clip-text text-transparent">Loyalty Vault</h1>
            <p class="text-gray-500 text-sm mt-2">Secure access portal</p>
            <div class="w-20 h-1 bg-gradient-to-r from-red-400 to-red-600 mx-auto rounded-full mt-4"></div>
        </div>

        <!-- Login Card -->
        <div class="login-card p-8">
            <div class="flex justify-center mb-6">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center lock-icon">
                    <i class="fa-solid fa-lock text-3xl text-red-600"></i>
                </div>
            </div>
            
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Authentication Required</h2>
            <p class="text-center text-gray-500 text-sm mb-6">Please enter your credentials to continue</p>

            <?php if (isset($login_error)): ?>
            <div class="mb-5 bg-red-50 border-l-4 border-red-500 p-3 rounded-lg flex gap-3 items-start">
                <i class="fa-regular fa-circle-exclamation text-red-500 mt-0.5"></i>
                <p class="text-sm text-red-700"><?= htmlspecialchars($login_error) ?></p>
            </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        <i class="fa-regular fa-user mr-2 text-red-500"></i>Username
                    </label>
                    <input type="text" name="username" required 
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-400 transition-all input-focus"
                        placeholder="Enter username">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">
                        <i class="fa-regular fa-key mr-2 text-red-500"></i>Password
                    </label>
                    <input type="password" name="password" required 
                        class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-red-400 transition-all input-focus"
                        placeholder="Enter password">
                </div>
                <button type="submit" name="login_submit"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-200">
                    <i class="fa-regular fa-arrow-right-to-bracket"></i>
                    <span>Access Loyalty Vault</span>
                </button>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-400">
                    <i class="fa-regular fa-shield-haltered text-red-400 mr-1"></i>
                    Authorized personnel only
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-xs text-gray-400">
            <p>© 2025 ASB Fashion & Glamour — Secure Loyalty System</p>
            <p class="mt-1">Developed by Vexel IT by Kavizz</p>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}

// ========== MAIN LOYALTY VAULT PAGE (AFTER LOGIN) ==========
$customer = null;
$error = "";

if(isset($_POST['search']))
{
    $search = trim($_POST['search_text']);

    if(!empty($search))
    {
        $apiUrl =
        "http://124.43.17.54/api/customer.php"
        . "?key=ASB2026SECRET"
        . "&search="
        . urlencode($search);

        $response = @file_get_contents($apiUrl);

        if($response)
        {
            $data = json_decode($response,true);

            if(
                isset($data['success']) &&
                $data['success'] == true
            )
            {
                $customer = $data['customer'];
            }
            else
            {
                $error = "Customer not found.";
            }
        }
        else
        {
            $error = "Unable to connect to customer server.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>ASB Fashion & Glamour | Loyalty Vault — Points Rewards</title>

<!-- Tailwind CSS + Google Fonts + Font Awesome -->
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800;14..32,900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    * {
        font-family: 'Inter', sans-serif;
    }

    body {
        background: linear-gradient(145deg, #fff5f5 0%, #ffffff 100%);
        position: relative;
    }

    /* Floating background animation */
    @keyframes floatOrb {
        0% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
        50% { transform: translateY(-25px) rotate(5deg); opacity: 0.6; }
        100% { transform: translateY(0px) rotate(0deg); opacity: 0.3; }
    }

    .floating-bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 0;
    }

    .floating-bg span {
        position: absolute;
        background: radial-gradient(circle, rgba(220,38,38,0.06) 0%, rgba(220,38,38,0) 70%);
        border-radius: 50%;
        animation: floatOrb 15s infinite ease-in-out;
    }

    /* Card animations */
    @keyframes fadeSlideUp {
        0% { opacity: 0; transform: translateY(30px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @keyframes gentlePulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        70% { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .card-animate {
        animation: fadeSlideUp 0.6s cubic-bezier(0.2, 0.9, 0.4, 1.1) forwards;
    }

    .points-pulse {
        animation: gentlePulse 2s infinite;
    }

    .search-btn-hover {
        transition: all 0.25s ease;
    }
    .search-btn-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px -8px rgba(220, 38, 38, 0.4);
    }

    .input-focus-effect:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
        border-color: #dc2626;
        outline: none;
    }

    .info-row {
        transition: all 0.2s ease;
        border-radius: 14px;
    }
    .info-row:hover {
        background-color: #fef2f2;
        transform: translateX(6px);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.97);
        border-radius: 2rem;
        box-shadow: 0 25px 40px -14px rgba(0, 0, 0, 0.12), 0 1px 3px rgba(0,0,0,0.03);
        border: 1px solid rgba(220,38,38,0.18);
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        transform: scale(1.01);
        box-shadow: 0 30px 45px -15px rgba(0, 0, 0, 0.2);
    }

    .point-box-card {
        transition: all 0.25s ease;
    }
    .point-box-card:hover {
        transform: translateY(-5px);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }
    ::-webkit-scrollbar-track {
        background: #fff1f1;
    }
    ::-webkit-scrollbar-thumb {
        background: #dc2626;
        border-radius: 8px;
    }

    /* Logout button style */
    .logout-btn {
        transition: all 0.2s ease;
    }
    .logout-btn:hover {
        background-color: #fee2e2;
        transform: translateX(3px);
    }
</style>
</head>
<body class="antialiased">

<!-- Floating background orbs -->
<div class="floating-bg">
    <span style="width: 300px; height: 300px; top: 5%; left: -100px; animation-duration: 20s;"></span>
    <span style="width: 450px; height: 450px; bottom: 0%; right: -120px; animation-duration: 25s; animation-delay: 2s;"></span>
    <span style="width: 200px; height: 200px; top: 50%; right: 10%; animation-duration: 16s;"></span>
    <span style="width: 250px; height: 250px; bottom: 15%; left: 5%; animation-duration: 22s; opacity: 0.4;"></span>
</div>

<main class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Header with dual brand + Logout -->
    <div data-aos="fade-down" data-aos-duration="800" class="text-center mb-10">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
            <div class="flex-1"></div>
            <div class="inline-flex items-center gap-2 bg-red-50 rounded-full px-5 py-1.5 shadow-sm border border-red-100">
                <i class="fa-regular fa-star text-red-500 text-sm"></i>
                <span class="text-xs font-bold text-red-700 uppercase tracking-wide">ASB FASHION | ASB GLAMOUR</span>
                <i class="fa-regular fa-gem text-red-500 text-sm"></i>
            </div>
            <div class="flex-1 flex justify-end">
                <a href="?logout=1" class="logout-btn bg-white/80 hover:bg-red-50 text-red-600 text-sm font-semibold px-4 py-2 rounded-full shadow-sm border border-red-200 flex items-center gap-2 transition-all">
                    <i class="fa-regular fa-arrow-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
        <h1 class="text-5xl md:text-6xl font-black tracking-tight bg-gradient-to-r from-red-800 via-red-600 to-red-500 bg-clip-text text-transparent">
            Loyalty Vault
        </h1>
        <p class="text-gray-600 mt-4 max-w-2xl mx-auto text-base md:text-lg font-medium">
            <span class="font-bold text-red-600">2% cashback</span> on every bill · Earn, Redeem, Reward
        </p>
        <div class="flex justify-center gap-3 mt-4 flex-wrap">
            <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full"><i class="fa-regular fa-credit-card mr-1"></i> 2% back in points</span>
            <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full"><i class="fa-regular fa-bag-shopping mr-1"></i> Instant redemption</span>
            <span class="bg-red-100 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-full"><i class="fa-regular fa-gem mr-1"></i> Fashion + Glamour</span>
        </div>
        <div class="w-28 h-1 bg-gradient-to-r from-red-400 to-red-600 mx-auto rounded-full mt-5"></div>
    </div>

    <!-- Search Card -->
    <div data-aos="fade-up" data-aos-duration="600" class="glass-card p-5 md:p-7 mb-12">
        <form method="post" class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="relative flex-grow w-full">
                <i class="fa-solid fa-id-card absolute left-5 top-1/2 -translate-y-1/2 text-red-400 text-lg"></i>
                <input 
                    type="text" 
                    name="search_text" 
                    placeholder="Customer Code / Mobile Number / NIC" 
                    required
                    class="input-focus-effect w-full pl-14 pr-5 py-4 rounded-2xl border-2 border-gray-200 focus:border-red-400 transition-all duration-200 bg-white text-gray-800 font-medium shadow-sm"
                    value="<?php echo isset($_POST['search_text']) ? htmlspecialchars($_POST['search_text']) : ''; ?>"
                >
            </div>
            <button 
                type="submit" 
                name="search"
                class="search-btn-hover bg-red-600 hover:bg-red-700 text-white font-extrabold px-9 py-4 rounded-2xl transition-all flex items-center gap-3 shadow-lg shadow-red-200"
            >
                <i class="fa-regular fa-id-card"></i>
                <span>Search Member</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
            </button>
        </form>
    </div>

    <!-- Error Message -->
    <?php if($error): ?>
    <div data-aos="fade-up" data-aos-duration="500" class="mt-8 bg-white rounded-2xl border-l-8 border-red-500 shadow-xl p-5 flex gap-4 items-start">
        <div class="bg-red-100 rounded-full p-3">
            <i class="fa-regular fa-circle-exclamation text-red-600 text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-black text-gray-800">Unable to fetch loyalty data</p>
            <p class="text-gray-600"><?= htmlspecialchars($error) ?></p>
            <button onclick="this.parentElement.parentElement.style.display='none'" class="text-red-500 text-sm font-bold mt-2 inline-flex gap-1 items-center"><i class="fa-regular fa-xmark"></i> Dismiss</button>
        </div>
    </div>
    <?php endif; ?>

    <!-- Customer Details Card (Enhanced with Earned/Redeemed/Available Points) -->
    <?php if($customer): 
        $earned = (float)$customer['POINTS_ADDED'];
        $redeemed = (float)$customer['POINTS_DEDUCTED'];
        $available = $earned - $redeemed;
        if($available < 0) $available = 0;
        
        $address = implode(', ', array_filter([
            $customer['CM_ADD1'],
            $customer['CM_ADD2'],
            $customer['CM_ADD3'],
            $customer['CM_ADD4']
        ]));
    ?>
    <div class="card-animate" data-aos="zoom-in-up" data-aos-duration="700">
        <div class="glass-card overflow-hidden border-l-[6px] border-l-red-500 transition-all duration-300">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-50 via-white to-red-50 px-6 py-5 border-b border-red-100 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="bg-red-100 p-3.5 rounded-2xl text-red-600 shadow-inner">
                        <i class="fa-regular fa-user fa-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-black text-gray-800 tracking-tight">
                            <?= htmlspecialchars($customer['CM_TITLE'] . ' ' . $customer['CM_NAME']) ?>
                        </h2>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-[11px] font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full"><i class="fa-regular fa-crown mr-1"></i> Loyalty Elite</span>
                            <span class="text-[11px] font-semibold text-gray-500"><i class="fa-regular fa-calendar-alt mr-1"></i> Active Member</span>
                        </div>
                    </div>
                </div>
                <div class="bg-red-600 text-white rounded-full px-5 py-2 text-sm font-bold shadow-md flex items-center gap-2">
                    <i class="fa-regular fa-star-shine"></i> 
                    <span>Active Vault</span>
                </div>
            </div>

            <!-- Points Grid: Earned | Redeemed | Available -->
            <div class="p-6 md:p-7">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
                    <!-- Earned Points -->
                    <div class="point-box-card bg-gradient-to-br from-red-50 to-white rounded-2xl p-5 text-center border border-red-200 shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3 text-red-600">
                            <i class="fa-solid fa-arrow-up-long text-xl"></i>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Earned Points</h3>
                        <div class="text-3xl md:text-4xl font-black text-gray-800 mt-2"><?= number_format($earned) ?></div>
                        <p class="text-[10px] text-red-500 mt-1"><i class="fa-regular fa-clock"></i> Lifetime earnings</p>
                    </div>
                    
                    <!-- Redeemed Points -->
                    <div class="point-box-card bg-gradient-to-br from-red-50 to-white rounded-2xl p-5 text-center border border-red-200 shadow-sm">
                        <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-3 text-red-600">
                            <i class="fa-solid fa-arrow-down-long text-xl"></i>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Redeemed Points</h3>
                        <div class="text-3xl md:text-4xl font-black text-gray-800 mt-2"><?= number_format($redeemed) ?></div>
                        <p class="text-[10px] text-red-500 mt-1"><i class="fa-regular fa-gift"></i> Spent on rewards</p>
                    </div>
                    
                    <!-- Available Points (Highlight) -->
                    <div class="point-box-card bg-gradient-to-br from-red-600 to-red-500 rounded-2xl p-5 text-center shadow-lg points-pulse">
                        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3 text-white">
                            <i class="fa-regular fa-gem text-xl"></i>
                        </div>
                        <h3 class="text-xs font-bold text-white/80 uppercase tracking-wider">Available Points</h3>
                        <div class="text-4xl md:text-5xl font-black text-white mt-2"><?= number_format($available) ?></div>
                        <p class="text-[10px] text-white/70 mt-1"><i class="fa-regular fa-circle-check"></i> Ready to redeem</p>
                    </div>
                </div>

                <!-- 2% Cashback Info Banner -->
                <div class="mb-6 bg-gradient-to-r from-amber-50 to-red-50 rounded-xl p-3 flex flex-wrap items-center justify-between gap-3 border border-red-200">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-percent text-2xl text-red-500"></i>
                        <div>
                            <p class="text-sm font-bold text-gray-800">2% Loyalty Cashback on Every Bill</p>
                            <p class="text-xs text-gray-600">ASB Fashion & ASB Glamour — spend more, earn more points</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-full px-4 py-1.5 shadow-sm">
                        <span class="text-xs font-bold text-red-600"><i class="fa-regular fa-receipt"></i> LKR 1,000 = 20 pts</span>
                    </div>
                </div>

                <!-- Customer Details Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    <div class="info-row flex items-center p-2">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mr-4">
                            <i class="fa-solid fa-hashtag"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Customer Code</p>
                            <p class="text-gray-800 font-bold"><?= htmlspecialchars($customer['CM_CODE']) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-row flex items-center p-2">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mr-4">
                            <i class="fa-regular fa-id-card"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">NIC Number</p>
                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($customer['CM_NIC']) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-row flex items-center p-2">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mr-4">
                            <i class="fa-solid fa-mobile-screen-button"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Mobile Number</p>
                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($customer['CM_MOBILE']) ?></p>
                        </div>
                    </div>
                    
                    <div class="info-row flex items-center p-2">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mr-4">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Date of Birth</p>
                            <p class="text-gray-800 font-medium">
                                <?php if(!empty($customer['CM_DOB'])): ?>
                                    <?= date('d-m-Y', strtotime($customer['CM_DOB'])) ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Not provided</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="info-row md:col-span-2 flex items-start p-2">
                        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600 mr-4 flex-shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-400 uppercase">Address</p>
                            <p class="text-gray-700 font-medium leading-relaxed">
                                <?= htmlspecialchars($address) ?: '<span class="italic text-gray-400">Address not available</span>' ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Redemption Freedom Message -->
                <div class="mt-6 pt-4 border-t border-red-100 flex flex-wrap gap-3 justify-between items-center">
                    <div class="flex gap-3 text-xs font-semibold">
                        <span class="bg-red-50 px-3 py-1.5 rounded-full"><i class="fa-regular fa-bag-shopping text-red-500 mr-1"></i> ASB Fashion</span>
                        <span class="bg-red-50 px-3 py-1.5 rounded-full"><i class="fa-regular fa-sparkles text-red-500 mr-1"></i> ASB Glamour</span>
                    </div>
                    <p class="text-xs text-gray-500 flex items-center gap-1"><i class="fa-regular fa-hand-peace text-red-400"></i> Use your points to buy anything — no restrictions</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- No result after search -->
    <?php if(!$customer && !$error && isset($_POST['search'])): ?>
    <div class="text-center mt-12 p-10 rounded-3xl bg-white/70 backdrop-blur-sm border border-red-200 shadow-sm">
        <i class="fa-regular fa-face-frown text-5xl text-red-300 mb-3"></i>
        <p class="text-gray-600 font-medium">No loyalty profile matched your search.</p>
        <p class="text-xs text-red-400 mt-2">Try Customer Code, Mobile Number, or NIC</p>
    </div>
    <?php endif; ?>

    <!-- Perks & Benefits Section -->
    <div data-aos="fade-up" data-aos-delay="150" class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-red-100 shadow-md hover:shadow-xl transition-all group">
            <div class="flex items-center gap-3 mb-3">
                <i class="fa-regular fa-percent text-3xl text-red-500"></i>
                <h3 class="font-black text-xl text-gray-800">2% Loyalty Cashback</h3>
            </div>
            <p class="text-gray-600 text-sm">Every bill amount at <strong class="text-red-600">ASB Fashion</strong> or <strong class="text-red-600">ASB Glamour</strong> instantly adds 2% back as loyalty points. The more you shop, the more you earn.</p>
            <div class="mt-3 bg-red-50 rounded-xl p-2 text-center text-xs font-bold text-red-700">💰 Example: LKR 10,000 bill = +200 points</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-red-100 shadow-md hover:shadow-xl transition-all group">
            <div class="flex items-center gap-3 mb-3">
                <i class="fa-regular fa-hand-peace text-3xl text-red-500"></i>
                <h3 class="font-black text-xl text-gray-800">Redeem Freely</h3>
            </div>
            <p class="text-gray-600 text-sm">Use your loyalty points to buy anything — from luxury fashion apparel to glamour & beauty essentials. No complicated rules, no caps.</p>
            <div class="mt-3 flex gap-2 justify-end text-xs text-red-600 font-semibold"><i class="fa-regular fa-arrow-right"></i> Shop with points now</div>
        </div>
    </div>

    <!-- Brand Strip -->
    <div class="mt-12 text-center text-[11px] tracking-wide text-gray-400 flex justify-center gap-6 flex-wrap">
        <span class="flex items-center gap-1"><i class="fa-regular fa-circle-check text-red-400"></i> ASB Fashion Official Partner</span>
        <span class="flex items-center gap-1"><i class="fa-regular fa-circle-check text-red-400"></i> ASB Glamour Premium Rewards</span>
        <span class="flex items-center gap-1"><i class="fa-regular fa-coins text-red-400"></i> 2% Points Vault Guarantee</span>
    </div>
</main>

<!-- Footer with All Rights Reserved + Vexel IT by Kavizz -->
<footer class="relative z-10 mt-14 border-t-2 border-red-100 bg-white py-8 text-gray-600 text-sm">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center gap-5">
            <div class="text-center md:text-left">
                <div class="flex items-center gap-2 font-black text-red-700 text-xl mb-1 justify-center md:justify-start">
                    <i class="fa-regular fa-crown text-red-500"></i>
                    <span>ASB Fashion & Glamour</span>
                </div>
                <p class="text-xs text-gray-500 max-w-xs">Luxury loyalty ecosystem — earn 2% back, redeem across fashion & beauty.</p>
            </div>
            <div class="text-center border-l-2 border-red-100 pl-6 hidden md:block h-12"></div>
            <div class="text-center md:text-right">
                <p class="font-semibold text-gray-800">© 2025 All Rights Reserved — ASB Fashion</p>
                <p class="text-xs text-gray-500 mt-1 flex gap-1 flex-wrap justify-center md:justify-end">
                    <span>Developed by <strong class="text-red-600">Vexel IT by Kavizz</strong></span>
                    <span class="hidden md:inline">•</span>
                    <span>Loyalty Vault v3.0</span>
                </p>
                <div class="flex gap-4 justify-center md:justify-end mt-2 text-red-400">
                    <i class="fa-brands fa-instagram hover:text-red-700 transition cursor-pointer"></i>
                    <i class="fa-brands fa-facebook hover:text-red-700 transition cursor-pointer"></i>
                    <i class="fa-regular fa-envelope hover:text-red-700 transition cursor-pointer"></i>
                </div>
            </div>
        </div>
        <div class="text-center text-[11px] text-gray-400 mt-6 pt-4 border-t border-gray-100">
            <i class="fa-regular fa-star-of-life text-red-300"></i> Every transaction adds 2% to your points vault — Use points for any product at ASB Fashion & ASB Glamour
        </div>
    </div>
</footer>

<script>
    // Initialize AOS animations
    AOS.init({
        duration: 700,
        once: true,
        offset: 20,
        easing: 'ease-out-quad'
    });
    
    // Additional hover effect for point boxes
    const pointBoxes = document.querySelectorAll('.point-box-card');
    pointBoxes.forEach(box => {
        box.addEventListener('mouseenter', () => {
            box.style.transform = 'translateY(-6px)';
        });
        box.addEventListener('mouseleave', () => {
            box.style.transform = 'translateY(0)';
        });
    });
</script>
</body>
</html>

<?php
/* ===== ORIGINAL FUNCTIONALITY FULLY PRESERVED =====
   - API call: same endpoint "http://124.43.17.54/api/customer.php?key=ASB2026SECRET&search=..."
   - All original fields: CM_TITLE, CM_NAME, CM_CODE, CM_NIC, CM_MOBILE, CM_DOB, CM_ADD1-4
   - NEW FIELDS ADDED from your updated logic: POINTS_ADDED, POINTS_DEDUCTED (Earned/Redeemed points)
   - Points calculation: earned - redeemed = available (with negative protection)
   - Error handling: same messages "Customer not found" / "Unable to connect to customer server"
   - No backend changes — only frontend design, animations, and visual enhancements
   - Added: 2% cashback messaging, ASB Glamour branding, footer credits "Vexel IT by Kavizz"
   - Red/white consistent color scheme, responsive layout, animations with AOS
   - NEW: Login page with username "asbit" and password "asb@123" before accessing the vault
*/
?>