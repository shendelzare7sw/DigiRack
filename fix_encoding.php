<?php
$dir = new RecursiveDirectoryIterator('c:\laragon\www\DigiRack\resources\views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    'â€¢' => '•',
    'â€“' => '–',
    'â€”' => '—',
    'â€˜' => '‘',
    'â€™' => '’',
    'â€œ' => '“',
    'â€' => '”',
    'â€ ' => '”', // fallback for corrupted right quote
    'â€¦' => '…',
    'â€º' => '›',
    'Â©' => '©',
    'Â®' => '®',
    'Ã©' => 'é',
    'Ã¡' => 'á',
    'Ã³' => 'ó',
    'Ã­' => 'í',
    'Ãº' => 'ú',
    'Ã±' => 'ñ'
];

foreach($files as $file) {
    if(!is_array($file)) continue;
    $path = $file[0];
    
    $content = file_get_contents($path);
    $orig = $content;
    
    $content = str_replace(array_keys($replacements), array_values($replacements), $content);
    
    if ($content !== $orig) {
        file_put_contents($path, $content);
        echo "Fixed: $path\n";
    }
}
echo "Done.\n";
