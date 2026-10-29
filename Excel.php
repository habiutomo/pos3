<?php

namespace App\Services;

use RuntimeException;

class Excel
{
    private const NS_MAIN = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    private const NS_REL_DOC = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

    private const NS_REL_PKG = 'http://schemas.openxmlformats.org/package/2006/relationships';

    public static function read(string $path, ?string $formatHint = null): array
    {
        if ($formatHint === 'csv' || $formatHint === 'txt') {
            return self::readCsv(file_get_contents($path));
        }

        if ($formatHint === 'xlsx') {
            return self::readXlsx(file_get_contents($path));
        }

        $bin = file_get_contents($path);

        if (str_starts_with($bin, 'PK')) {
            return self::readXlsx($bin);
        }

        if (str_starts_with($bin, "\xD0\xCF\x11\xE0")) {
            throw new RuntimeException('Format .xls lama tidak didukung. Simpan ulang file sebagai .xlsx atau .csv');
        }

        return self::readCsv($bin);
    }

    public static function buildXlsx(array $rows): string
    {
        $sheetXml = '';

        foreach (array_values($rows) as $rIdx => $cells) {
            $cellsXml = '';

            foreach (array_values($cells) as $cIdx => $value) {
                if ($value === null || $value === '') {
                    continue;
                }

                $ref = self::colLetter($cIdx).($rIdx + 1);

                if (self::isNumericValue($value)) {
                    $cellsXml .= '<c r="'.$ref.'"><v>'.htmlspecialchars((string) (0 + $value), ENT_XML1 | ENT_QUOTES).'</v></c>';
                } else {
                    $text = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES);
                    $cellsXml .= '<c r="'.$ref.'" t="inlineStr"><is><t xml:space="preserve">'.$text.'</t></is></c>';
                }
            }

            if ($cellsXml !== '') {
                $sheetXml .= '<row r="'.($rIdx + 1).'">'.$cellsXml.'</row>';
            }
        }

        $files = [
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                .'</Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="'.self::NS_REL_PKG.'">'
                .'<Relationship Id="rId1" Type="'.self::NS_REL_DOC.'/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="'.self::NS_MAIN.'" xmlns:r="'.self::NS_REL_DOC.'">'
                .'<sheets><sheet name="Data" sheetId="1" r:id="rId1"/></sheets>'
                .'</workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="'.self::NS_REL_PKG.'">'
                .'<Relationship Id="rId1" Type="'.self::NS_REL_DOC.'/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="'.self::NS_REL_DOC.'/styles" Target="styles.xml"/>'
                .'</Relationships>',
            'xl/styles.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<styleSheet xmlns="'.self::NS_MAIN.'">'
                .'<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
                .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
                .'<borders count="1"><border/></borders>'
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                .'<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
                .'</styleSheet>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="'.self::NS_MAIN.'"><sheetData>'.$sheetXml.'</sheetData></worksheet>',
        ];

