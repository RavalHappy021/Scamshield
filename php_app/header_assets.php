<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<!-- Global Styles (Combined from Navbar & Index) -->
<style>
    :root {
        --primary-bg: #0f2027;
        --nav-bg: rgba(15, 32, 39, 0.85);
        --accent-blue: #00d2ff;
        --accent-gradient: linear-gradient(135deg, #00d2ff, #3a7bd5);
        --glass-bg: rgba(255, 255, 255, 0.05);
        --glass-border: rgba(255, 255, 255, 0.1);
    }

    body {
        background-color: var(--primary-bg);
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        overflow-x: hidden;
    }

    /* Navbar Global Styles */
    .navbar {
        background: var(--nav-bg) !important;
        backdrop-filter: blur(15px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 15px 0;
        transition: all 0.3s;
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        letter-spacing: -0.5px;
        background: var(--accent-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .nav-link {
        font-weight: 500;
        margin: 0 5px;
        transition: color 0.3s;
    }

    .nav-link:hover {
        color: var(--accent-blue) !important;
    }

    .btn-auth {
        background: var(--accent-gradient);
        border: none !important;
        border-radius: 50px;
        padding: 8px 20px;
        font-weight: 600;
        color: white !important;
    }

    .welcome-box {
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        background: var(--accent-gradient);
        padding: 8px 18px;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(0, 210, 255, 0.3);
        margin-right: 15px;
        cursor: pointer;
        display: inline-block;
    }
</style>
