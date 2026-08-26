<?php
/** Rebuild the SQLite knowledge index from authoritative portfolio data. */
if (PHP_SAPI !== 'cli') exit("CLI only\n");
require dirname(__DIR__) . '/bootstrap.php';
KnowledgeChunk::ensureTable();
$ok = 0; $failed = 0;
foreach (ShowcaseProject::all() as $card) { $result = KnowledgeIndexService::syncShowcase($card); $result['ok'] ? $ok++ : $failed++; echo "Project #{$card['id']}: " . ($result['ok'] ? "indexed" : "failed") . "\n"; }
foreach (Certificate::all() as $certificate) { $result = KnowledgeIndexService::syncCertificate($certificate); $result['ok'] ? $ok++ : $failed++; echo "Certificate #{$certificate['id']}: " . ($result['ok'] ? "indexed" : "failed") . "\n"; }
echo "Knowledge rebuild complete: {$ok} indexed, {$failed} failed.\n";
exit($failed > 0 ? 1 : 0);
