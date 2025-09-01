<?php
// results.php
require 'config.php';
$attempt = isset($_GET['attempt']) ? intval($_GET['attempt']) : 0;
if(!$attempt){
    echo "No attempt specified.";
    exit;
}
$stmt = $pdo->prepare("SELECT * FROM results WHERE id = ?");
$stmt->execute([$attempt]);
$r = $stmt->fetch();
if(!$r){
    echo "Result not found.";
    exit;
}
 
// Basic feedback mapping (server gave iq_score already)
$iq = intval($r['iq_score']);
$correct = intval($r['correct_count']);
$total = intval($r['total_questions']);
$details = json_decode($r['details'], true);
$percent = isset($details['percent']) ? intval($details['percent']) : round(($correct/$total)*100);
$feedback_text = '';
if($percent >= 85){
    $feedback_text = "Excellent — strong reasoning & pattern recognition.";
} elseif($percent >= 65){
    $feedback_text = "Good — clear strengths, some areas to polish.";
} elseif($percent >= 40){
    $feedback_text = "Average — practice will help improve speed and accuracy.";
} else {
    $feedback_text = "Work on fundamentals: practice puzzles and number series.";
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>EQ Test — Results</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    :root{--bg:#04121b;--card:#07202b;--accent:#7c3aed;--muted:#9fb6c0}
    body{margin:0;font-family:Inter,system-ui,Arial;background:linear-gradient(180deg,#04121b,#07202b);color:#eaf6ff;display:flex;align-items:center;justify-content:center;height:100vh}
    .card{width:860px;max-width:94%;padding:26px;border-radius:16px;background:linear-gradient(180deg,rgba(255,255,255,0.02),transparent);box-shadow:0 20px 60px rgba(2,6,23,0.6)}
    .score{display:flex;gap:20px;align-items:center}
    .bubble{width:140px;height:140px;border-radius:16px;background:linear-gradient(180deg,var(--accent),#5b21b6);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;box-shadow:0 12px 30px rgba(92,33,182,0.12)}
    .meta{color:var(--muted);margin-top:8px}
    .actions{margin-top:18px;display:flex;gap:10px;justify-content:flex-end}
    button.btn{padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:700}
    button.primary{background:linear-gradient(90deg,#7c3aed,#06b6d4);color:#022}
    button.ghost{background:transparent;color:var(--muted);border:1px solid rgba(255,255,255,0.03)}
  </style>
</head>
<body>
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div>
        <h1 style="margin:0">Your Result</h1>
        <div class="meta">Taken at: <?php echo htmlspecialchars($r['taken_at']); ?> • Duration: <?php echo isset($details['duration_seconds']) ? intval($details['duration_seconds']).'s' : 'N/A'; ?></div>
      </div>
      <div style="text-align:right">
        <div style="color:var(--muted)">Correct</div>
        <div style="font-weight:800;font-size:20px"><?php echo $correct . ' / ' . $total; ?></div>
      </div>
    </div>
 
    <div style="margin-top:18px" class="score">
      <div class="bubble"><?php echo $iq; ?></div>
      <div style="flex:1">
        <h2 style="margin-top:0"><?php echo $feedback_text; ?></h2>
        <p style="color:var(--muted)">Your IQ-like score is <strong><?php echo $iq; ?></strong>. This is a simplified estimate based on the short test and for practice/entertainment only.</p>
        <div style="margin-top:10px;color:var(--muted)">
          <strong>Tips:</strong>
          <ul>
            <li>Practice number series & pattern puzzles daily for 10-20 minutes.</li>
            <li>Work on speed and accuracy — time yourself.</li>
            <li>Review wrong answers and understand the reasoning.</li>
          </ul>
        </div>
      </div>
    </div>
 
    <div class="actions">
      <button class="btn ghost" id="retake">Retake Test</button>
      <button class="btn primary" id="share">Share Result</button>
    </div>
  </div>
 
<script>
document.getElementById('retake').addEventListener('click', ()=>{
  // JS redirect to quiz
  window.location.href = 'quiz.php';
});
document.getElementById('share').addEventListener('click', ()=>{
  const text = `I scored <?php echo $iq;?> on this quick EQ test! (<?php echo $correct;?>/<?php echo $total;?>) Try it yourself.`;
  if(navigator.share){
    navigator.share({title:'My EQ result', text: text, url: window.location.href});
  } else {
    navigator.clipboard && navigator.clipboard.writeText(text).then(()=>{
      alert('Result copied to clipboard. Share it anywhere!');
    }, ()=>{ alert('Copy failed — select and copy the text: ' + text);});
  }
});
</script>
</body>
</html>
 
