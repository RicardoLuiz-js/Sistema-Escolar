<?php
// php/imagens_funcoes.php

/**
 * Funções para gerenciamento de imagens
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

// Função para obter todas as pastas disponíveis
function getPastasDisponiveis() {
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
            
            // Verificar se tem imagens na pasta
            $arquivos = scandir($caminhoMes);
            $temImagem = false;
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $temImagem = true;
                    break;
                }
            }
            
            $pastas[] = [
                'ano' => $ano,
                'mes' => $mes,
                'caminho' => "uploads/relatorios/$ano/$mes/",
                'nome' => "$ano/$mes",
                'label' => getNomeMes($mes) . " de $ano",
                'temImagem' => $temImagem
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

// Função para escanear imagens de uma pasta específica
function escanearImagens($pastaSelecionada = null) {
    $diretorioBase = '../uploads/relatorios/';
    $imagens = [];
    
    if ($pastaSelecionada) {
        // Escanear apenas a pasta selecionada
        $caminhoCompleto = '../' . $pastaSelecionada;
        if (is_dir($caminhoCompleto)) {
            $arquivos = scandir($caminhoCompleto);
            foreach ($arquivos as $arquivo) {
                if ($arquivo === '.' || $arquivo === '..') continue;
                
                $extensao = strtolower(pathinfo($arquivo, PATHINFO_EXTENSION));
                if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $imagens[] = [
                        'caminho' => $pastaSelecionada . $arquivo,
                        'nome' => $arquivo,
                        'tamanho' => filesize($caminhoCompleto . $arquivo)
                    ];
                }
            }
        }
    } else {
        // Escanear todas as pastas
        if (!is_dir($diretorioBase)) return $imagens;
        
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
                        $imagens[] = [
                            'caminho' => "uploads/relatorios/$ano/$mes/$arquivo",
                            'nome' => $arquivo,
                            'ano' => $ano,
                            'mes' => $mes,
                            'tamanho' => filesize($caminhoMes . $arquivo)
                        ];
                    }
                }
            }
        }
    }
    
    return $imagens;
}

// Função para formatar tamanho de arquivo
function formatarTamanho($bytes) {
    if ($bytes > 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes > 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
?>