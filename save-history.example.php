<?php
// Quantum Ethic Air Quality Map — receives the history built in the browser (build-history.html)
// and writes it to history.js in this folder. Only valid JSON data is ever written, never code.
$TOKEN = 'CHANGE-ME';
header('Content-Type: application/json');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['ok'=>false,'error'=>'POST only']); exit; }
$hdr = isset($_SERVER['HTTP_X_QE_TOKEN']) ? $_SERVER['HTTP_X_QE_TOKEN'] : '';
if (!hash_equals($TOKEN, $hdr)) { http_response_code(403); echo json_encode(['ok'=>false,'error'=>'bad token']); exit; }
$raw = file_get_contents('php://input', false, null, 0, 8*1024*1024);
$data = json_decode($raw, true);
if (!is_array($data) || !isset($data['dates'],$data['grid'],$data['places']) || !is_array($data['dates']) || count($data['dates'])<1) {
  http_response_code(400); echo json_encode(['ok'=>false,'error'=>'not a history object']); exit;
}
$js = 'window.QE_HISTORY=' . json_encode($data) . ';';
$ok = file_put_contents(__DIR__ . '/history.js', $js, LOCK_EX);
if ($ok === false) { http_response_code(500); echo json_encode(['ok'=>false,'error'=>'could not write history.js (folder permissions)']); exit; }
echo json_encode(['ok'=>true,'bytes'=>$ok,'days'=>count($data['dates']),'start'=>$data['start'],'end'=>$data['end']]);
