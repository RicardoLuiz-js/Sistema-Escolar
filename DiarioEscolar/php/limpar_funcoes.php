<?php
// php/limpar_funcoes.php

session_start();

// Senha definida
define('SENHA_LIMPEZA', 'apagar404');

/**
 * Verifica se a senha está correta
 */
function verificarSenha($senha) {
    return $senha === SENHA_LIMPEZA;
}

/**
 * Conta quantas imagens existem
 */
function contarImagens() {
    $diretorioBase = '../uploads/relatorios/';
    $total = 0;
    
    if (!is_dir($diretorioBase)) return 0;
    
    $anos = scandir($diretorioBase);
    foreach ($anos as $ano) {
        if ($ano === '.' || $ano === '..') continue;
        
        $caminhoAno = $diretorioBase . $ano . '/';
        if (!is_dir($caminhoAno)) continue;
        
        $meses = scandir($caminhoAno);
        foreach ($meses as $mes) {
            if ($mes === '.' || $mes === '..') continue;
            
            $caminhoMes = $caminhoAno . $mes . '/';
            if (!is_dir($caminhoMes)) continue;
            
            $arquivos = scandir($caminhoMes);
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $total++;
                }
            }
        }
    }
    
    return $total;
}

/**
 * Conta quantos arquivos (não imagens) existem
 */
function contarArquivos() {
    $diretorioBase = '../uploads/relatorios/';
    $total = 0;
    
    if (!is_dir($diretorioBase)) return 0;
    
    $anos = scandir($diretorioBase);
    foreach ($anos as $ano) {
        if ($ano === '.' || $ano === '..') continue;
        
        $caminhoAno = $diretorioBase . $ano . '/';
        if (!is_dir($caminhoAno)) continue;
        
        $meses = scandir($caminhoAno);
        foreach ($meses as $mes) {
            if ($mes === '.' || $mes === '..') continue;
            
            $caminhoMes = $caminhoAno . $mes . '/';
            if (!is_dir($caminhoMes)) continue;
            
            $arquivos = scandir($caminhoMes);
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $total++;
                }
            }
        }
    }
    
    return $total;
}

/**
 * Calcula o tamanho total da pasta uploads
 */
function calcularTamanhoTotal() {
    $diretorioBase = '../uploads/';
    $tamanhoTotal = 0;
    
    if (!is_dir($diretorioBase)) return 0;
    
    $itens = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($diretorioBase, RecursiveDirectoryIterator::SKIP_DOTS)
    );
    
    foreach ($itens as $item) {
        if ($item->isFile()) {
            $tamanhoTotal += $item->getSize();
        }
    }
    
    return $tamanhoTotal;
}

/**
 * Formata tamanho em bytes para formato legível
 */
function formatarTamanho($bytes) {
    if ($bytes > 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes > 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes > 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

/**
 * Limpar apenas imagens
 */
function limparApenasImagens() {
    $diretorioBase = '../uploads/relatorios/';
    $removidos = 0;
    $espaco = 0;
    
    if (!is_dir($diretorioBase)) return ['removidos' => 0, 'espaco' => 0];
    
    $anos = scandir($diretorioBase);
    foreach ($anos as $ano) {
        if ($ano === '.' || $ano === '..') continue;
        
        $caminhoAno = $diretorioBase . $ano . '/';
        if (!is_dir($caminhoAno)) continue;
        
        $meses = scandir($caminhoAno);
        foreach ($meses as $mes) {
            if ($mes === '.' || $mes === '..') continue;
            
            $caminhoMes = $caminhoAno . $mes . '/';
            if (!is_dir($caminhoMes)) continue;
            
            $arquivos = scandir($caminhoMes);
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $caminhoCompleto = $caminhoMes . $arquivo;
                    $espaco += filesize($caminhoCompleto);
                    if (unlink($caminhoCompleto)) {
                        $removidos++;
                    }
                }
            }
        }
    }
    
    return ['removidos' => $removidos, 'espaco' => $espaco];
}

/**
 * Limpar apenas arquivos (não imagens)
 */
