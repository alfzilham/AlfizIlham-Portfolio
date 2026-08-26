<?php
/** Builds SQLite knowledge chunks from authoritative portfolio rows. */
class KnowledgeIndexService
{
    public static function syncShowcase(array $card) { return self::sync('project', $card['id'], trim($card['title'] . "\n" . $card['description'] . (!empty($card['link']) ? "\nLive link: " . $card['link'] : '')), ['title'=>$card['title'], 'description'=>$card['description'], 'link'=>$card['link'] ?? null, 'image'=>$card['image'] ?? null]); }
    public static function syncCertificate(array $certificate) { return self::sync('certificate', $certificate['id'], trim($certificate['title'] . "\nIssuer: " . ($certificate['company'] ?? '') . "\nCredential ID: " . ($certificate['credential_id'] ?? '')), ['title'=>$certificate['title'], 'company'=>$certificate['company'] ?? null, 'credential_id'=>$certificate['credential_id'] ?? null, 'credential_link'=>$certificate['credential_link'] ?? null, 'image'=>$certificate['image'] ?? null]); }
    public static function delete($type, $id) { try { RagService::delete($type, $id); return ['ok'=>true]; } catch (Throwable $e) { error_log('Knowledge delete failed: '.$e->getMessage()); return ['ok'=>false, 'warning'=>'Knowledge index update failed.']; } }
    private static function sync($type, $id, $content, array $metadata) { try { RagService::sync($type, $id, $content, $metadata, EmbeddingService::embed($content, 'document')); return ['ok'=>true]; } catch (Throwable $e) { error_log('Knowledge sync failed: '.$e->getMessage()); return ['ok'=>false, 'warning'=>'Knowledge index update failed.']; } }
}
