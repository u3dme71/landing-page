<?php
// ===== BucksCountyCreator.com — Single-file PHP Landing Page =====
// Uses PHPMailer (SMTP) for reliable email delivery + honeypot anti-spam

$success = false; 
$error = '';

// ---------- PHPMailer load ----------
$use_phpmailer = false;
try {
  // Option A: Composer autoload (recommended)
  if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    $use_phpmailer = true;
  } else {
    // Option B: Manual include (drop PHPMailer src folder in same dir)
    $alt1 = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    $alt2 = __DIR__ . '/PHPMailer/src/SMTP.php';
    $alt3 = __DIR__ . '/PHPMailer/src/Exception.php';
    if (file_exists($alt1) && file_exists($alt2) && file_exists($alt3)) {
      require $alt1; require $alt2; require $alt3;
      $use_phpmailer = true;
    }
  }
} catch (Throwable $e) {
  $use_phpmailer = false;
}

// ---------- Handle POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Honeypot
  $hp = trim($_POST['website'] ?? '');
  if ($hp !== '') {
    $error = 'Bad request.'; // bot
  } else {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $phone   = trim($_POST['phone'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $when    = trim($_POST['when'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
      $error = 'Please fill in the required fields.';
    } else {
      $to = 'booking@buckscountycreator.com';
      $subject = 'New Lead — Bucks County Creator';
      $body_txt = "Name: $name\nEmail: $email\nPhone: $phone\nService: $service\nTimeline: $when\n\nMessage:\n$message\n\nSource: ".($_POST['source'] ?? 'Website')."\n";

      // Try PHPMailer if available
      if ($use_phpmailer) {
        try {
          // --- SMTP CONFIG: update these ---
          $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
          $mail->isSMTP();
          $mail->Host       = 'secure.emailsrvr.com';      // e.g. smtp.gmail.com, smtp.sendgrid.net
          $mail->Port       = 587;                      // 587 (TLS) or 465 (SSL)
          $mail->SMTPAuth   = true;
          $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; // or ENCRYPTION_SMTPS for 465
          $mail->Username   = 'booking@buckscountycreator.com';
          $mail->Password   = '****';
          $mail->CharSet    = 'UTF-8';

          $mail->setFrom('booking@buckscountycreator.com', 'BucksCountyCreator');
          $mail->addAddress($to);
          // so you can reply directly
          $mail->addReplyTo($email, $name);

          $mail->Subject = $subject;
          // Simple HTML body + text alt
          $mail->isHTML(true);
          $mail->Body = nl2br(htmlspecialchars($body_txt));
          $mail->AltBody = $body_txt;

          $mail->send();
          $success = true;
        } catch (Throwable $e) {
          $error = 'Message not sent (SMTP error). Please email booking@buckscountycreator.com.';
        }
      } else {
        // Fallback (commented out to force SMTP usage)
        // if (@mail($to, $subject, $body_txt, "From: BucksCountyCreator <no-reply@buckscountycreator.com>\r\nReply-To: $name <$email>\r\n")) {
        //   $success = true;
        // } else {
          $error = 'Mail system not configured. Please email booking@buckscountycreator.com.';
        // }
      }
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Bucks County Creator — Video, Photo & Social Content in PA/NJ</title>
  <meta name="description" content="Bucks County Creator: high‑energy video, photo, and social content for restaurants, bands, real estate & local brands. Book a shoot today.">

  <!-- Favicons (optional) -->
  <link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/favicons/favicon-180x180.png">
  <link rel="icon" type="image/png" sizes="192x192" href="/favicons/favicon-192x192.png">
  <link rel="shortcut icon" href="/favicon.ico">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <!-- GLightbox (lightbox) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css"/>

  <style>
    :root {
      --brand: #0ea5e9;
      --brand-dark: #0369a1;
      --ink: #0f172a;
      --muted: #475569;
      --bg: #0b1220;
      --card: #111827;
      --ring: rgba(14,165,233,.35);
    }
    html, body { font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Arial; background: var(--bg); color: #e5e7eb; }
    .btn-brand { background: linear-gradient(90deg, var(--brand), #22d3ee); border: 0; color: #001018; font-weight: 700; }
    .btn-brand:hover { filter: brightness(1.05); transform: translateY(-1px); }
    .btn-ghost { border:1px solid #334155; color:#e2e8f0; }
    .btn-ghost:hover { border-color:#94a3b8; }
    .navbar { background: rgba(2,6,23,.6); backdrop-filter: blur(8px); }
    .badge-soft { background: rgba(14,165,233,.4); color:#e0faff; border:1px solid rgba(125,211,252,.5); font-weight:600; }
    .hero { position: relative; overflow: hidden; }
    .hero:before { content:""; position:absolute; inset:-20%; background: radial-gradient(60rem 40rem at 10% 10%, rgba(14,165,233,.25), transparent 60%), radial-gradient(40rem 30rem at 90% 20%, rgba(34,211,238,.18), transparent 60%), radial-gradient(30rem 20rem at 40% 80%, rgba(59,130,246,.18), transparent 60%); filter: blur(40px); z-index:0; }
    .glass { background: linear-gradient( to bottom right, rgba(255,255,255,.06), rgba(255,255,255,.03) ); border: 1px solid rgba(148,163,184,.15); box-shadow: 0 10px 30px rgba(2,6,23,.6); }
    .section { padding: 5rem 0; }
    .card { background: var(--card); border:1px solid rgba(148,163,184,.15); }
    .card:hover { border-color: rgba(125,211,252,.35); box-shadow: 0 10px 30px rgba(14,165,233,.15); }
    .ring:focus { box-shadow: 0 0 0 .25rem var(--ring); }
    .logo { font-weight: 800; letter-spacing: .5px; }
    .check { color:#34d399; }
    .shadow-soft { box-shadow: 0 30px 80px rgba(2,6,23,.6); }
    .ratio::before { background:#0b1220; }
    .footer a { color:#9ca3af; }
    .footer a:hover { color:#e5e7eb; }

    /* ===== Contrast tweaks ===== */
    .h3.m-0, .h3.m-0 span { color:#ffffff !important; } /* Prices */
    .card .text-white-50 { color:#d1d5db !important; }  /* Sub-labels */
    #testimonials .card p { color:#e5e7eb; }
    #testimonials .card .small { color:#cbd5e1 !important; font-weight:600; }
    .card .card-title, .card h5 { color:#eef2ff; }

    /* Sticky mobile CTA */
    .sticky-cta {
      position: fixed; left: 0; right: 0; bottom: 12px;
      display: none; /* default hidden, enabled on < lg */
      z-index: 1080;
      padding: 0 16px;
    }
    @media (max-width: 991.98px) {
      .sticky-cta { display:block; }
    }
  </style>

  <!-- OpenGraph / Twitter -->
  <meta property="og:title" content="Bucks County Creator — Video • Photo • Social"/>
  <meta property="og:description" content="High‑energy content that gets you booked. Serving Bucks County, Philly, NJ & beyond."/>
  <meta property="og:type" content="website"/>
  <meta property="og:url" content="https://buckscountycreator.com/"/>
  <meta property="og:image" content="https://buckscountycreator.com/og-cover.jpg"/>
  <meta name="twitter:card" content="summary_large_image"/>

  <!-- LocalBusiness Schema -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "ProfessionalService",
    "name": "Bucks County Creator",
    "url": "https://buckscountycreator.com/",
    "areaServed": ["Bucks County", "Philadelphia", "New Jersey"],
    "sameAs": ["https://www.instagram.com/BucksCountyCreator", "https://www.youtube.com/@BucksCountyCreator"],
    "description": "Video production, photography, and social content for local businesses, bands, and restaurants.",
    "email": "sales@rayjphotos.com"
  }
  </script>
</head>
<body>
  <!-- ===== NAVBAR ===== -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container">
      <a class="navbar-brand text-white logo" href="#top">BucksCounty<span class="text-info">Creator</span></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link text-white-50" href="#work">Work</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="#services">Services</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="#pricing">Pricing</a></li>
          <li class="nav-item"><a class="nav-link text-white-50" href="#contact">Book</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- ===== HERO ===== -->
  <header id="top" class="hero section d-flex align-items-center" style="min-height: 90vh;">
    <div class="container position-relative" style="z-index:1;">
      <div class="row align-items-center g-5">
        <div class="col-lg-7">
          <span class="badge badge-soft rounded-pill px-3 py-2 mb-3">Video • Photo • Social</span>
          <h1 class="display-4 fw-bold lh-tight mb-3">High‑energy content that gets you <span class="text-info">booked</span>.</h1>
          <p class="lead text-white-50">We shoot fast, edit clean, and deliver assets you can use on Instagram, YouTube, and your website. Serving Bucks County, Philly, and NJ.</p>
          <div class="d-flex gap-3 mt-4">
            <a href="#contact" class="btn btn-brand btn-lg px-4 ring">Book a Shoot</a>
            <a href="#work" class="btn btn-ghost btn-lg px-4">See Samples</a>
          </div>
          <div class="d-flex gap-4 mt-4 small text-white-50">
            <div><span class="check me-1">✔</span>24–48h sneak peeks</div>
            <div><span class="check me-1">✔</span>Short‑form ready</div>
            <div><span class="check me-1">✔</span>Licensable music</div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="glass rounded-4 p-3 shadow-soft">
            <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
              <iframe
                src="https://www.youtube.com/embed/Azt_HKOYbgE?rel=0&modestbranding=1&playsinline=1"
                title="Highlight Reel"
                width="560"
                height="315"
                loading="lazy"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
              ></iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ===== WORK ===== -->
<section id="work" class="section">
  <div class="container">
    <div class="d-flex align-items-end justify-content-between mb-4">
      <h2 class="fw-bold m-0">Recent Work</h2>
      <a href="https://www.instagram.com/BucksCountyCreator" class="text-info text-decoration-none" target="_blank" rel="noopener">Follow on Instagram →</a>
    </div>

    <div class="row g-4">
      <!-- Restaurant Event Reel -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="ratio ratio-16x9">
            <iframe
        src="https://www.youtube.com/embed/1JDL6EsUJp0?rel=0&modestbranding=1&playsinline=1&vq=hd1080"
        title="Restaurant Event Reel"
        loading="lazy"
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
        allowfullscreen
      ></iframe>
          </div>
          <div class="card-body">
            <h5 class="card-title">Restaurant Event Reel</h5>
            <p class="card-text text-white-50">
              15s vertical reel designed for Instagram & TikTok. Fast cuts, appetizing close‑ups, and motion text.
            </p>
          </div>
        </div>
      </div>

      <!-- Cafe Promo -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="ratio ratio-16x9">
            <iframe
              src="https://www.youtube.com/embed/_Elu82qOPG0"
              title="Band Promo"
              loading="lazy"
              allowfullscreen
            ></iframe>
          </div>
          <div class="card-body">
            <h5 class="card-title">Cafe Promo</h5>
            <p class="card-text text-white-50">
              High‑energy story telling videos that creates engagement, views and likes.
            </p>
          </div>
        </div>
      </div>
    </div> <!-- end .row -->

<!-- Band Promo -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="ratio ratio-16x9">
            <iframe
              src="https://www.youtube.com/embed/P97HGjlLyVU"
              title="Band Promo"
              loading="lazy"
              allowfullscreen
            ></iframe>
          </div>
          <div class="card-body">
            <h5 class="card-title">Band Promo</h5>
            <p class="card-text text-white-50">
              High‑energy promo cut for booking agents. Multi‑cam performance + crowd shots.
            </p>
          </div>
        </div>
      </div>
    </div> <!-- end .row -->



  </div> <!-- end .container -->
</section>


  <!-- ===== SERVICES ===== -->
  <section id="services" class="section">
    <div class="container">
      <div class="text-center mb-5">
        <h2 class="fw-bold">What We Do</h2>
        <p class="text-white-50">Straightforward packages that create months of content from one shoot.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 p-4">
            <h5 class="mb-3">Restaurant Content Day</h5>
            <ul class="list-unstyled small text-white-50 mb-4">
              <li><span class="check me-2">✔</span>2‑hour shoot (photo + video)</li>
              <li><span class="check me-2">✔</span>20 edited photos</li>
              <li><span class="check me-2">✔</span>6 vertical reels (15–20s)</li>
              <li><span class="check me-2">✔</span>Branding bumper + captions</li>
            </ul>
            <div class="d-flex align-items-baseline gap-2">
              <div class="h3 m-0">$<span>750</span></div>
              <div class="text-white-50">flat</div>
            </div>
            <a href="#contact" class="btn btn-brand mt-3 ring">Book</a>
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
          <div class="card h-100 p-4 border-info" style="border-width:2px;">
            <div class="d-inline-block small mb-2 px-2 py-1 badge-soft rounded">Most Popular</div>
<h5 class="mb-3">Storyboard Reels Package</h5>
    <ul class="list-unstyled small text-white-50 mb-4">
      <li><span class="check me-2">✔</span>Vertical‑only reels — two different cuts from the same shoot (2 revisions)</li>
      <li><span class="check me-2">✔</span>Low+High‑rez for easy sharing</li>
      <li><span class="check me-2">✔</span>30–60s video, edited & color‑corrected</li>
      <li><span class="check me-2">✔</span>Vertical (9:16) formats (MP4)</li>
      <li><span class="check me-2">✔</span>Narration and/or text overlays</li>
      <li><span class="check me-2">✔</span>Licensed background music</li>
      <li><span class="check me-2">✔</span>Exported & optimized for Instagram, TikTok, YouTube Shorts, and Facebook</li>
    </ul>
    <div class="d-flex align-items-baseline gap-2">
      <div class="h3 m-0">$<span>899</span></div>
      <div class="text-white-50">per session</div>
    </div>
    <div class="small text-white-50 mt-2">
      All footage can be reused later to create more 30–60s clips for <strong>$249</strong> each.
    </div>
    <a href="#contact" class="btn btn-brand mt-3 ring">Book</a>            
          </div>
        </div>
        <div class="col-md-6 col-lg-4">
  <div class="card h-100 p-4">
<h5 class="mb-3">Band Promo Package</h5>
            <ul class="list-unstyled small text-white-50 mb-4">
              <li><span class="check me-2">✔</span>2 set live shoot + b‑roll</li>
              <li><span class="check me-2">✔</span>90s promo cut + 3 reels</li>
              <li><span class="check me-2">✔</span>Website header loop (muted)</li>
              <li><span class="check me-2">✔</span>Optional member mini‑reels</li>
            </ul>
            <div class="d-flex align-items-baseline gap-2">
              <div class="h3 m-0">$<span>750</span></div>
              <div class="text-white-50">starting</div>
            </div>
            <a href="#contact" class="btn btn-brand mt-3 ring">Book</a>
  </div>
</div>
      </div>
    </div>
  </section>

  <!-- ===== PRICING BLURB ===== -->
  <section id="pricing" class="section py-0">
    <div class="container">
      <div class="glass rounded-4 p-4 p-md-5 mb-5">
        <div class="row align-items-center g-4">
          <div class="col-lg-8">
            <h3 class="fw-bold mb-2">Need a custom package?</h3>
            <p class="m-0 text-white-50">We can tailor deliverables for launches, menu updates, or tours. Ask about retainer discounts for 4+ shoots/mo.</p>
          </div>
          <div class="col-lg-4 text-lg-end">
            <a href="#contact" class="btn btn-brand ring">Get a Quote</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== TESTIMONIALS ===== -->
  <section id="testimonials" class="section pt-0">
    <div class="container">
      <div class="row g-4">
        <div class="col-md-6">
          <div class="card p-4 h-100">
            <p class="mb-3">“Fast turnaround and the reels boosted our IG reach the same week. We booked two new private events from the promo.”</p>
            <div class="small text-white-50">— Local Band, PA</div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-4 h-100">
            <p class="mb-3">“Food shots look incredible. Short videos perform great on Google and Uber. Will book monthly.”</p>
            <div class="small text-white-50">— Restaurant Owner, NJ</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ===== CONTACT / BOOK ===== -->
  <section id="contact" class="section">
    <div class="container">
      <div class="row g-5 align-items-center">
        <div class="col-lg-6">
          <h2 class="fw-bold">Let’s make content.</h2>
          <p class="text-white-50">Tell us what you need and we’ll reply same day. Prefer DM? <a class="text-info" href="https://www.instagram.com/BucksCountyCreator" target="_blank" rel="noopener">Instagram</a> or <a class="text-info" href="https://www.youtube.com/@BucksCountyCreator" target="_blank" rel="noopener">YouTube</a>.</p>

          <?php if ($success): ?>
            <div class="alert alert-success">Thanks! Your message is in. We’ll reply shortly.</div>
          <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="post" class="row g-3" novalidate>
            <input type="hidden" name="source" value="Landing">
            <div class="col-md-6">
              <label class="form-label">Name *</label>
              <input type="text" name="name" class="form-control ring" placeholder="Your name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control ring" placeholder="you@email.com" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="tel" name="phone" class="form-control ring" placeholder="Optional">
            </div>
            <div class="col-md-6">
              <label class="form-label">Service</label>
              <select class="form-select ring" name="service">
                <option value="">Select…</option>
                <option>Restaurant Content</option>
                <option>Band Promo</option>
                <option>Real Estate</option>
                <option>Other / Custom</option>
              </select>
            </div>
            <div class="col-md-12">
              <label class="form-label">Timeline</label>
              <input type="text" name="when" class="form-control ring" placeholder="E.g., next week, specific date, ASAP">
            </div>
            <div class="col-12">
              <label class="form-label">Project details *</label>
              <textarea name="message" class="form-control ring" rows="5" placeholder="Tell us about your shoot…" required></textarea>
            </div>
            <!-- Honeypot -->
            <div class="d-none">
              <label>Website</label>
              <input type="text" name="website" autocomplete="off">
            </div>
            <div class="col-12 d-flex align-items-center gap-3">
              <button class="btn btn-brand px-4 ring" type="submit">Send Message</button>
              <a href="tel:+1" class="btn btn-ghost">Call Now</a>
            </div>
            <div class="small text-white-50">By sending, you agree to be contacted about your inquiry. No spam—ever.</div>
          </form>
        </div>
        <div class="col-lg-6">
          <div class="glass rounded-4 p-4">
            <h5 class="mb-3">Why clients book us</h5>
            <ul class="list-unstyled m-0">
              <li class="mb-2"><span class="check me-2">✔</span>We plan content around your goals (bookings, reservations, sales)</li>
              <li class="mb-2"><span class="check me-2">✔</span>We deliver multiple formats (9:16, 1:1, 16:9) for each platform</li>
              <li class="mb-2"><span class="check me-2">✔</span>Clear pricing, rights-safe music, and on-time delivery</li>
            </ul>
            <hr class="text-white-50">
            <div class="d-flex gap-3">
              <a href="https://www.instagram.com/BucksCountyCreator" target="_blank" rel="noopener" class="btn btn-ghost">Instagram</a>
              <a href="https://www.youtube.com/@BucksCountyCreator" target="_blank" rel="noopener" class="btn btn-ghost">YouTube</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Sticky CTA (mobile) -->
  <div class="sticky-cta d-lg-none">
    <a href="#contact" class="btn btn-brand btn-lg w-100 ring">Book a Shoot</a>
  </div>

  <!-- ===== FOOTER ===== -->
  <footer class="footer py-4 border-top border-secondary-subtle">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
      <div class="small">© <?php echo date('Y'); ?> Bucks County Creator — Bucks County, PA</div>
      <div class="small">Email: <a href="mailto:sales@rayjphotos.com">sales@rayjphotos.com</a></div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
  <script>
    // Init lightbox
    const lightbox = GLightbox({ selector: '.glightbox' });
  </script>
</body>
</html>

