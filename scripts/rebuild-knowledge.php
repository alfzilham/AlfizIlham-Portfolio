<?php
/** Rebuild the derived Neon knowledge index from SQLite. Run: php scripts/rebuild-knowledge.php */
if (PHP_SAPI !== 'cli') exit("CLI only\n");
require dirname(__DIR__) . '/bootstrap.php';
if (!RagService::isConfigured()) exit("Neon or pdo_pgsql is not configured.\n");
RagService::ensureSchema();
foreach (ShowcaseProject::all() as $card) { NeonSyncService::syncShowcase($card); echo "Synced project #{$card['id']}\n"; }
foreach (Certificate::all() as $certificate) { NeonSyncService::syncCertificate($certificate); echo "Synced certificate #{$certificate['id']}\n"; }
echo "Knowledge rebuild complete.\n";
