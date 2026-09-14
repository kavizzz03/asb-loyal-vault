<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Route back unauthorized actors attempting access
if (!isset($_SESSION['authenticated_user']) || $_SESSION['authenticated_user'] !== true) {
    header("Location: login.php");
    exit;
}

$customer = $_SESSION['customer_profile'];

$earned = (float)($customer['POINTS_ADDED'] ?? 0);
$redeemed = (float)($customer['POINTS_DEDUCTED'] ?? 0);
$available = $earned - $redeemed;
if ($available < 0) { $available = 0; }

$address = implode(', ', array_filter([
    $customer['CM_ADD1'] ?? '',
    $customer['CM_ADD2'] ?? '',
    $customer['CM_ADD3'] ?? '',
    $customer['CM_ADD4'] ?? ''
]));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>ASB Loyal Vault | Customer Portal</title>
    <link rel="icon" type="image/png" href="logo.png">
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-panel: #ffffff;
            --bg-card: rgba(255, 255, 255, 0.85);
            --primary: #e11d48;
            --primary-hover: #be123c;
            --primary-glow: rgba(225, 29, 72, 0.08);
            --border-color: rgba(15, 23, 42, 0.08);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --accent-gold: #b45309;
            --transition-luxury: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
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
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Luxury Ambient Glow Blobs background matrix */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            right: 10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.04) 0%, rgba(0, 0, 0, 0) 70%);
            z-index: -1;
            pointer-events: none;
        }

        .dashboard-wrapper {
            max-width: 1300px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* ==========================================================================
           PREMIUM NAVBAR COMPONENT (LIGHT EDITION)
           ========================================================================== */
        .navbar-premium {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(248, 250, 252, 0.9) 100%);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1.25rem 2rem;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
        }

        .brand-logo-img {
            height: 40px;
            width: auto;
            object-fit: contain;
        }

        .brand-title-main {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.2;
            color: #0f172a;
        }

        .brand-title-main span {
            color: var(--primary);
        }

        .brand-subtitle-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-weight: 700;
        }

        .brand-subtitle-sub em {
            font-family: 'Playfair Display', serif;
            text-transform: capitalize;
            color: #0f172a;
            letter-spacing: 0px;
            font-style: italic;
        }

        .logout-action-pill {
            background: rgba(15, 23, 42, 0.04);
            border: 1px solid var(--border-color);
            color: #be123c;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: var(--transition-luxury);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logout-action-pill:hover {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.15);
            transform: translateY(-1px);
        }

        /* ==========================================================================
           METRICS DASHBOARD SYSTEM WITH DYNAMIC HERO CARDS
           ========================================================================== */
        .metrics-panel-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .premium-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: var(--transition-luxury);
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
        }

        .premium-card:hover {
            border-color: rgba(225, 29, 72, 0.2);
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
        }

        .metric-data-card {
            padding: 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 160px;
        }

        /* Cinematic light-fashion lookbook card integration */
        .metric-card-dark {
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.9), rgba(241, 245, 20, 0.02)), url('https://images.unsplash.com/photo-1490481651871-ab68de25d43d?q=80&w=600&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(225, 29, 72, 0.2);
            box-shadow: 0 4px 20px rgba(225, 29, 72, 0.05);
        }

        .metric-card-value {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -1px;
            line-height: 1;
            margin: 0.75rem 0;
        }

        .value-light {
            background: linear-gradient(135deg, #0f172a 0%, #475569 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .value-dark {
            color: #1e293b;
        }

        /* ==========================================================================
           SPLIT ROW: DATA IDENTITIES & CHART INTERFACES
           ========================================================================== */
        .split-analytics-row {
            display: grid;
            grid-template-columns: 1.2fr 0.8fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .card-padded-box {
            padding: 2rem;
        }

        .component-title-header {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
            color: #0f172a;
        }

        .component-title-header i {
            color: var(--primary);
        }

        /* Profile Grid Layout Arrays */
        .profile-table-array {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .profile-row-item {
            border-bottom: 1px solid rgba(15, 23, 42, 0.04);
            padding-bottom: 0.75rem;
        }

        .profile-row-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
        }

        .profile-row-label i {
            color: rgba(15, 23, 42, 0.3);
            font-size: 0.8rem;
        }

        .profile-row-value-bold {
            font-size: 1rem;
            font-weight: 700;
            color: var(--accent-gold);
        }

        .profile-row-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
        }

        .edit-trigger-btn {
            margin-top: 1.75rem;
            width: 100%;
            padding: 0.9rem;
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition-luxury);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .edit-trigger-btn:hover {
            background: #0f172a;
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Canvas Wrapper adjustments */
        .chart-rendering-canvas {
            position: relative;
            height: 230px;
            width: 100%;
        }

        /* ==========================================================================
           CONNECTED PRIVILEGES ICON NODES
           ========================================================================== */
        .privileges-grid-system {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .privilege-node {
            background: rgba(15, 23, 42, 0.01);
            border: 1px solid rgba(15, 23, 42, 0.03);
            border-radius: 16px;
            padding: 1.5rem;
            transition: var(--transition-luxury);
        }

        .privilege-node:hover {
            background: rgba(225, 29, 72, 0.02);
            border-color: rgba(225, 29, 72, 0.12);
            transform: translateY(-2px);
        }

        .privilege-icon-shield {
            width: 44px;
            height: 44px;
            background: rgba(225, 29, 72, 0.06);
            border: 1px solid rgba(225, 29, 72, 0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .privilege-node h4 {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .privilege-node p {
            font-size: 0.85rem;
            color: var(--text-muted);
            line-height: 1.5;
        }

        /* ==========================================================================
           MODAL VAULT MANAGEMENT INTERFACE (LIGHT MODE ELEVATION)
           ========================================================================== */
        .vault-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            z-index: 100;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .vault-modal-card {
            background: #ffffff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 24px;
            width: 100%;
            max-width: 650px;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
            animation: modalFadeUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            overflow: hidden;
            color: #0f172a;
        }

        .vault-modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15, 23, 42, 0.01);
        }

        .vault-modal-header h5 {
            font-size: 1.15rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-icon-trigger {
            font-size: 1.25rem;
            color: var(--text-muted);
            cursor: pointer;
            transition: var(--transition-luxury);
        }

        .close-icon-trigger:hover {
            color: #0f172a;
        }

        .vault-modal-body {
            padding: 2rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .modal-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .modal-field-full {
            grid-column: span 2;
        }

        .vault-input-group label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .vault-input {
            width: 100%;
            padding: 0.85rem 1rem;
            background: #f8fafc;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            color: #0f172a;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            outline: none;
            transition: var(--transition-luxury);
        }

        .vault-input:focus {
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .vault-input[readonly] {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
            border-color: transparent;
        }

        .vault-modal-footer {
            padding: 1.25rem 2rem;
            border-top: 1px solid var(--border-color);
            background: #f8fafc;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn-vault-action {
            padding: 0.85rem 1.5rem;
            border-radius: 12px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition-luxury);
            font-family: inherit;
        }

        .btn-vault-close {
            background: transparent;
            border: 1px solid var(--border-color);
            color: var(--text-muted);
        }

        .btn-vault-close:hover {
            background: rgba(15, 23, 42, 0.03);
            color: #0f172a;
        }

        .btn-vault-save {
            background: var(--primary);
            border: none;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.15);
        }

        .btn-vault-save:hover {
            background: var(--primary-hover);
            box-shadow: 0 6px 18px rgba(225, 29, 72, 0.25);
        }

        /* ==========================================================================
           TOAST NOTIFICATION ARCHITECTURE
           ========================================================================== */
        .system-toast-alert {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #ffffff;
            border: 1px solid rgba(225, 29, 72, 0.15);
            border-left: 4px solid var(--primary);
            padding: 1rem 1.5rem;
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            z-index: 200;
            font-weight: 600;
            font-size: 0.9rem;
            color: #0f172a;
            transform: translateY(120%);
            opacity: 0;
            transition: var(--transition-luxury);
        }

        .system-toast-alert.trigger-show {
            transform: translateY(0);
            opacity: 1;
        }

        /* Global Alert Banners */
        .system-error-banner {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
            padding: 1rem 1.5rem;
            border-radius: 14px;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .system-footer-container {
            margin-top: 4rem;
            border-top: 1px solid var(--border-color);
            padding-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #94a3b8;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .developer-signature-badge {
            background: rgba(15, 23, 42, 0.02);
            border: 1px solid var(--border-color);
            padding: 6px 14px;
            border-radius: 100px;
            color: var(--text-muted);
        }

        @keyframes modalFadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ==========================================================================
           RESPONSIVE VIEWPORT BREAKPOINTS (TABLETS & PHONES)
           ========================================================================== */
        @media (max-width: 1024px) {
            .metrics-panel-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .split-analytics-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .navbar-premium {
                flex-direction: column;
                gap: 1.25rem;
                text-align: center;
                padding: 1.5rem;
            }
            .user-action-cluster {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            .metrics-panel-grid {
                grid-template-columns: 1fr;
            }
            .profile-table-array {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .privileges-grid-system {
                grid-template-columns: 1fr;
            }
            .modal-grid {
                grid-template-columns: 1fr;
            }
            .modal-field-full {
                grid-column: span 1;
            }
            .system-footer-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }
  </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <div id="globalAlert" style="display: none;" class="system-error-banner mb-6">
        <i class="fas fa-triangle-exclamation"></i> <span id="alertMessage"></span>
    </div>
    
    <header class="navbar-premium">
        <div class="flex items-center gap-4 max-sm:flex-col">
            <img src="logo.png" alt="ASB Logo" class="brand-logo-img h-10 w-auto" onerror="this.style.display='none';">
            <div class="brand-text-identity text-left max-sm:text-center">
                <div class="brand-title-main">ASB <span>Loyal Vault</span></div>
                <div class="brand-subtitle-sub">Fashion & <em>Glamour</em></div>
            </div>
        </div>
        
        <div class="user-action-cluster flex items-center gap-6">
            <div class="welcome-back-text text-right max-sm:text-center">
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Authenticated Member</p>
                <h4 id="customerNamePlaceholder" class="font-extrabold text-slate-800 text-base">Loading Session...</h4>
            </div>
            <a href="logout.php" class="logout-action-pill">
                <i class="fas fa-right-from-bracket"></i> Sign Out
            </a>
        </div>
    </header>

    <div class="metrics-panel-grid" id="statsContainer"></div>

    <div class="split-analytics-row">
        <div class="premium-card card-padded-box">
            <div class="component-title-header">
                <span><i class="fas fa-address-card"></i> Personal Identity Verification</span>
            </div>
            <div id="profileDetails" class="profile-table-array"></div>
            <button class="edit-trigger-btn" onclick="openEditModal()">
                <i class="fas fa-user-pen"></i> Edit Info & Addresses
            </button>
        </div>

        <div class="premium-card card-padded-box">
            <div class="component-title-header">
                <i class="fas fa-chart-pie"></i> Ledger Distribution Balance
            </div>
            <div class="chart-rendering-canvas">
                <canvas id="pointsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="premium-card card-padded-box my-8">
        <div class="component-title-header">
            <i class="fas fa-wand-magic-sparkles"></i> Connected Vault Benefits
        </div>
        <div class="privileges-grid-system">
            <div class="privilege-node">
                <div class="privilege-icon-shield"><i class="fas fa-tags"></i></div>
                <h4>2% Store Cashback</h4>
                <p>Accumulate 2% reward volume on transactions across ASB Fashion and ASB Glamour chains.</p>
            </div>
            <div class="privilege-node">
                <div class="privilege-icon-shield"><i class="fas fa-bolt-lightning"></i></div>
                <h4>Real Time Redemptions</h4>
                <p>Instantly convert ledger balances to immediate purchase reductions at physical checkouts.</p>
            </div>
            <div class="privilege-node">
                <div class="privilege-icon-shield"><i class="fas fa-ticket"></i></div>
                <h4>Collection Access</h4>
                <p>Unlock priority notification pipelines for newly arriving seasonal lookbooks.</p>
            </div>
        </div>
    </div>

    <footer class="system-footer-container">
        <p>&copy; 2026 ASB Fashion Network Operations. All rights reserved.</p>
        <div class="developer-signature-badge">
            <i class="fas fa-code-branch text-rose-500"></i> Powered by Vexel IT by Kavizz
        </div>
    </footer>
</div>

<!-- ==========================================
     MODAL GATEWAY OVERLAY PREPARATION BLOCK
     ========================================== -->
<div class="vault-modal-overlay" id="editVaultModal">
    <div class="vault-modal-card">
        <div class="vault-modal-header">
            <h5><i class="fas fa-user-gear text-rose-500"></i> Update Customer Profile</h5>
            <i class="fas fa-xmark close-icon-trigger" onclick="closeEditModal()"></i>
        </div>
        <div class="vault-modal-body">
            <div id="modalAlertBox" class="hidden system-error-banner mb-4" role="alert"></div>
            <form id="vaultUpdateForm" onsubmit="event.preventDefault();">
                <div class="modal-grid">
                    <div class="vault-input-group">
                        <label>Customer Code (Read Only)</label>
                        <input type="text" id="input_CM_CODE" class="vault-input" readonly>
                    </div>
                    <div class="vault-input-group">
                        <label>Mobile Number (Read Only)</label>
                        <input type="text" id="input_CM_MOBILE" class="vault-input" readonly>
                    </div>
                    <div class="vault-input-group modal-field-full">
                        <label>Full Name</label>
                        <input type="text" id="input_CM_NAME" class="vault-input" required>
                    </div>
                    <div class="vault-input-group">
                        <label>National ID (NIC)</label>
                        <input type="text" id="input_CM_NIC" class="vault-input" oninput="validateAndUpdateFromNIC(this.value)">
                    </div>
                    <div class="vault-input-group">
                        <label>Date of Birth</label>
                        <input type="date" id="input_CM_DOB" class="vault-input">
                    </div>
                    <div class="vault-input-group modal-field-full">
                        <label>Email Address</label>
                        <input type="email" id="input_CM_EMAIL" class="vault-input" placeholder="example@domain.com">
                    </div>
                    <div class="vault-input-group"><label>Address Line 1</label><input type="text" id="input_CM_ADD1" class="vault-input"></div>
                    <div class="vault-input-group"><label>Address Line 2</label><input type="text" id="input_CM_ADD2" class="vault-input"></div>
                    <div class="vault-input-group"><label>Address Line 3</label><input type="text" id="input_CM_ADD3" class="vault-input"></div>
                    <div class="vault-input-group"><label>Address Line 4</label><input type="text" id="input_CM_ADD4" class="vault-input"></div>
                </div>
            </form>
        </div>
        <div class="vault-modal-footer">
            <button type="button" class="btn-vault-action btn-vault-close" onclick="closeEditModal()">Cancel</button>
            <button type="button" id="saveUpdateBtn" class="btn-vault-action btn-vault-save" onclick="commitCustomerUpdates()">Save Ledger Changes</button>
        </div>
    </div>
</div>

<div id="liveToast" class="system-toast-alert">
    <i class="fas fa-circle-check text-rose-500 text-base"></i> 
    <span id="toastText">Secure data pathway initialized</span>
</div>

<!-- ==========================================
     CORE DATA BINDING APPLICATION ENGINE
<!-- ==========================================
     CORE DATA BINDING APPLICATION ENGINE
     ========================================== -->
<script>
    let customerData = null;
    let pointsEarned = 0, pointsRedeemed = 0, pointsAvailable = 0;
    let dynamicChartInstance = null;
    
    try {
        const phpCustomerProfile = {
            CM_TITLE: '<?= htmlspecialchars($customer['CM_TITLE'] ?? 'Mr.', ENT_QUOTES, 'UTF-8') ?>',
            CM_NAME: '<?= htmlspecialchars($customer['CM_NAME'] ?? 'Loyal Member', ENT_QUOTES, 'UTF-8') ?>',
            CM_CODE: '<?= htmlspecialchars($customer['CM_CODE'] ?? 'ASB000', ENT_QUOTES, 'UTF-8') ?>',
            CM_NIC: '<?= htmlspecialchars($customer['CM_NIC'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            CM_MOBILE: '<?= htmlspecialchars($customer['CM_MOBILE'] ?? '+94XXXXXXXX', ENT_QUOTES, 'UTF-8') ?>',
            CM_DOB_RAW: '<?= !empty($customer['CM_DOB']) ? date('Y-m-d', strtotime($customer['CM_DOB'])) : '' ?>',
            CM_DOB: '<?= !empty($customer['CM_DOB']) ? date('d-m-Y', strtotime($customer['CM_DOB'])) : 'Not Provided' ?>',
            CM_EMAIL: '<?= htmlspecialchars($customer['CM_EMAIL'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            CM_ADD1: '<?= htmlspecialchars($customer['CM_ADD1'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            CM_ADD2: '<?= htmlspecialchars($customer['CM_ADD2'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            CM_ADD3: '<?= htmlspecialchars($customer['CM_ADD3'] ?? '', ENT_QUOTES, 'UTF-8') ?>',
            CM_ADD4: '<?= htmlspecialchars($customer['CM_ADD4'] ?? '', ENT_QUOTES, 'UTF-8') ?>'
        };
        
        pointsEarned = parseFloat(<?= json_encode($earned) ?>) || 0;
        pointsRedeemed = parseFloat(<?= json_encode($redeemed) ?>) || 0;
        pointsAvailable = parseFloat(<?= json_encode($available) ?>) || 0;
        
        customerData = phpCustomerProfile;
    } catch(e) {
        console.error("Data pipeline instantiation failure", e);
        showErrorAlert("System error compiling application state tables.");
    }

    function showErrorAlert(msg) {
        const alertDiv = document.getElementById('globalAlert');
        const alertMsg = document.getElementById('alertMessage');
        if(alertDiv && alertMsg) {
            alertMsg.innerText = msg;
            alertDiv.style.display = 'flex';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function triggerSystemToast(msg) {
        const toastEl = document.getElementById('liveToast');
        const textSpan = document.getElementById('toastText');
        if(toastEl && textSpan) {
            textSpan.innerText = msg;
            toastEl.classList.add('trigger-show');
            setTimeout(() => { toastEl.classList.remove('trigger-show'); }, 3500);
        }
    }

    function processMetricsOutput() {
        const container = document.getElementById('statsContainer');
        if(!container) return;
        container.innerHTML = `
        <div style="background: rgba(16, 185, 129, 0.04); border: 1px solid rgba(16, 185, 129, 0.15); border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.4s ease; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 0.75rem;">
                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #065f46;">Total Points Earned</span>
                <div style="width: 32px; height: 32px; background: #ecfdf5; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #10b981;">
                    <i class="fas fa-arrow-trend-up" style="font-size: 0.9rem;"></i>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #064e3b; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', sans-serif;">
                ${pointsEarned.toLocaleString()} <span style="font-size: 0.85rem; font-weight: 600; color: #10b981;">PTS</span>
            </div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #059669; margin-top: 0.75rem; display: flex; align-items: center; gap: 4px;">
                <i class="fas fa-chart-line"></i> Lifetime volume
            </div>
        </div>

        <div style="background: rgba(217, 119, 6, 0.04); border: 1px solid rgba(217, 119, 6, 0.15); border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.4s ease; box-shadow: 0 10px 25px rgba(217, 119, 6, 0.01);">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 0.75rem;">
                <span style="font-size: 0.72rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; color: #92400e;">Total Redeemed</span>
                <div style="width: 32px; height: 32px; background: #fffbeb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #d97706;">
                    <i class="fas fa-gift" style="font-size: 0.9rem;"></i>
                </div>
            </div>
            <div style="font-size: 1.75rem; font-weight: 800; color: #78350f; letter-spacing: -0.5px; font-family: 'Plus Jakarta Sans', sans-serif;">
                ${pointsRedeemed.toLocaleString()} <span style="font-size: 0.85rem; font-weight: 600; color: #d97706;">PTS</span>
            </div>
            <div style="font-size: 0.75rem; font-weight: 600; color: #b45309; margin-top: 0.75rem; display: flex; align-items: center; gap: 4px;">
                <i class="fas fa-history"></i> Settled balance
            </div>
        </div>
        
        <div class="premium-card metric-data-card metric-card-dark">
            <div class="flex justify-between items-start w-100">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400">Available Balance</span>
                <i class="fas fa-vault text-amber-500 text-lg animate-pulse"></i>
            </div>
            <div class="metric-card-value value-light">${pointsAvailable.toLocaleString()} <span class="text-xs font-semibold text-rose-400">PTS</span></div>
            <div class="text-xs font-bold text-rose-400 mt-2"><i class="fas fa-shield-check mr-1"></i> Active fluid luxury token</div>
        </div>`;
    }

    function processProfileOutput() {
        const profileDiv = document.getElementById('profileDetails');
        const placeholder = document.getElementById('customerNamePlaceholder');
        if(placeholder && customerData) {
            placeholder.innerText = `${customerData.CM_TITLE} ${customerData.CM_NAME}`.trim();
        }
        if(!profileDiv) return;
        
        const addrArr = [customerData.CM_ADD1, customerData.CM_ADD2, customerData.CM_ADD3, customerData.CM_ADD4].filter(v => v && v.trim() !== '');
        const addressBlock = addrArr.join(', ');
        
        profileDiv.innerHTML = `
            <div class="profile-row-item">
                <div class="profile-row-label"><i class="fas fa-hashtag"></i> ID Index</div>
                <div class="profile-row-value-bold">${customerData.CM_CODE}</div>
            </div>
            <div class="profile-row-item">
                <div class="profile-row-label"><i class="fas fa-id-card-clip"></i> National ID</div>
                <div class="profile-row-value">${customerData.CM_NIC || 'Not Provided'}</div>
            </div>
            <div class="profile-row-item">
                <div class="profile-row-label"><i class="fas fa-envelope"></i> Digital Mail</div>
                <div class="profile-row-value">${customerData.CM_EMAIL || 'Not provided'}</div>
            </div>
            <div class="profile-row-item">
                <div class="profile-row-label"><i class="fas fa-mobile-screen"></i> Mobile</div>
                <div class="profile-row-value">${customerData.CM_MOBILE}</div>
            </div>
            <div class="profile-row-item">
                <div class="profile-row-label"><i class="fas fa-calendar"></i> Birth Date</div>
                <div class="profile-row-value">${customerData.CM_DOB}</div>
            </div>
            <div class="profile-row-item border-b-0 pb-0">
                <div class="profile-row-label"><i class="fas fa-location-dot"></i> Street Address</div>
                <div class="profile-row-value text-slate-700 leading-relaxed">${addressBlock || 'No address recorded'}</div>
            </div>`;
    }

    // ==========================================
    // SRI LANKAN NIC FORM PATTERN ENGINE
    // ==========================================
    // ==========================================
    // OFFICIAL SRI LANKAN NIC PARSING ENGINE
    // ==========================================
    function validateAndUpdateFromNIC(nicValue) {
        // Strip trailing spaces and any trailing English letter for old NICs (V/X)
        let nic = nicValue.trim().toUpperCase();
        if (nic.endsWith('V') || nic.endsWith('X')) {
            nic = nic.slice(0, -1);
        }

        const modalAlert = document.getElementById('modalAlertBox');
        const dobField = document.getElementById('input_CM_DOB');
        const saveBtn = document.getElementById('saveUpdateBtn');

        modalAlert.classList.add('hidden');
        saveBtn.disabled = false;

        if (!nic) {
            if (!customerData.CM_DOB_RAW) {
                dobField.value = '';
                dobField.readOnly = false;
            }
            return;
        }

        let year, dayOfYear;
        const oldNicRegex = /^([0-9]{9})$/;
        const newNicRegex = /^([0-9]{12})$/;

        if (oldNicRegex.test(nic)) {
            year = parseInt("19" + nic.substring(0, 2), 10);
            dayOfYear = parseInt(nic.substring(2, 5), 10);
        } else if (newNicRegex.test(nic)) {
            year = parseInt(nic.substring(0, 4), 10);
            dayOfYear = parseInt(nic.substring(4, 7), 10);
        } else {
            modalAlert.innerHTML = `<i class="fas fa-circle-exclamation mr-1"></i> Invalid Sri Lankan NIC format. Ensure you have entered the 9 numbers (Old) or 12 numbers (New).`;
            modalAlert.classList.remove('hidden');
            saveBtn.disabled = true;
            return;
        }

        // Adjust for female identification index offset
        if (dayOfYear > 500) {
            dayOfYear -= 500;
        }

        if (dayOfYear < 1 || dayOfYear > 366) {
            modalAlert.innerHTML = `<i class="fas fa-circle-exclamation mr-1"></i> Internal NIC index out of bounds. Please crosscheck the number or contact your nearest branch.`;
            modalAlert.classList.remove('hidden');
            saveBtn.disabled = true;
            return;
        }

        // OFFICIAL DRP STANDARD: February is ALWAYS mapped as 29 days 
        // to prevent the "one day forward" offset shift across common years.
        const monthDays = [31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

        let accumulatedDays = 0;
        let month = 0;
        let day = 0;

        for (let i = 0; i < 12; i++) {
            if (dayOfYear <= accumulatedDays + monthDays[i]) {
                month = i + 1;
                day = dayOfYear - accumulatedDays;
                break;
            }
            accumulatedDays += monthDays[i];
        }

        // Edge case fallback protection: handling non-leap year February 29 records if generated
        const realIsLeapYear = (year % 4 === 0 && year % 100 !== 0) || (year % 400 === 0);
        if (month === 2 && day === 29 && !realIsLeapYear) {
            month = 3;
            day = 1;
        }

        // Pad structural parameters cleanly
        const formattedMonth = month < 10 ? '0' + month : month;
        const formattedDay = day < 10 ? '0' + day : day;
        const computedDob = `${year}-${formattedMonth}-${formattedDay}`;

        dobField.value = computedDob;
        dobField.readOnly = true; 
    }

    function openEditModal() {
        const modalAlert = document.getElementById('modalAlertBox');
        if (modalAlert) modalAlert.classList.add('hidden');

        // Fixed the incorrect nullish operator mapping right here:
        document.getElementById('input_CM_CODE').value = customerData.CM_CODE;
        document.getElementById('input_CM_MOBILE').value = customerData.CM_MOBILE;
        document.getElementById('input_CM_NAME').value = customerData.CM_NAME;
        document.getElementById('input_CM_EMAIL').value = customerData.CM_EMAIL;
        document.getElementById('input_CM_ADD1').value = customerData.CM_ADD1;
        document.getElementById('input_CM_ADD2').value = customerData.CM_ADD2;
        document.getElementById('input_CM_ADD3').value = customerData.CM_ADD3;
        document.getElementById('input_CM_ADD4').value = customerData.CM_ADD4;

        const nicField = document.getElementById('input_CM_NIC');
        const dobField = document.getElementById('input_CM_DOB');
        
        nicField.value = customerData.CM_NIC;
        dobField.value = customerData.CM_DOB_RAW;

        if (customerData.CM_NIC && customerData.CM_NIC.trim() !== '') {
            nicField.readOnly = true;
        } else {
            nicField.readOnly = false;
        }

        if (customerData.CM_DOB_RAW && customerData.CM_DOB_RAW.trim() !== '') {
            dobField.readOnly = true;
        } else {
            if(nicField.value) {
                validateAndUpdateFromNIC(nicField.value);
            } else {
                dobField.readOnly = false;
            }
        }
        
        document.getElementById('editVaultModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editVaultModal').style.display = 'none';
        document.getElementById('globalAlert').style.display = 'none';
        document.getElementById('saveUpdateBtn').disabled = false;
    }

    function commitCustomerUpdates() {
        const saveBtn = document.getElementById('saveUpdateBtn');
        const modalAlert = document.getElementById('modalAlertBox');
        
        const payload = {
            CM_CODE: document.getElementById('input_CM_CODE').value,
            CM_NAME: document.getElementById('input_CM_NAME').value,
            CM_NIC: document.getElementById('input_CM_NIC').value,
            CM_EMAIL: document.getElementById('input_CM_EMAIL').value,
            CM_DOB: document.getElementById('input_CM_DOB').value,
            CM_ADD1: document.getElementById('input_CM_ADD1').value,
            CM_ADD2: document.getElementById('input_CM_ADD2').value,
            CM_ADD3: document.getElementById('input_CM_ADD3').value,
            CM_ADD4: document.getElementById('input_CM_ADD4').value
        };

        if(!payload.CM_NAME.trim()) {
            modalAlert.innerText = "Full Name field validation failure: Input context missing.";
            modalAlert.classList.remove('hidden');
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerText = "Synchronizing Ledger...";

        fetch('update_customer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            saveBtn.disabled = false;
            saveBtn.innerText = "Save Ledger Changes";

            if(data.success) {
                let displayDob = 'Not Provided';
                if(payload.CM_DOB) {
                    const dParts = payload.CM_DOB.split('-');
                    if(dParts.length === 3) displayDob = `${dParts[2]}-${dParts[1]}-${dParts[0]}`;
                }

                customerData = {
                    ...customerData, 
                    ...payload,
                    CM_DOB_RAW: payload.CM_DOB,
                    CM_DOB: displayDob
                };

                processProfileOutput();
                processMetricsOutput(); 
                updateChartInstance();   
                
                closeEditModal();
                triggerSystemToast("Master ledger successfully updated.");
            } else {
                modalAlert.innerText = data.message || "ERP transaction execution fault detected.";
                modalAlert.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error("AJAX Pipeline Interruption Event:", error);
            saveBtn.disabled = false;
            saveBtn.innerText = "Save Ledger Changes";
            modalAlert.innerText = "Pipeline Failure: Data structural handling exception.";
            modalAlert.classList.remove('hidden');
        });
    }

    function updateChartInstance() {
        if (dynamicChartInstance) {
            dynamicChartInstance.data.datasets[0].data = [pointsEarned, pointsRedeemed, pointsAvailable];
            dynamicChartInstance.update();
        }
    }

    function renderChartInstance() {
        const canvas = document.getElementById('pointsChart');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        
        dynamicChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Earned Total', 'Redeemed Total', 'Available Vault Balance'],
                datasets: [{
                    data: [pointsEarned, pointsRedeemed, pointsAvailable],
                    backgroundColor: ['#e11d48', '#64748b', '#b45309'],
                    borderWidth: 0,
                    cutout: '78%',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom', 
                        labels: { 
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, 
                            padding: 16,
                            color: '#475569',
                            usePointStyle: true
                        } 
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        processMetricsOutput();
        processProfileOutput();
        renderChartInstance();
        triggerSystemToast("Vault connection established.");
    });
</script>
</body>
</html>