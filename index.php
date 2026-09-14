<?php
// index.php - ASB Fashion Customer Portal Landing Page

// Smart Search Engine Crawler Detection
function isSearchEngineBot() {
    $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $botKeywords = [
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 
        'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver', 'twitterbot'
    ];
    
    foreach ($botKeywords as $bot) {
        if (strpos($userAgent, $bot) !== false) {
            return true;
        }
    }
    return isset($_GET['seo_bot']);
}

// Redirect real users to login while allowing SEO spiders to index the structured landing page
if (!isSearchEngineBot()) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://customer.asbfashion.com/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Primary SEO Meta Tags -->
    <title>ASB Customer Portal | Loyal Vault Login</title>
    <meta name="title" content="ASB Fashion Customer Portal | Glamour Gate & Loyal Vault Login">
    <meta name="description" content="Official ASB Fashion Customer Portal. VIP login for ASB Glamour Gate members and Loyal Vault rewards. Find ASB showroom branches in Panadura, Matara, Negombo, Anuradhapura, Tangalle, and islandwide in Sri Lanka.">
    <meta name="keywords" content="ASB Fashion customer login, ASB Glamour Gate, ASB Loyal Vault, ASB loyalty rewards, ASB fashion portal, Sri Lanka leading clothing retail brand, ASB clothing network, family fashion store Sri Lanka, Ampara, Anuradhapura, Matara, Kalutara, Panadura, Aluthgama, Mathugama, Tangalle, Monaragala, Kuliyapitiya, Warakapola, Balangoda, Negombo, Chilaw, Ambalangoda shopping, VIP fashion rewards Sri Lanka">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <meta name="author" content="ASB Group of Companies Sri Lanka">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://customer.asbfashion.com/">

    <!-- Branding & Favicon Configuration -->
    <link rel="icon" type="image/png" sizes="32x32" href="logo.png">
    <link rel="icon" type="image/png" sizes="16x16" href="logo.png">
    <link rel="apple-touch-icon" href="logo.png">

    <!-- Browser & OS Theme -->
    <meta name="theme-color" content="#0f172a">
    <meta name="color-scheme" content="dark">

    <!-- Performance Optimization Preconnects -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://customer.asbfashion.com/">
    <meta property="og:site_name" content="ASB Fashion">
    <meta property="og:title" content="ASB Fashion Customer Portal | Glamour Gate & Loyal Vault Login">
    <meta property="og:description" content="Log in to your ASB Fashion account. Exclusive rewards gateway for loyal customers and Glamour Gate VIP members across Sri Lanka.">
    <meta property="og:image" content="https://customer.asbfashion.com/logo.png">

    <!-- Twitter Card -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://customer.asbfashion.com/">
    <meta property="twitter:title" content="ASB Fashion Customer Portal | Glamour Gate Rewards">
    <meta property="twitter:description" content="Secure account access to the ASB Loyal Vault for Sri Lanka's leading clothing retail brand.">
    <meta property="twitter:image" content="https://customer.asbfashion.com/logo.png">

    <!-- Google Site Verification -->
    <meta name="google-site-verification" content="MsH6lYGpBC66xWDxV-JnBu07VY3HaIUtJiKr5pYSyck">

    <!-- Tailwind CSS Engine -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-glow {
            background: radial-gradient(circle at 50% 0%, rgba(245, 158, 11, 0.12) 0%, rgba(15, 23, 42, 0) 70%);
        }
    </style>

    <!-- Deep Local SEO Schema (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@graph": [
        {
          "@type": "ClothingStore",
          "@id": "https://customer.asbfashion.com/#store",
          "name": "ASB Fashion",
          "alternateName": ["ASB Fashions", "ASB Glamour Gate", "ASB Loyal Vault"],
          "url": "https://customer.asbfashion.com/",
          "logo": "https://customer.asbfashion.com/logo.png",
          "image": "https://customer.asbfashion.com/logo.png",
          "description": "ASB Fashion is Sri Lanka's premier clothing retail chain. The official customer hub provides secure access to ASB Glamour Gate memberships and ASB Loyal Vault rewards across all national retail outlets.",
          "priceRange": "$$",
          "telephone": "+94112345678",
          "address": {
            "@type": "PostalAddress",
            "addressLocality": "Panadura",
            "addressRegion": "Western Province",
            "addressCountry": "LK"
          },
          "hasMap": "https://www.google.com/maps",
          "areaServed": [
            {"@type": "AdministrativeArea", "name": "Ampara"},
            {"@type": "AdministrativeArea", "name": "Anuradhapura"},
            {"@type": "AdministrativeArea", "name": "Matara"},
            {"@type": "AdministrativeArea", "name": "Kalutara"},
            {"@type": "AdministrativeArea", "name": "Panadura"},
            {"@type": "AdministrativeArea", "name": "Aluthgama"},
            {"@type": "AdministrativeArea", "name": "Mathugama"},
            {"@type": "AdministrativeArea", "name": "Tangalle"},
            {"@type": "AdministrativeArea", "name": "Monaragala"},
            {"@type": "AdministrativeArea", "name": "Kuliyapitiya"},
            {"@type": "AdministrativeArea", "name": "Warakapola"},
            {"@type": "AdministrativeArea", "name": "Balangoda"},
            {"@type": "AdministrativeArea", "name": "Negombo"},
            {"@type": "AdministrativeArea", "name": "Chilaw"},
            {"@type": "AdministrativeArea", "name": "Ambalangoda"}
          ]
        },
        {
          "@type": "WebSite",
          "@id": "https://customer.asbfashion.com/#website",
          "url": "https://customer.asbfashion.com/",
          "name": "ASB Fashion Customer Hub",
          "publisher": {"@id": "https://customer.asbfashion.com/#store"}
        }
      ]
    }
    </script>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between antialiased selection:bg-amber-400 selection:text-slate-950 bg-glow">

    <!-- Navbar Header -->
    <header class="w-full border-b border-slate-800/80 backdrop-blur-md bg-slate-950/60 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="https://customer.asbfashion.com/" class="flex items-center gap-2 group">
                <span class="text-2xl font-black tracking-wider text-white uppercase group-hover:text-amber-400 transition">ASB <span class="text-amber-400">FASHION</span></span>
            </a>
            <a href="login.php" class="px-5 py-2 text-xs font-bold uppercase tracking-wider rounded-lg text-slate-950 bg-amber-400 hover:bg-amber-300 transition shadow-md shadow-amber-400/10">
                Portal Login
            </a>
        </div>
    </header>

    <!-- Main Content Section -->
    <main class="flex-grow max-w-5xl mx-auto px-6 py-16 text-center flex flex-col justify-center">
        
        <!-- Header Callout -->
        <div class="space-y-4">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-amber-500/30 bg-amber-500/10 text-amber-400 text-xs font-semibold tracking-wider uppercase">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span> Official Loyalty & Rewards Registry
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-white uppercase leading-none">
                ASB LOYAL <span class="bg-gradient-to-r from-amber-200 via-amber-400 to-amber-500 bg-clip-text text-transparent">VAULT</span>
            </h1>
            <p class="text-lg md:text-xl font-medium tracking-wide text-amber-400/90 max-w-2xl mx-auto uppercase">
                Glamour Gate VIP Portal & Central Rewards Ecosystem
            </p>
            <p class="text-sm md:text-base text-slate-400 max-w-xl mx-auto leading-relaxed">
                Welcome to Sri Lanka's leading family fashion retail network. Access tier benefits, account balances, and localized showroom services seamlessly.
            </p>
        </div>

        <!-- Feature Pillar Cards -->
        <section class="mt-14 grid grid-cols-1 md:grid-cols-3 gap-6 text-left max-w-4xl mx-auto w-full">
            <div class="p-6 bg-slate-900/60 border border-slate-800/80 rounded-2xl backdrop-blur-sm hover:border-amber-500/40 transition duration-300 group">
                <div class="w-10 h-10 rounded-xl bg-amber-400/10 flex items-center justify-center text-amber-400 font-bold mb-4 group-hover:scale-110 transition duration-300">
                    VIP
                </div>
                <h3 class="text-lg font-bold text-white mb-2">ASB Glamour Gate</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Our premier elite access ecosystem. Unlock luxury fashion rewards, early tier seasonal updates, and exclusive privileges designed for trendsetters.
                </p>
            </div>

            <div class="p-6 bg-slate-900/60 border border-slate-800/80 rounded-2xl backdrop-blur-sm hover:border-amber-500/40 transition duration-300 group">
                <div class="w-10 h-10 rounded-xl bg-amber-400/10 flex items-center justify-center text-amber-400 font-bold mb-4 group-hover:scale-110 transition duration-300">
                    ★
                </div>
                <h3 class="text-lg font-bold text-white mb-2">Loyal Customers</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Rewarding millions of families across Sri Lanka. Earn and redeem points instantly across our entire national showroom retail grid.
                </p>
            </div>

            <div class="p-6 bg-slate-900/60 border border-slate-800/80 rounded-2xl backdrop-blur-sm hover:border-amber-500/40 transition duration-300 group">
                <div class="w-10 h-10 rounded-xl bg-amber-400/10 flex items-center justify-center text-amber-400 font-bold mb-4 group-hover:scale-110 transition duration-300">
                    🔒
                </div>
                <h3 class="text-lg font-bold text-white mb-2">ASB Loyal Vault</h3>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Your secure repository for real-time account validation, voucher management, and synchronized purchase history profiles.
                </p>
            </div>
        </section>

        <!-- CTA Gateway -->
        <div class="mt-14 p-8 bg-slate-900/40 border border-slate-800/60 rounded-2xl max-w-2xl mx-auto w-full">
            <p class="text-slate-400 text-xs mb-5">
                Note: This is the official authentication portal for registered customer reward profiles.
            </p>
            <a href="login.php" class="inline-flex items-center justify-center gap-2 px-8 py-3.5 text-sm font-bold uppercase tracking-wider rounded-xl text-slate-950 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 transition-all duration-200 shadow-xl shadow-amber-500/10 transform hover:-translate-y-0.5">
                Enter Secure Account Login &rarr;
            </a>
        </div>

        <!-- Location Keywords & Store Showrooms -->
        <section class="mt-16 border-t border-slate-900 pt-10">
            <h2 class="text-xs font-bold text-slate-400 tracking-widest uppercase mb-6">
                Verified Customer Hub Locations & Regional Showrooms
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-2.5 text-xs text-slate-400">
                <?php 
                $locations = [
                    "Panadura Head Hub", "Anuradhapura City", "Matara Regional", "Kalutara District", "Ampara Hub",
                    "Aluthgama Center", "Mathugama Showroom", "Tangalle Coastal", "Monaragala Outlet", "Kuliyapitiya Town",
                    "Warakapola Center", "Balangoda Store", "Negombo Commercial", "Chilaw Central", "Ambalangoda Store"
                ];
                foreach ($locations as $loc): 
                ?>
                    <div class="p-2.5 bg-slate-900/40 rounded-lg border border-slate-800/60 hover:border-slate-700 transition">
                        <?php echo $loc; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-950 text-slate-500 py-6 text-center text-xs border-t border-slate-900">
        <div class="max-w-5xl mx-auto px-6">
            <p>© <?php echo date('Y'); ?> ASB Fashions Sri Lanka. All Rights Reserved. Glamour Gate & Loyal Vault Portal Infrastructure.</p>
            <p class="mt-1 text-slate-600">Monitored and secured under standard provincial database & security protocols.</p>
        </div>
    </footer>

</body>
</html>