<?php
// index.php
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>EQ Test — Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    /* internal CSS - attractive design */
    :root{--bg:#0f1724;--card:#0b2636;--accent:#2dd4bf;--muted:#9fb6c0;--glass:rgba(255,255,255,0.03)}
    html,body{height:100%;margin:0;font-family:Inter,system-ui,Arial}
    body{background:linear-gradient(180deg,#06121a,#072c3a);display:flex;align-items:center;justify-content:center;color:#e6fbff}
    .container{width:920px;max-width:92%;padding:28px;border-radius:18px;background:linear-gradient(180deg,rgba(255,255,255,0.02),transparent);box-shadow:0 10px 30px rgba(2,6,23,0.6);border:1px solid rgba(255,255,255,0.03)}
    header{display:flex;align-items:center;gap:20px}
    .logo{width:72px;height:72px;border-radius:12px;background:linear-gradient(135deg,var(--accent),#0369a1);display:flex;align-items:center;justify-content:center;font-weight:700;color:#052226;font-size:20px;box-shadow:0 6px 18px rgba(45,212,191,0.12)}
    h1{margin:0;font-size:28px}
    p.lead{color:var(--muted);margin-top:10px;line-height:1.5}
    .hero{display:flex;justify-content:space-between;gap:20px;margin-top:24px;align-items:center}
    .card{background:var(--card);padding:20px;border-radius:14px;flex:1;box-shadow:inset 0 1px 0 rgba(255,255,255,0.02)}
    .cta{background:linear-gradient(90deg,var(--accent),#06b6d4);padding:14px 18px;border-radius:12px;border:none;font-weight:700;cursor:pointer;color:#022;box-shadow:0 10px 30px rgba(3,105,161,0.12);font-size:16px}
    .info{color:var(--muted);margin-top:12px}
    footer{margin-top:18px;color:rgba(255,255,255,0.06);font-size:12px;text-align:right}
    @media (max-width:720px){.hero{flex-direction:column}}
  </style>
</head>
<body>
  <div class="container">
    <header>
      <div class="logo">EQ</div>
      <div>
        <h1>EQ / IQ Test — Measure your reasoning</h1>
        <p class="lead">A quick test to assess pattern recognition, numerical reasoning and logical thinking. This is for practice and entertainment.</p>
      </div>
    </header>
 
    <div class="hero">
      <div class="card">
        <h3>What you'll get</h3>
        <ul style="color:var(--muted);margin-top:8px;line-height:1.6">
          <li>A short 10-question test</li>
          <li>Instant IQ-like score and personalized feedback</li>
          <li>Option to retake and share your result</li>
        </ul>
        <p class="info">Estimated time: ~5 minutes. No login required.</p>
      </div>
 
      <div style="width:280px;display:flex;flex-direction:column;gap:12px;align-items:center;justify-content:center">
        <div style="background:var(--glass);padding:18px;border-radius:12px;width:100%;text-align:center">
          <div style="font-size:22px;font-weight:700">Ready?</div>
          <div style="color:var(--muted);margin-top:6px">Start the test and know your score</div>
        </div>
        <button id="startBtn" class="cta">Start Test</button>
      </div>
    </div>
 
    <footer>Made with ♥ • EQ Test Clone</footer>
  </div>
 
<script>
document.getElementById('startBtn').addEventListener('click', function(){
  // JS redirection to quiz page
  window.location.href = 'quiz.php';
});
</script>
</body>
</html>
 