function limparApenasArquivos() {
    $diretorioBase = '../uploads/relatorios/';
    $removidos = 0;
    $espaco = 0;
    
    if (!is_dir($diretorioBase)) return ['removidos' => 0, 'espaco' => 0];
    
    $anos = scandir($diretorioBase);
    foreach ($anos as $ano) {
        if ($ano === '.' || $ano === '..') continue;
        
        $caminhoAno = $diretorioBase . $ano . '/';
        if (!is_dir($caminhoAno)) continue;
        
        $meses = scandir($caminhoAno);
        foreach ($meses as $mes) {
            if ($mes === '.' || $mes === '..') continue;
            
            $caminhoMes = $caminhoAno . $mes . '/';
            if (!is_dir($caminhoMes)) continue;
            
            $arquivos = scandir($caminhoMes);
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $caminhoCompleto = $caminhoMes . $arquivo;
                    $espaco += filesize($caminhoCompleto);
                    if (unlink($caminhoCompleto)) {
                        $removidos++;
                    }
                }
            }
        }
    }
    
    return ['removidos' => $removidos, 'espaco' => $espaco];
}

/**
 * Limpar tudo (arquivos + imagens)
 */
function limparTudo() {
    $imagens = limparApenasImagens();
    $arquivos = limparApenasArquivos();
    
    return [
        'imagens' => $imagens['removidos'],
        'arquivos' => $arquivos['removidos'],
        'espaco' => $imagens['espaco'] + $arquivos['espaco']
    ];
}

/**
 * Criar backup dos arquivos
 */
function criarBackup($tipo = 'tudo') {
    $diretorioBase = '../uploads/';
    $data = date('Y-m-d_H-i-s');
    $nomeArquivo = "backup_{$tipo}_{$data}.zip";
    $caminhoBackup = "../backups/{$nomeArquivo}";
    
    // Criar pasta de backups se não existir
    if (!is_dir('../backups')) {
        mkdir('../backups', 0777, true);
    }
    
    $zip = new ZipArchive();
    if ($zip->open($caminhoBackup, ZipArchive::CREATE) !== TRUE) {
        return false;
    }
    
    // Adicionar arquivos ao ZIP
    $diretorios = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($diretorioBase),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($diretorios as $arquivo) {
        if (!$arquivo->isFile()) continue;
        
        $caminhoReal = $arquivo->getRealPath();
        $extensao = strtolower($arquivo->getExtension());
        
        // Filtrar por tipo
        if ($tipo === 'imagens' && !in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            continue;
        }
        if ($tipo === 'arquivos' && in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            continue;
        }
        
        $caminhoRelativo = substr($caminhoReal, strlen(realpath('../')) + 1);
        $zip->addFile($caminhoReal, $caminhoRelativo);
    }
    
    $zip->close();
    
    // Registrar no log
    $log = fopen('../backups/backup_log.txt', 'a');
    fwrite($log, "[" . date('Y-m-d H:i:s') . "] Backup criado: {$nomeArquivo} (Tipo: {$tipo})\n");
    fclose($log);
    
    return $nomeArquivo;
}

/**
 * Listar backups disponíveis
 */
function listarBackups() {
    $backups = [];
    $pastaBackup = '../backups/';
    
    if (!is_dir($pastaBackup)) return $backups;
    
    $arquivos = scandir($pastaBackup);
    foreach ($arquivos as $arquivo) {
        if ($arquivo === '.' || $arquivo === '..') continue;
        
        if (pathinfo($arquivo, PATHINFO_EXTENSION) === 'zip') {
            $caminho = $pastaBackup . $arquivo;
            $backups[] = [
                'nome' => $arquivo,
                'tamanho' => formatarTamanho(filesize($caminho)),
                'data' => date('d/m/Y H:i:s', filemtime($caminho))
            ];
        }
    }
    
    // Ordenar por data (mais recente primeiro)
    usort($backups, function($a, $b) {
        return strtotime(str_replace('/', '-', $b['data'])) - strtotime(str_replace('/', '-', $a['data']));
    });
    
    return $backups;
}
?>