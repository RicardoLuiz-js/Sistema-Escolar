<?php
// php/arquivos_funcoes.php

/**
 * Funções para gerenciamento de arquivos (PDF, DOC, etc.)
 */

// Função para obter nome do mês
function getNomeMes($mes) {
    $meses = [
        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
    ];
    return $meses[$mes] ?? $mes;
}

// Função para obter ícone baseado na extensão
function getIconeArquivo($extensao) {
    $icones = [
        'pdf' => 'fa-file-pdf',
        'doc' => 'fa-file-word',
        'docx' => 'fa-file-word',
        'xls' => 'fa-file-excel',
        'xlsx' => 'fa-file-excel',
        'ppt' => 'fa-file-powerpoint',
        'pptx' => 'fa-file-powerpoint',
        'txt' => 'fa-file-lines',
        'csv' => 'fa-file-csv',
        'zip' => 'fa-file-zipper',
        'rar' => 'fa-file-zipper',
        'mp3' => 'fa-file-audio',
        'mp4' => 'fa-file-video',
        'jpg' => 'fa-file-image',
        'jpeg' => 'fa-file-image',
        'png' => 'fa-file-image',
        'gif' => 'fa-file-image'
    ];
    
    return $icones[strtolower($extensao)] ?? 'fa-file';
}

// Função para obter cor baseado na extensão
function getCorArquivo($extensao) {
    $cores = [
        'pdf' => '#e74c3c',     // Vermelho
        'doc' => '#2980b9',      // Azul
        'docx' => '#2980b9',     // Azul
        'xls' => '#27ae60',      // Verde
        'xlsx' => '#27ae60',     // Verde
        'ppt' => '#e67e22',      // Laranja
        'pptx' => '#e67e22',     // Laranja
        'txt' => '#7f8c8d',      // Cinza
        'csv' => '#27ae60',      // Verde
        'zip' => '#f39c12',      // Amarelo
        'rar' => '#f39c12',      // Amarelo
        'mp3' => '#9b59b6',      // Roxo
        'mp4' => '#e67e22',      // Laranja
        'jpg' => '#3498db',      // Azul claro
        'jpeg' => '#3498db',     // Azul claro
        'png' => '#3498db',      // Azul claro
        'gif' => '#3498db'       // Azul claro
    ];
    
    return $cores[strtolower($extensao)] ?? '#95a5a6';
}

// Função para obter todas as pastas disponíveis
function getPastasDisponiveisArquivos() {
    $diretorioBase = '../uploads/relatorios/';
    $pastas = [];
    
    if (!is_dir($diretorioBase)) return $pastas;
    
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
            
            // Verificar se tem arquivos na pasta (não imagens)
            $arquivos = scandir($caminhoMes);
            $temArquivo = false;
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                // Ignorar imagens (já estão na página de imagens)
                if (!in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $temArquivo = true;
                    break;
                }
            }
            
            $pastas[] = [
                'ano' => $ano,
                'mes' => $mes,
                'caminho' => "uploads/relatorios/$ano/$mes/",
                'nome' => "$ano/$mes",
                'label' => getNomeMes($mes) . " de $ano",
                'temArquivo' => $temArquivo
            ];
        }
    }
    
    // Ordenar por ano/mês (mais recente primeiro)
    usort($pastas, function($a, $b) {
        $anoA = intval($a['ano']);
        $anoB = intval($b['ano']);
        $mesA = intval($a['mes']);
        $mesB = intval($b['mes']);
        
        if ($anoA > $anoB) return -1;
        if ($anoA < $anoB) return 1;
        if ($mesA > $mesB) return -1;
        if ($mesA < $mesB) return 1;
        return 0;
    });
    
    return $pastas;
}

