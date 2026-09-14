<?php
require_once 'config.php';

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$step = 1;
$error = "";
$success = "";

if (isset($_SESSION['forgot_step'])) {
    $step = $_SESSION['forgot_step'];
}

// Helper block to generate and issue OTP to avoid logic duplication
function issueOTP($user, $mysql) {
    $targetMobile = $user['mobile'];
    $otp = rand(100000, 999999);
    
    // Set explicit expiry timeframe (5 minutes)
    $expiryTimestamp = time() + 300; 
    $expiry = date('Y-m-d H:i:s', $expiryTimestamp);

    $stmt = $mysql->prepare("INSERT INTO otp_requests (mobile, otp_code, purpose, expires_at) VALUES (?, ?, 'forgot', ?)");
    $stmt->execute([$targetMobile, $otp, $expiry]);

    sendSMSNotification($targetMobile, "Your ASB Password Recovery verification code is: $otp. Expires in 5 mins.");

    $_SESSION['forgot_user_id'] = $user['id'];
    $_SESSION['forgot_mobile'] = $targetMobile;
    $_SESSION['forgot_expiry_ts'] = $expiryTimestamp; // Stored for frontend timer synchronization
    $_SESSION['forgot_step'] = 2;
    
    return "Verification code issued to your recovery mobile line matching registration parameters.";
}

// STEP 1: Verify user exists on Web DB before issuing OTP
if (isset($_POST['send_reset_otp']) && $step == 1) {
    $search = trim($_POST['search_text']);
    $formattedMobile = formatMobileSriLanka($search);

    $stmt = $mysql->prepare("SELECT * FROM users WHERE customer_code = ? OR nic = ? OR mobile = ? OR mobile = ?");
    $stmt->execute([$search, $search, $search, $formattedMobile]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['cached_search_user'] = $user; // Cache user to enable seamless resend capability
        $success = issueOTP($user, $mysql);
        $step = 2;
    } else {
        $error = "No localized accounts are present matching that identification tracking code.";
    }
}

// STEP 2b: Handle secure Resend Request via pipeline
if (isset($_POST['resend_otp_code']) && $step == 2) {
    if (isset($_SESSION['cached_search_user'])) {
        $success = issueOTP($_SESSION['cached_search_user'], $mysql);
    } else {
        $error = "Session expired. Restart the authentication trace loop.";
        unset($_SESSION['forgot_step'], $_SESSION['forgot_user_id'], $_SESSION['forgot_mobile'], $_SESSION['forgot_expiry_ts'], $_SESSION['cached_search_user']);
        $step = 1;
    }
}

// STEP 2: Process Token Match Validation
if (isset($_POST['verify_reset_otp']) && $step == 2) {
    $otp_input = trim($_POST['otp_code']);
    $mobile = $_SESSION['forgot_mobile'];

    $stmt = $mysql->prepare("SELECT id FROM otp_requests WHERE mobile = ? AND otp_code = ? AND purpose = 'forgot' AND expires_at >= NOW() AND is_verified = 0 ORDER BY id DESC LIMIT 1");
    $stmt->execute([$mobile, $otp_input]);
    $otpRecord = $stmt->fetch();

    if ($otpRecord) {
        $stmt = $mysql->prepare("UPDATE otp_requests SET is_verified = 1 WHERE id = ?");
        $stmt->execute([$otpRecord['id']]);

        $_SESSION['forgot_step'] = 3;
        $step = 3;
        $success = "Identity verified. Configure alternative system access password thresholds.";
    } else {
        $error = "Invalid token entry or validation time frame expired.";
    }
}

// STEP 3: Change account entry access passwords
if (isset($_POST['execute_password_reset']) && $step == 3) {
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];

    if (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($pass !== $confirm_pass) {
        $error = "Password mapping configurations mismatch.";
    } else {
        $userId = $_SESSION['forgot_user_id'];
        $newPasswordHash = password_hash($pass, PASSWORD_BCRYPT);

        $stmt = $mysql->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->execute([$newPasswordHash, $userId]);

        unset($_SESSION['forgot_step'], $_SESSION['forgot_user_id'], $_SESSION['forgot_mobile'], $_SESSION['forgot_expiry_ts'], $_SESSION['cached_search_user']);
        $_SESSION['login_flash_success'] = "Password configuration updated successfully. Sign in using new details.";
        header("Location: login.php");
        exit;
    }
}

