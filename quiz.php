<?php
// quiz.php
require 'config.php';
 
// fetch questions and options
$stmt = $pdo->query("SELECT q.id AS qid, q.qtext, o.id AS oid, o.otext
                     FROM questions q
                     JOIN options o ON o.question_id = q.id
                     ORDER BY q.id, o.id");
$rows = $stmt->fetchAll();
 
$questions = [];
foreach($rows as $r){
    $qid = $r['qid'];
    if(!isset($questions[$qid])){
        $questions[$qid] = [
            'id'=>$qid,
            'qtext'=>$r['qtext'],
            'options'=>[]
        ];
    }
    $questions[$qid]['options'][] = ['id'=>$r['oid'],'otext'=>$r['otext']];
}
$questions = array_values($questions);
$total = count($questions);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>EQ Test — Quiz</title>
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <style>
    :root{--bg:#071821;--card:#082a36;--accent:#ffd166;--muted:#9fb6c0}
    body{margin:0;font-family:Inter,system-ui,Arial;background:linear-gradient(180deg,#03202a,#051b2a);color:#eaf6ff}
    .wrap{max-width:980px;margin:22px auto;padding:20px;border-radius:16px;background:linear-gradient(180deg,rgba(255,255,255,0.02),transparent);box-shadow:0 20px 50px rgba(2,6,23,0.6)}
    header{display:flex;align-items:center;justify-content:space-between}
    h2{margin:0}
    .progress{color:var(--muted)}
    .question{margin-top:18px;padding:18px;border-radius:12px;background:var(--card);box-shadow:inset 0 1px 0 rgba(255,255,255,0.02)}
    .options{margin-top:10px;display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .opt{padding:12px;border-radius:10px;border:1px solid rgba(255,255,255,0.03);cursor:pointer;user-select:none}
    .opt.checked{outline:3px solid rgba(255,209,102,0.12);background:linear-gradient(90deg,rgba(255,209,102,0.06),transparent)}
    .nav{display:flex;justify-content:space-between;margin-top:18px}
    button.btn{padding:10px 14px;border-radius:10px;border:none;cursor:pointer;font-weight:700}
    button.primary{background:linear-gradient(90deg,var(--accent),#ffb4a2);color:#052;box-shadow:0 10px 24px rgba(255,177,99,0.08)}
    button.ghost{background:transparent;color:var(--muted);border:1px solid rgba(255,255,255,0.03)}
    .center{display:flex;gap:8px;align-items:center}
    @media(max-width:720px){.options{grid-template-columns:1fr}}
  </style>
</head>
<body>
  <div class="wrap">
    <header>
      <div>
        <h2>EQ / IQ Test</h2>
        <div class="progress">Questions: <span id="current">1</span> / <?php echo $total; ?></div>
      </div>
      <div class="center">
        <div style="color:var(--muted);margin-right:10px">Time</div>
        <div id="timer" style="font-weight:700">00:00</div>
      </div>
    </header>
 
    <main id="mainArea">
      <!-- JS will inject questions -->
    </main>
 
    <div class="nav">
      <div>
        <button id="prevBtn" class="btn ghost">Previous</button>
      </div>
      <div>
        <button id="submitBtn" class="btn primary">Submit & See Result</button>
        <button id="nextBtn" class="btn ghost">Next</button>
      </div>
    </div>
  </div>
 
<script>
const questions = <?php echo json_encode($questions, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT); ?>;
let idx = 0;
const total = questions.length;
const answers = {}; // store option id by question id
const startTime = Date.now();
let timerInterval = null;
 
function startTimer(){
  timerInterval = setInterval(()=>{
    const diff = Math.floor((Date.now()-startTime)/1000);
    const mm = String(Math.floor(diff/60)).padStart(2,'0');
    const ss = String(diff%60).padStart(2,'0');
    document.getElementById('timer').textContent = mm + ':' + ss;
  },1000);
}
 
function render(){
  document.getElementById('current').textContent = idx+1;
  const q = questions[idx];
  const main = document.getElementById('mainArea');
  let html = `<div class="question"><div style="font-weight:700">Q${idx+1}. ${escapeHtml(q.qtext)}</div><div class="options">`;
  for(const o of q.options){
    const chosen = answers[q.id] == o.id ? 'checked' : '';
    html += `<div class="opt ${chosen}" data-q="${q.id}" data-o="${o.id}">${escapeHtml(o.otext)}</div>`;
  }
  html += `</div></div>`;
  main.innerHTML = html;
  attachOptionEvents();
  updateNav();
}
 
function attachOptionEvents(){
  document.querySelectorAll('.opt').forEach(el=>{
    el.onclick = ()=>{
      const qid = el.getAttribute('data-q');
      const oid = el.getAttribute('data-o');
      answers[qid] = oid;
      // refresh to highlight
      render();
    };
  });
}
 
function updateNav(){
  document.getElementById('prevBtn').disabled = (idx===0);
  document.getElementById('nextBtn').disabled = (idx===total-1);
}
 
function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m];}); }
 
document.getElementById('nextBtn').addEventListener('click', ()=>{ if(idx<total-1){ idx++; render(); } });
document.getElementById('prevBtn').addEventListener('click', ()=>{ if(idx>0){ idx--; render(); } });
 
document.getElementById('submitBtn').addEventListener('click', ()=>{
  if(Object.keys(answers).length < total){
    if(!confirm('You have unanswered questions. Submit anyway?')) return;
  }
  // Prepare payload
  const payload = {
    answers: answers,
    duration_seconds: Math.floor((Date.now()-startTime)/1000)
  };
  // send to submit.php via fetch (AJAX)
  fetch('submit.php', {
    method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  }).then(r=>r.json()).then(data=>{
    if(data && data.success){
      // redirect using JS with attempt id so results page can fetch
      window.location.href = 'results.php?attempt=' + encodeURIComponent(data.attempt_id);
    } else {
      alert('Error submitting test: ' + (data.message || 'unknown'));
    }
  }).catch(e=>{
    alert('Submission failed: ' + e);
  });
});
 
startTimer();
render();
</script>
</body>
</html>
