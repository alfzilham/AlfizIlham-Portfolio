<?php
/** Translates SQLite showcase/certificate rows into retrievable knowledge chunks. */
class NeonSyncService
{
    public static function syncShowcase(array $card)
    {
        self::sync('project', $card['id'], trim($card['title'] . "\n" . $card['description'] . (!empty($card['link']) ? "\nLive link: " . $card['link'] : '')), [
            'title' => $card['title'], 'description' => $card['description'], 'link' => $card['link'] ?? null, 'image' => $card['image'] ?? null,
        ]);
    }

    public static function syncCertificate(array $certificate)
    {
        self::sync('certificate', $certificate['id'], trim($certificate['title'] . "\nIssuer: " . ($certificate['company'] ?? '') . "\nCredential ID: " . ($certificate['credential_id'] ?? '')), [
            'title' => $certificate['title'], 'company' => $certificate['company'] ?? null,
            'credential_id' => $certificate['credential_id'] ?? null, 'credential_link' => $certificate['credential_link'] ?? null,
            'image' => $certificate['image'] ?? null,
        ]);
    }

    public static function delete($type, $id)
    {
        if (!RagService::isConfigured()) return ['ok' => false, 'warning' => 'Knowledge index is not configured.'];
        try { RagService::delete($type, $id); return ['ok' => true]; }
        catch (Throwable $e) { error_log('Neon delete sync failed: ' . $e->getMessage()); return ['ok' => false, 'warning' => 'Knowledge index sync failed.']; }
    }

    private static function sync($type, $id, $content, $metadata)
    {
        if (!RagService::isConfigured()) return ['ok' => false, 'warning' => 'Knowledge index is not configured.'];
        try {
            RagService::sync($type, $id, $content, $metadata, EmbeddingService::embed($content, 'document'));
            return ['ok' => true];
        } catch (Throwable $e) {
            error_log('Neon sync failed: ' . $e->getMessage());
            return ['ok' => false, 'warning' => 'Knowledge index sync failed.'];
        }
    }
}
