<?php
declare(strict_types=1);
$statusFile = '/var/lib/cluster-ha-nxlog/status.json';
header('Content-Type: text/html; charset=utf-8');
$data = [];
if (is_readable($statusFile)) {
    $decoded = json_decode((string)file_get_contents($statusFile), true);
    if (is_array($decoded)) $data = $decoded;
}
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
$role = $data['role'] ?? 'UNKNOWN';
$active = $role === 'ACTIVE';
?>
<!doctype html>
<html lang="pt-br"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta http-equiv="refresh" content="5"><title>NXLog HA - Status</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#101820;color:#eaf0f4;font:15px Arial,sans-serif}
header{background:#172b3a;padding:22px 5%;border-bottom:3px solid #36c2a1}
h1{margin:0;font-size:25px}main{max-width:1000px;margin:30px auto;padding:0 18px}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:15px}
.card{background:#1b2d3a;border:1px solid #304656;border-radius:12px;padding:20px}
.label{color:#9fb2bf;font-size:12px;text-transform:uppercase;letter-spacing:1px}
.value{font-size:22px;font-weight:bold;margin-top:9px;overflow-wrap:anywhere}
.good{color:#43d6a5}.bad{color:#ff7777}.muted{color:#9fb2bf}
footer{margin-top:25px;color:#8da1af;font-size:12px}
</style></head><body>
<header><h1>NXLog — Cluster HA</h1><div class="muted">Monitoramento local • atualização automática a cada 5 segundos</div></header>
<main>
<div class="grid">
<div class="card"><div class="label">Papel do nó</div><div class="value <?= $active?'good':'muted' ?>"><?=h($role)?></div></div>
<div class="card"><div class="label">Hostname</div><div class="value"><?=h($data['hostname']??'Sem dados')?></div></div>
<div class="card"><div class="label">NXLog</div><div class="value <?=($data['nxlog']??'')==='active'?'good':'bad'?>"><?=h($data['nxlog']??'UNKNOWN')?></div></div>
<div class="card"><div class="label">Keepalived</div><div class="value <?=($data['keepalived']??'')==='active'?'good':'bad'?>"><?=h($data['keepalived']??'UNKNOWN')?></div></div>
<div class="card"><div class="label">VIP</div><div class="value"><?=h($data['vip']??'192.168.15.108')?></div></div>
<div class="card"><div class="label">Listener</div><div class="value" style="font-size:14px"><?=h($data['listener']??'Sem dados')?></div></div>
</div>
<footer>Última coleta: <?=h($data['timestamp']??'Aguardando primeira coleta')?> • O painel exibe o estado deste servidor.</footer>
</main></body></html>
