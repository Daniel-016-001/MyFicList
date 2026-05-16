<?php
$dirs = [__DIR__ . '/app', __DIR__ . '/routes'];

foreach ($dirs as $dirPath) {
    $dir = new RecursiveDirectoryIterator($dirPath);
    $iterator = new RecursiveIteratorIterator($dir);
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            $tokens = token_get_all($content);
            $output = '';
            
            foreach ($tokens as $token) {
                if (is_array($token)) {
                    if ($token[0] === T_COMMENT || $token[0] === T_DOC_COMMENT) {
                        // Preservamos los saltos de línea para no descolocar el formato
                        $output .= str_repeat("\n", substr_count($token[1], "\n"));
                        continue;
                    }
                    $output .= $token[1];
                } else {
                    $output .= $token;
                }
            }
            
            // Limpiar múltiples saltos de línea vacíos seguidos que dejan los comentarios
            $output = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n\n", $output);
            // Evitar más de 2 saltos de línea
            $output = preg_replace("/\n{3,}/", "\n\n", $output);
            
            file_put_contents($file->getPathname(), $output);
        }
    }
}

echo "Comentarios eliminados en app/ y routes/\n";