// Função para escanear arquivos de uma pasta específica
function escanearArquivos($pastaSelecionada = null) {
    $diretorioBase = '../uploads/relatorios/';
    $arquivos = [];
    
    // Extensões de imagem para IGNORAR
    $extensoesImagem = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if ($pastaSelecionada) {
        // Escanear apenas a pasta selecionada
        $caminhoCompleto = '../' . $pastaSelecionada;
        if (is_dir($caminhoCompleto)) {
            $itens = scandir($caminhoCompleto);
            foreach ($itens as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $caminhoCompletoArquivo = $caminhoCompleto . $item;
                if (is_file($caminhoCompletoArquivo)) {
                    $extensao = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    
                    // Ignorar imagens
                    if (!in_array($extensao, $extensoesImagem)) {
                        $arquivos[] = [
                            'caminho' => $pastaSelecionada . $item,
                            'nome' => $item,
                            'extensao' => $extensao,
                            'tamanho' => filesize($caminhoCompletoArquivo),
                            'data_modificacao' => filemtime($caminhoCompletoArquivo)
                        ];
                    }
                }
            }
        }
    } else {
        // Escanear todas as pastas
        if (!is_dir($diretorioBase)) return $arquivos;
        
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
                
                $itens = scandir($caminhoMes);
                foreach ($itens as $item) {
                    if ($item === '.' || $item === '..') continue;
                    
                    $caminhoCompleto = $caminhoMes . $item;
                    if (is_file($caminhoCompleto)) {
                        $extensao = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                        
                        // Ignorar imagens
                        if (!in_array($extensao, $extensoesImagem)) {
                            $arquivos[] = [
                                'caminho' => "uploads/relatorios/$ano/$mes/$item",
                                'nome' => $item,
                                'extensao' => $extensao,
                                'ano' => $ano,
                                'mes' => $mes,
                                'tamanho' => filesize($caminhoCompleto),
                                'data_modificacao' => filemtime($caminhoCompleto)
                            ];
                        }
                    }
                }
            }
        }
    }
    
    return $arquivos;
}

// Função para formatar tamanho de arquivo
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

// Função para formatar data
function formatarDataArquivo($timestamp) {
    return date('d/m/Y H:i', $timestamp);
}

// Função para agrupar arquivos por tipo
function agruparPorTipo($arquivos) {
    $grupos = [
        'pdf' => ['nome' => 'PDF', 'icone' => 'fa-file-pdf', 'cor' => '#e74c3c', 'arquivos' => []],
        'word' => ['nome' => 'Word', 'icone' => 'fa-file-word', 'cor' => '#2980b9', 'arquivos' => []],
        'excel' => ['nome' => 'Excel', 'icone' => 'fa-file-excel', 'cor' => '#27ae60', 'arquivos' => []],
        'powerpoint' => ['nome' => 'PowerPoint', 'icone' => 'fa-file-powerpoint', 'cor' => '#e67e22', 'arquivos' => []],
        'texto' => ['nome' => 'Texto', 'icone' => 'fa-file-lines', 'cor' => '#7f8c8d', 'arquivos' => []],
        'compactado' => ['nome' => 'Compactados', 'icone' => 'fa-file-zipper', 'cor' => '#f39c12', 'arquivos' => []],
        'audio' => ['nome' => 'Áudio', 'icone' => 'fa-file-audio', 'cor' => '#9b59b6', 'arquivos' => []],
        'video' => ['nome' => 'Vídeo', 'icone' => 'fa-file-video', 'cor' => '#e67e22', 'arquivos' => []],
        'outros' => ['nome' => 'Outros', 'icone' => 'fa-file', 'cor' => '#95a5a6', 'arquivos' => []]
    ];
    
    foreach ($arquivos as $arquivo) {
        $ext = strtolower($arquivo['extensao']);
        
        if (in_array($ext, ['pdf'])) {
            $grupos['pdf']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['doc', 'docx'])) {
            $grupos['word']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['xls', 'xlsx', 'csv'])) {
            $grupos['excel']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['ppt', 'pptx'])) {
            $grupos['powerpoint']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['txt', 'rtf', 'md'])) {
            $grupos['texto']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])) {
            $grupos['compactado']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])) {
            $grupos['audio']['arquivos'][] = $arquivo;
        } elseif (in_array($ext, ['mp4', 'avi', 'mkv', 'mov', 'wmv'])) {
            $grupos['video']['arquivos'][] = $arquivo;
        } else {
            $grupos['outros']['arquivos'][] = $arquivo;
        }
    }
    
    // Remover grupos vazios
    return array_filter($grupos, function($grupo) {
        return !empty($grupo['arquivos']);
    });
}
?>