<?php
// Quantum Ethic Air Quality Map — EPA AirNow monitor layer.
// Fetches the latest hour of monitor observations for the contiguous US from the AirNow API,
// caches them for 15 minutes, and serves a compact JSON to index.html. The API key stays on the server.
// Get a free key at https://docs.airnowapi.org/account/request/ and paste it below.
$KEY = 'CHANGE-ME';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=300');
$cacheFile = __DIR__ . '/airnow-cache.json';
$ttl = 900; // seconds

if ($KEY === 'CHANGE-ME' || $KEY === '') { echo json_encode(['ok'=>false,'error'=>'no key']); exit; }
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $ttl) { readfile($cacheFile); exit; }

$end = gmdate('Y-m-d\TH');
$start = gmdate('Y-m-d\TH', time() - 2*3600);
$url = 'https://www.airnowapi.org/aq/data/?startDate=' . $start . '&endDate=' . $end
     . '&parameters=PM25,OZONE&BBOX=-125,24,-66,50&dataType=A&format=application/json&verbose=1&monitorType=0&includerawconcentrations=0&API_KEY=' . urlencode($KEY);

function fetch_url($url) {
  if (function_exists('curl_init')) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>25, CURLOPT_FOLLOWLOCATION=>true, CURLOPT_USERAGENT=>'QuantumEthicAirQualityMap/1.0']);
    $r = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    return ($r !== false && $code >= 200 && $code < 300) ? $r : false;
  }
  $ctx = stream_context_create(['http'=>['timeout'=>25, 'user_agent'=>'QuantumEthicAirQualityMap/1.0']]);
  return @file_get_contents($url, false, $ctx);
}

$raw = fetch_url($url);
$rows = $raw === false ? null : json_decode($raw, true);
if (!is_array($rows)) {
  if (file_exists($cacheFile)) { readfile($cacheFile); exit; }   // serve stale cache rather than nothing
  echo json_encode(['ok'=>false,'error'=>'AirNow unreachable']); exit;
}

// Keep the latest observation per site per parameter.
$sites = [];
foreach ($rows as $r) {
  if (!isset($r['Latitude'],$r['Longitude'],$r['Parameter'],$r['AQI'],$r['UTC'])) continue;
  $k = round($r['Latitude'],4) . ',' . round($r['Longitude'],4);
  if (!isset($sites[$k])) $sites[$k] = ['lat'=>(float)$r['Latitude'], 'lon'=>(float)$r['Longitude'], 'name'=>isset($r['SiteName'])?$r['SiteName']:'', 'agency'=>isset($r['AgencyName'])?$r['AgencyName']:'', 'utc'=>$r['UTC']];
  $p = strtoupper($r['Parameter']);
  if ($p === 'PM2.5' || $p === 'PM25') $p = 'pm25'; elseif ($p === 'OZONE' || $p === 'O3') $p = 'o3'; else continue;
  if (!isset($sites[$k][$p]) || $r['UTC'] >= $sites[$k]['utc']) { $sites[$k][$p] = (int)$r['AQI']; if ($r['UTC'] > $sites[$k]['utc']) $sites[$k]['utc'] = $r['UTC']; }
}
$out = ['ok'=>true, 'fetched'=>gmdate('c'), 'count'=>count($sites), 'sites'=>array_values($sites)];
$json = json_encode($out);
@file_put_contents($cacheFile, $json, LOCK_EX);
echo $json;