        return self::zipStore($files);
    }

    private static function readCsv(string $content): array
    {
        $handle = fopen('php://temp', 'r+b');

        if ($handle === false) {
            throw new RuntimeException('File tidak dapat dibuka.');
        }

        fwrite($handle, $content);
        rewind($handle);

        $first = (string) fgets($handle);
        $delimiter = ',';

        if (substr_count($first, ';') > substr_count($first, ',')) {
            $delimiter = ';';
        } elseif (substr_count($first, "\t") > substr_count($first, ',')) {
            $delimiter = "\t";
        }

        rewind($handle);

        $rows = [];

        while (($raw = fgetcsv($handle, null, $delimiter)) !== false) {
            if ($raw === [null] || $raw === false) {
                continue;
            }

            $row = array_map(fn ($v) => self::toUtf8(trim((string) $v)), $raw);

            if (implode('', $row) !== '') {
                $rows[] = $row;
            }
        }

        fclose($handle);

        if (isset($rows[0][0])) {
            $rows[0][0] = preg_replace('/^\xEF\xBB\xBF/', '', $rows[0][0]);
        }

        return $rows;
    }

    private static function toUtf8(string $value): string
    {
        return mb_check_encoding($value, 'UTF-8') ? $value : mb_convert_encoding($value, 'UTF-8', 'Windows-1252');
    }

    private static function readXlsx(string $bin): array
    {
        try {
            $files = self::unzip($bin);
        } catch (RuntimeException $e) {
            throw new RuntimeException('File Excel tidak valid atau rusak: '.$e->getMessage());
        }

        $workbookPath = 'xl/workbook.xml';

        if (! isset($files[$workbookPath])) {
            throw new RuntimeException('File bukan workbook Excel yang valid.');
        }

        $sheetPath = self::resolveSheetPath($files[$workbookPath], $files);

        if (! isset($files[$sheetPath])) {
            throw new RuntimeException('Worksheet tidak ditemukan di dalam file.');
        }

        $shared = isset($files['xl/sharedStrings.xml']) ? self::parseSharedStrings($files['xl/sharedStrings.xml']) : [];

        return self::parseSheet($files[$sheetPath], $shared);
    }

    private static function resolveSheetPath(string $workbookXml, array $files): string
    {
        $workbook = simplexml_load_string($workbookXml);

        if ($workbook !== false) {
            $workbook->registerXPathNamespace('m', self::NS_MAIN);
            $sheets = $workbook->xpath('/m:workbook/m:sheets/m:sheet');

            $rid = '';
            if (! empty($sheets)) {
                $attrs = $sheets[0]->attributes(self::NS_REL_DOC);
                $rid = $attrs === null ? '' : (string) $attrs->id;
            }

            if ($rid !== '' && isset($files['xl/_rels/workbook.xml.rels'])) {
                $rels = simplexml_load_string($files['xl/_rels/workbook.xml.rels']);

                if ($rels !== false) {
                    foreach ($rels->children(self::NS_REL_PKG) as $rel) {
                        if ((string) $rel['Id'] === $rid) {
                            $target = ltrim((string) $rel['Target'], '/');

                            return str_starts_with($target, 'xl/') ? $target : 'xl/'.$target;
                        }
                    }
                }
            }
        }

        $candidates = preg_grep('#^xl/worksheets/sheet\d+\.xml$#i', array_keys($files));

        usort($candidates, 'strnatcasecmp');

        return $candidates === [] ? '' : reset($candidates);
    }

    private static function parseSharedStrings(string $xml): array
    {
        $root = @simplexml_load_string($xml);

        if ($root === false) {
            return [];
        }

        $root->registerXPathNamespace('m', self::NS_MAIN);
        $strings = [];

        foreach ($root->xpath('/m:sst/m:si') ?: [] as $si) {
            $si->registerXPathNamespace('m', self::NS_MAIN);
            $parts = [];

            foreach ($si->xpath('.//m:t') ?: [] as $t) {
                $parts[] = (string) $t;
            }

            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    private static function parseSheet(string $xml, array $shared): array
    {
        $root = @simplexml_load_string($xml);

        if ($root === false) {
            throw new RuntimeException('Isi worksheet tidak dapat dibaca.');
        }

        $root->registerXPathNamespace('m', self::NS_MAIN);
        $rows = [];
        $maxCol = -1;

        foreach ($root->xpath('//m:sheetData/m:row') ?: [] as $rowNode) {
            $rowNode->registerXPathNamespace('m', self::NS_MAIN);
            $rowData = [];

            foreach ($rowNode->xpath('./m:c') ?: [] as $cell) {
                $cell->registerXPathNamespace('m', self::NS_MAIN);
                $ref = (string) $cell['r'];
                $idx = $ref !== '' ? self::colIndex($ref) : count($rowData);
                $type = (string) $cell['t'];

                if ($type === 's') {
                    $val = $shared[(int) (string) $cell->v] ?? '';
                } elseif ($type === 'inlineStr') {
                    $parts = [];

                    foreach ($cell->xpath('.//m:t') ?: [] as $t) {
                        $parts[] = (string) $t;
                    }

                    $val = implode('', $parts);
                } elseif ($type === 'b') {
                    $val = ((string) $cell->v) === '1' ? '1' : '0';
                } else {
                    $val = trim((string) $cell->v);
                }

                $rowData[$idx] = $val;
                $maxCol = max($maxCol, $idx);
            }

            for ($i = 0; $i <= $maxCol; $i++) {
                $rowData[$i] ??= '';
            }

            ksort($rowData);

            if (implode('', $rowData) !== '') {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    private static function colIndex(string $ref): int
    {
        $letters = preg_replace('/[^A-Z]/', '', strtoupper($ref));
        $index = 0;

        foreach (str_split($letters ?: 'A') as $ch) {
            $index = $index * 26 + (ord($ch) - 64);
        }

        return max(0, $index - 1);
    }

    private static function colLetter(int $index): string
    {
        $letter = '';
        $index += 1;

        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $letter = chr(65 + $mod).$letter;
            $index = intdiv($index - $mod, 26);
        }

        return $letter;
    }

    private static function isNumericValue(mixed $value): bool
    {
        if (is_int($value) || is_float($value)) {
            return true;
        }

        return is_string($value) && preg_match('/^-?[1-9]\d{0,13}(\.\d{1,6})?$|^0$/', $value) === 1;
    }

    private static function zipStore(array $files): string
    {
        $now = getdate();
        $dosTime = ($now['hours'] << 11) | ($now['minutes'] << 5) | intdiv($now['seconds'], 2);
        $dosDate = (($now['year'] - 1980) << 9) | ($now['mon'] << 5) | $now['mday'];

        $local = '';
        $central = '';
        $offset = 0;

        foreach ($files as $name => $content) {
            $size = strlen($content);
            $crc = crc32($content) & 0xFFFFFFFF;
            $nameLen = strlen($name);

            $local .= "PK\x03\x04"
                .pack('vvvvvVVVvv', 20, 0, 0, $dosTime, $dosDate, $crc, $size, $size, $nameLen, 0)
                .$name.$content;

            $central .= "PK\x01\x02"
                .pack('vvvvvvVVVvvvvvVV', 20, 20, 0, 0, $dosTime, $dosDate, $crc, $size, $size, $nameLen, 0, 0, 0, 0, 0, $offset)
                .$name;

            $offset += 30 + $nameLen + $size;
        }

        $centralDirSize = strlen($central);
        $end = "PK\x05\x06".pack('vvvvVVv', 0, 0, count($files), count($files), $centralDirSize, $offset, 0);

        return $local.$central.$end;
    }

    private static function unzip(string $bin): array
    {
        $len = strlen($bin);

        if ($len < 22) {
            throw new RuntimeException('Ukuran file terlalu kecil.');
        }

        $eocd = false;
        $limit = max(0, $len - 65557);

        for ($i = $len - 22; $i >= $limit; $i--) {
            if (substr($bin, $i, 4) === "PK\x05\x06") {
                $eocd = $i;
                break;
            }
        }

        if ($eocd === false) {
            throw new RuntimeException('Struktur ZIP tidak ditemukan.');
        }

        $eocdInfo = unpack('vdisk/vcdDisk/vcountThis/vcountTotal/VcdSize/VcdOffset/vcommentLen', substr($bin, $eocd + 4, 18));

        $pos = $eocdInfo['cdOffset'];
        $meta = [];

        for ($n = 0; $n < $eocdInfo['countTotal']; $n++) {
            if (substr($bin, $pos, 4) !== "PK\x01\x02") {
                break;
            }

            $h = unpack(
                'vmadeBy/vneed/vflag/vmethod/vtime/vdate/Vcrc/Vcsize/Vusize/vnameLen/vextraLen/vcommentLen/vdiskStart/vintAttr/VextAttr/VlocalOff',
                substr($bin, $pos + 4, 42)
            );

            $name = substr($bin, $pos + 46, $h['nameLen']);
            $meta[$name] = $h;
            $pos += 46 + $h['nameLen'] + $h['extraLen'] + $h['commentLen'];
        }

        $files = [];

        foreach ($meta as $name => $h) {
            if ($h['flag'] & 1) {
                throw new RuntimeException('File ZIP terenkripsi tidak didukung.');
            }

            $lo = $h['localOff'];

            $lh = unpack(
                'vneed/vflag/vmethod/vtime/vdate/Vcrc/Vcsize/Vusize/vnameLen/vextraLen',
                substr($bin, $lo + 4, 26)
            );

            $dataStart = $lo + 30 + $lh['nameLen'] + $lh['extraLen'];
            $raw = substr($bin, $dataStart, $h['csize']);

            if ($h['method'] === 8) {
                $content = gzinflate($raw);

                if ($content === false) {
                    throw new RuntimeException('Gagal mendekompresi isi arsip.');
                }
            } elseif ($h['method'] === 0) {
                $content = $raw;
            } else {
                throw new RuntimeException('Metode kompresi ZIP tidak didukung.');
            }

            $files[$name] = $content;
        }

        return $files;
    }
}