// Clean recovery pipeline resets
if (isset($_POST['restart_forgot'])) {
    unset($_SESSION['forgot_step'], $_SESSION['forgot_user_id'], $_SESSION['forgot_mobile'], $_SESSION['forgot_expiry_ts'], $_SESSION['cached_search_user']);
    header("Location: forgot_password.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>ASB Loyal Vault | Account Recovery</title>
    <link rel="icon" type="image/png" href="logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-sub: #ffffff;
            --bg-card: #ffffff;
            --primary: #e11d48;
            --primary-hover: #be123c;
            --primary-glow: rgba(225, 29, 72, 0.08);
            --border-color: #e2e8f0;
            --border-hover: rgba(225, 29, 72, 0.4);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent-green: #10b981;
            --accent-amber: #b45309;
            --transition-smooth: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.25rem;
            -webkit-font-smoothing: antialiased;
            position: relative;
        }

        /* Ambient Premium Soft Light Glow Layers & Imagery Backdrop */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: linear-gradient(135deg, rgba(248, 250, 252, 0.9) 30%, rgba(255, 255, 255, 0.75) 100%), 
                              url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=1200&q=80');
            background-size: cover;
            background-position: center;
            z-index: -2;
        }

        body::after {
            content: '';
            position: absolute;
            top: 5%;
            left: 20%;
            width: 45vw;
            height: 45vw;
            max-width: 600px;
            max-height: 600px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.04) 0%, rgba(248, 250, 252, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .recovery-container {
            width: 100%;
            max-width: 520px;
            animation: revealUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
        }

        @keyframes revealUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* --- IDENTITY CONTAINER HEADER --- */
        .brand-header-cluster {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-logo-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            width: 70px;
            height: 70px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
            border: 1px solid var(--border-color);
            overflow: hidden;
            padding: 10px;
        }

        .brand-logo-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Fallback avatar-icon setup if brand logo resource is missing */
        .brand-logo-fallback {
            font-size: 1.8rem;
            color: var(--primary);
        }

        .brand-title-main {
            font-weight: 800;
            font-size: 1.75rem;
            letter-spacing: -0.5px;
            color: #0f172a;
            line-height: 1.2;
        }

        .brand-title-main span {
            color: var(--primary);
        }

        .brand-subtitle-sub {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: var(--text-muted);
            font-weight: 700;
            margin-top: 8px;
        }

        .brand-subtitle-sub em {
            font-family: 'Playfair Display', serif;
            text-transform: none;
            letter-spacing: 1px;
            font-style: italic;
            font-weight: 600;
            color: var(--accent-amber);
        }

        /* --- PREMIUM CARD FRAME --- */
        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.02);
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .premium-card:hover {
            border-color: rgba(225, 29, 72, 0.2);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.08);
        }

        .step-indicator-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            margin-bottom: 1.5rem;
        }

        .step-indicator-pill span {
            color: var(--primary);
        }

        .form-instruction {
            font-size: 0.95rem;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 2rem;
            font-weight: 400;
        }

        /* --- GROUPS AND CONTROLS --- */
        .form-group-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-group-wrapper i.input-context-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: var(--transition-smooth);
            pointer-events: none;
        }

        .premium-input-field {
            width: 100%;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.1rem 1.1rem 1.1rem 3.25rem;
            color: var(--text-main);
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            outline: none;
            transition: var(--transition-smooth);
        }

        .premium-input-field:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.06);
        }

        .premium-input-field::placeholder {
            color: #94a3b8;
            font-weight: 500;
        }

        .premium-input-field:focus + i.input-context-icon {
            color: var(--primary);
        }

        /* Dynamic overrides explicitly tailored for OTP workflow entry templates */
        .otp-style-override {
            letter-spacing: 8px !important; 
            font-size: 1.4rem !important; 
            text-align: center !important; 
            padding-left: 1.1rem !important;
        }

        /* --- TIMER ENGINE DASHBOARD UI --- */
        .timer-dashboard {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
            gap: 10px;
        }
        
        .timer-text-cluster {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #334155;
        }

        .timer-text-cluster i {
            color: var(--primary);
        }

        .timer-countdown-node {
            font-family: monospace;
            font-weight: 700;
            font-size: 1.05rem;
            color: var(--text-main);
            background: #e2e8f0;
            padding: 2px 8px;
            border-radius: 6px;
            white-space: nowrap;
        }

        .btn-resend-trigger {
            background: none;
            border: none;
            color: var(--primary);
            font-weight: 700;
            font-family: inherit;
            font-size: 0.88rem;
            cursor: pointer;
            transition: var(--transition-smooth);
            display: none; /* Controlled via JS Engine */
            white-space: nowrap;
        }

        .btn-resend-trigger:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        /* --- ACTION TRIGGERS --- */
        .action-button-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 2rem;
        }

        .btn-premium-primary {
            width: 100%;
            padding: 1.1rem;
            background: var(--primary);
            color: #ffffff;
            border: 1px solid var(--primary);
            border-radius: 16px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition-smooth);
        }

        .btn-premium-primary:hover {
            background: var(--primary-hover);
            border-color: var(--primary-hover);
            box-shadow: 0 10px 20px var(--primary-glow);
            transform: translateY(-1px);
        }

        .btn-premium-secondary {
            width: 100%;
            padding: 1.1rem;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition-smooth);
        }

        .btn-premium-secondary:hover {
            background: #e2e8f0;
            color: #1e293b;
            transform: translateY(-1px);
        }

        .back-navigation-link {
            display: block;
            text-align: center;
            margin-top: 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: var(--transition-smooth);
        }

        .back-navigation-link:hover {
            color: var(--primary);
        }

        /* --- NOTIFICATION PIPELINES --- */
        .system-notification-banner {
            border-radius: 16px;
            padding: 1.1rem 1.4rem;
            margin-bottom: 2rem;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            line-height: 1.5;
        }

        .banner-type-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-left: 4px solid var(--primary);
            color: #9f1239;
        }

        .banner-type-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-left: 4px solid var(--accent-green);
            color: #065f46;
        }

        .system-debug-window {
            background: #fef9c3;
            border: 1px dashed #ca8a04;
            border-radius: 14px;
            padding: 1rem;
            margin-bottom: 2rem;
            font-family: monospace;
            font-size: 0.78rem;
            color: #854d0e;
            line-height: 1.4;
        }

        /* --- FOOTER SPECIFICATIONS --- */
        .system-footer-container {
            text-align: center;
            margin-top: 2.5rem;
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.6);
        }

        /* --- CONTINUOUS RESPONSIVE DESIGN RULES --- */
        @media (max-width: 576px) {
            body { 
                padding: 1.5rem 1rem; 
            }
            .premium-card { 
                padding: 2.25rem 1.5rem; 
                border-radius: 20px; 
            }
            .brand-title-main { 
                font-size: 1.45rem; 
            }
            .form-instruction {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
            }
            .timer-dashboard {
                flex-direction: column;
                align-items: flex-start;
                padding: 1rem;
            }
            .timer-countdown-node {
                align-self: flex-end;
                margin-top: -24px;
            }
            .btn-resend-trigger {
                margin-top: 8px;
                display: block;
            }
        }

        @media (max-width: 380px) {
            .premium-card {
                padding: 2rem 1rem;
            }
            .otp-style-override {
                letter-spacing: 4px !important;
                font-size: 1.2rem !important;
            }
        }
    </style>
