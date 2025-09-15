<?php

namespace App\Traits;

use SimpleXMLElement;
use Carbon\Carbon;

trait XmlInterpreter
{
    protected function loadXml(string $path): SimpleXMLElement
    {
        if (!is_file($path)) {
            throw new \RuntimeException("XML non trovato: {$path}");
        }

        $raw = file_get_contents($path);

        $cleaned = preg_replace('/[^\x09\x0A\x0D\x20-\x7F]/u', '', $raw);

        if (strlen($raw) !== strlen($cleaned)) {
            $removed = strlen($raw) - strlen($cleaned);
            $this->warn("⚠️  Rimossi {$removed} caratteri non validi dall'XML");
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($cleaned);

        if ($xml === false) {
            $errs = array_map(fn($e) => trim($e->message), libxml_get_errors());
            libxml_clear_errors();
            throw new \RuntimeException("Errore parsing XML {$path}: " . implode(' | ', $errs));
        }

        return $xml;
    }

    protected function records(SimpleXMLElement $xml, string $tag = 'record'): \Generator
    {
        if (!isset($xml->$tag)) {
            return; // nessun record
        }
        foreach ($xml->$tag as $record) {
            yield $record;
        }
    }

    /**
     * Estrae un valore da un nodo XML indifferentemente da
     * attributo (<record Campo="x"/>) o child (<record><Campo>x</Campo></record>)
     */
    protected function xmlValue(SimpleXMLElement $record, string $key, $default = null): ?string
    {
        // 1) attributo
        if (isset($record[$key])) {
            $val = (string) $record[$key];
            $val = $this->normalize($val);
            return $val === '' ? $default : $val;
        }

        // 2) child
        if (isset($record->$key)) {
            // se multipli, prendi il primo
            $node = $record->$key[0];
            $val  = (string) $node;
            $val  = $this->normalize($val);
            return $val === '' ? $default : $val;
        }

        return $default;
    }

    /** Stringa normalizzata (trim + spazi interni compressi opzionale) */
    protected function xmlStr(SimpleXMLElement $record, string $key, ?string $default = null, bool $collapseSpaces = true): ?string
    {
        $v = $this->xmlValue($record, $key, $default);
        if ($v === null) return null;
        if ($collapseSpaces) {
            $v = preg_replace('/\s+/u', ' ', $v ?? '');
        }
        return $v === '' ? $default : $v;
    }

    /** Intero sicuro */
    protected function xmlInt(SimpleXMLElement $record, string $key, ?int $default = null): ?int
    {
        $v = $this->xmlValue($record, $key, null);
        if ($v === null || $v === '') return $default;
        if (is_numeric($v)) return (int) round((float) $v);
        return $default;
    }

    /** Float robusto (gestisce 1.234,56 e 1234,56 e 1234.56) */
    protected function xmlFloat(SimpleXMLElement $record, string $key, ?float $default = null): ?float
    {
        $v = $this->xmlValue($record, $key, null);
        if ($v === null || $v === '') return $default;

        $s = $v;

        // pattern tipo "1.234,56"
        if (preg_match('/^\d{1,3}(\.\d{3})+,\d+$/', $s)) {
            $s = str_replace('.', '', $s);
            $s = str_replace(',', '.', $s);
        }
        // pattern tipo "1234,56"
        elseif (preg_match('/^\d+,\d+$/', $s)) {
            $s = str_replace(',', '.', $s);
        }
        // altrimenti lasciamo "1234.56" com'è

        return is_numeric($s) ? (float) $s : $default;
    }

    /**
     * Data: prova più formati, ritorna stringa "Y-m-d" (o null).
     * Se vuoi un Carbon, passa $asCarbon = true.
     */
    protected function xmlDate(SimpleXMLElement $record, string $key, bool $asCarbon = false, ?string $tz = 'Europe/Rome')
    {
        $v = $this->xmlValue($record, $key, null);
        if (!$v) return null;

        $formats = [
            'Y-m-d',
            'Y-m-d H:i:s',
            'd/m/Y',
            'd/m/Y H:i:s',
            'Ymd',
        ];

        foreach ($formats as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $v, $tz);
                if ($dt !== false) {
                    return $asCarbon ? $dt : $dt->format('Y-m-d');
                }
            } catch (\Throwable $e) {
                // tenta prossimo formato
            }
        }

        // fallback al parser libero
        try {
            $dt = Carbon::parse($v, $tz);
            return $asCarbon ? $dt : $dt->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    /** Util: trim base */
    protected function normalize(?string $s): string
    {
        $s = (string) ($s ?? '');
        // rimuovi controlli, normalizza newline/tabs in spazi
        $s = str_replace(["\r", "\n", "\t"], ' ', $s);
        return trim($s);
    }
}
