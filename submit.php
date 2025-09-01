<?php
// submit.php
require 'config.php';
 
// only accept JSON POST
$inp = file_get_contents('php://input');
if(!$inp){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'No input']);
    exit;
}
$data = json_decode($inp, true);
if(!$data || !isset($data['answers'])){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Invalid payload']);
    exit;
}
$answers = $data['answers']; // array mapping qid => oid
$duration = isset($data['duration_seconds']) ? (int)$data['duration_seconds'] : 0;
 
// fetch correct answers
$stmt = $pdo->query("SELECT question_id, id AS oid, is_correct FROM options WHERE is_correct=1");
$correct = [];
while($r = $stmt->fetch()){
    $correct[$r['question_id']] = $r['oid'];
}
$total_questions = count($correct);
$correct_count = 0;
foreach($correct as $qid => $oid){
    if(isset($answers[$qid]) && intval($answers[$qid]) === intval($oid)){
        $correct_count++;
    }
}
 
// Simple IQ-like scaling: IQ = 70 + (correct/total) * 50
$iq = 70 + round(($correct_count / max(1,$total_questions)) * 50);
if($iq < 55) $iq = 55;
if($iq > 145) $iq = 145;
 
// feedback generation (brief)
$percent = round(($correct_count / max(1,$total_questions)) * 100);
if($percent >= 85){
    $feedback = "Excellent reasoning and problem solving.";
} elseif($percent >= 65){
    $feedback = "Strong performance. Good logical skills.";
} elseif($percent >= 40){
    $feedback = "Average performance. Some improvement areas.";
} else {
    $feedback = "Below average. Practice pattern recognition and basic logic.";
}
 
// Save result
$details = [
    'answers' => $answers,
    'duration_seconds' => $duration,
    'percent' => $percent,
];
$ins = $pdo->prepare("INSERT INTO results (name, correct_count, total_questions, iq_score, details) VALUES (?, ?, ?, ?, ?)");
$name = 'Guest';
$ins->execute([$name, $correct_count, $total_questions, $iq, json_encode($details)]);
$attempt_id = $pdo->lastInsertId();
 
header('Content-Type: application/json');
echo json_encode(['success'=>true,'attempt_id'=>$attempt_id,'iq'=>$iq,'feedback'=>$feedback]);
 