</head>
<body>

<div class="recovery-container">
    
    <div class="brand-header-cluster">
        <div class="brand-logo-wrapper">
            <!-- Elegant image engine deployment with fallback handler configuration -->
            <img src="logo.png" alt="ASB Logo" class="brand-logo-img" onerror="this.style.display='none'; document.getElementById('logo-fallback-icon').style.display='block';">
            <i class="fas fa-vault brand-logo-fallback" id="logo-fallback-icon" style="display: none;"></i>
        </div>
        <div class="brand-title-main">ASB <span>Loyal Vault</span></div>
        <div class="brand-subtitle-sub">Fashion & <em>Glamour</em></div>
    </div>

    <?php if(!empty($error)): ?>
        <div class="system-notification-banner banner-type-error">
            <i class="fas fa-circle-exclamation" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="system-notification-banner banner-type-success">
            <i class="fas fa-circle-check" style="margin-top: 3px;"></i>
            <div><?= htmlspecialchars($success) ?></div>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['sms_debug'])): ?>
        <div class="system-debug-window">
            <strong style="color: var(--accent-amber); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px;">Internal Debug Stream:</strong>
            <?= htmlspecialchars($_SESSION['sms_debug']); unset($_SESSION['sms_debug']); ?>
        </div>
    <?php endif; ?>

    <div class="premium-card">
        
        <?php if($step == 1){ ?>
            <div class="step-indicator-pill">Verification Phase <span>1 of 3</span></div>
            <p class="form-instruction">Provide your registered Customer Code, NIC tracking profile, or your verified mobile line connection sequence below to locate your security parameter profile.</p>
            
            <form method="post" autocomplete="off">
                <div class="form-group-wrapper">
                    <input type="text" name="search_text" class="premium-input-field" placeholder="Account Identification Code" required autofocus>
                    <i class="fas fa-fingerprint input-context-icon"></i>
                </div>
                
                <div class="action-button-stack">
                    <button type="submit" name="send_reset_otp" class="btn-premium-primary">
                        Send Verification OTP <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
            <a href="login.php" class="back-navigation-link"><i class="fas fa-arrow-left-long"></i> Return to Gateway Login</a>
        <?php } ?>

        <?php if($step == 2){ ?>
            <div class="step-indicator-pill">Identity Authentication <span>2 of 3</span></div>
            <p class="form-instruction">Enter the 6-digit dynamic authentication token transmitted via SMS to your recovery profile line sequence to bypass access blocks.</p>
            
            <div class="timer-dashboard">
                <div class="timer-text-cluster">
                    <i class="fas fa-stopwatch" id="timer-icon"></i>
                    <span id="timer-status-text">Code expires in:</span>
                </div>
                <div class="timer-countdown-node" id="countdown-display">05:00</div>
                
                <form method="post" id="resend-form" style="margin:0;">
                    <button type="submit" name="resend_otp_code" id="resend-trigger-btn" class="btn-resend-trigger">
                        <i class="fas fa-rotate-right"></i> Resend Code
                    </button>
                </form>
            </div>

            <form method="post" autocomplete="off">
                <div class="form-group-wrapper">
                    <input type="text" name="otp_code" class="premium-input-field otp-style-override" placeholder="••••••" required maxlength="6" pattern="\d{6}" autofocus>
                </div>
                
                <div class="action-button-stack">
                    <button type="submit" name="verify_reset_otp" class="btn-premium-primary">
                        Verify Token Match <i class="fas fa-shield-halved"></i>
                    </button>
                    <button type="submit" name="restart_forgot" class="btn-premium-secondary" formnovalidate>
                        Cancel Operation
                    </button>
                </div>
            </form>

            <script>
                (function() {
                    const targetTimestamp = <?= isset($_SESSION['forgot_expiry_ts']) ? $_SESSION['forgot_expiry_ts'] : (time() + 300) ?> * 1000;
                    
                    function updateRecoveryTimer() {
                        const now = new Date().getTime();
                        const distance = targetTimestamp - now;
                        
                        const displayNode = document.getElementById('countdown-display');
                        const statusText = document.getElementById('timer-status-text');
                        const resendBtn = document.getElementById('resend-trigger-btn');
                        const iconNode = document.getElementById('timer-icon');

                        if (distance < 0) {
                            clearInterval(timerInterval);
                            if(displayNode) displayNode.style.display = 'none';
                            if(iconNode) iconNode.className = 'fas fa-triangle-exclamation';
                            if(statusText) {
                                statusText.innerHTML = 'OTP Code Validity Terminated.';
                                statusText.style.color = 'var(--primary)';
                            }
                            if(resendBtn) resendBtn.style.display = 'inline-block';
                            return;
                        }

                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        const paddedMinutes = String(minutes).padStart(2, '0');
                        const paddedSeconds = String(seconds).padStart(2, '0');
                        
                        if(displayNode) displayNode.innerHTML = `${paddedMinutes}:${paddedSeconds}`;
                    }

                    updateRecoveryTimer();
                    const timerInterval = setInterval(updateRecoveryTimer, 1000);
                })();
            </script>
        <?php } ?>

        <?php if($step == 3){ ?>
            <div class="step-indicator-pill">Credential Update <span>3 of 3</span></div>
            <p class="form-instruction">Configure alternative vault access parameters. Ensure passwords contain appropriate security spacing complexity thresholds.</p>
            
            <form method="post" autocomplete="off">
                <div class="form-group-wrapper">
                    <input type="password" name="password" class="premium-input-field" placeholder="New Access Password" required autofocus>
                    <i class="fas fa-lock-open input-context-icon"></i>
                </div>
                
                <div class="form-group-wrapper">
                    <input type="password" name="confirm_password" class="premium-input-field" placeholder="Confirm New Password" required>
                    <i class="fas fa-lock input-context-icon"></i>
                </div>
                
                <div class="action-button-stack">
                    <button type="submit" name="execute_password_reset" class="btn-premium-primary">
                        Update Password Credentials <i class="fas fa-key"></i>
                    </button>
                </div>
            </form>
        <?php } ?>

    </div>

    <footer class="system-footer-container">
        <p>&copy; 2026 ASB Fashion. Secure Access Authorization System.</p>
    </footer>

</div>

</body>
</html>