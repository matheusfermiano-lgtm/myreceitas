<?php
// config/validation.php
// Validações centrais do MyReceitas:
// - Nomes: sem números, sem emojis, sem caracteres especiais
// - Telefone: salva/envia apenas números

if (!function_exists('validarNome')) {
    /**
     * Valida nome (pessoa, restaurante ou receita).
     * Regras:
     *  - Mínimo 3 e máximo 100 caracteres (após trim)
     *  - NÃO permite números
     *  - NÃO permite emojis
     *  - NÃO permite caracteres especiais (@ # $ % * ! ? / \ = + _  etc.)
     *  - Permite apenas: letras (com acento), espaços, hífen (-) e apóstrofo (' / ’)
     *
     * @param string $nome
     * @return array [bool $valido, string $erro]
     */
    function validarNome($nome) {
        $nome = trim((string)$nome);

        if ($nome === '') {
            return [false, 'O nome é obrigatório.'];
        }

        if (mb_strlen($nome, 'UTF-8') < 3) {
            return [false, 'O nome deve ter no mínimo 3 letras.'];
        }

        if (mb_strlen($nome, 'UTF-8') > 100) {
            return [false, 'O nome deve ter no máximo 100 caracteres.'];
        }

        // 1) Bloqueia números
        if (preg_match('/[0-9]/u', $nome)) {
            return [false, 'O nome não pode conter números.'];
        }

        // 2) Bloqueia emojis e símbolos pictográficos
        // Faixas: emoticons, símbolos diversos, dingbats, flags, etc.
        if (preg_match('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE00}-\x{FE0F}\x{1F300}-\x{1F6FF}\x{1F900}-\x{1F9FF}]/u', $nome)) {
            return [false, 'O nome não pode conter emojis.'];
        }

        // 3) Permite APENAS letras (com acentos), espaços, hífen e apóstrofo
        if (!preg_match("/^[\p{L}\s'\-’]+$/u", $nome)) {
            return [false, 'O nome não pode conter caracteres especiais. Use apenas letras e espaços.'];
        }

        // 4) Precisa ter ao menos 3 letras (evita " - ' " etc.)
        $soLetras = preg_replace("/[^\p{L}]/u", '', $nome);
        if (mb_strlen($soLetras, 'UTF-8') < 3) {
            return [false, 'O nome deve conter ao menos 3 letras.'];
        }

        return [true, ''];
    }
}

if (!function_exists('limparTelefone')) {
    /**
     * Remove tudo que não for número. Ex: "(47) 99999-9999" => "47999999999"
     */
    function limparTelefone($telefone) {
        return preg_replace('/\D/', '', (string)$telefone);
    }
}

if (!function_exists('validarTelefone')) {
    /**
     * Valida telefone brasileiro (após limpeza).
     * - Vazio é válido (campo opcional na maioria dos formulários)
     * - Se preenchido: 10 ou 11 dígitos, não pode ser todos iguais (ex: 11111111111)
     *
     * @param string $telefoneSomenteNumeros
     * @param bool $obrigatorio
     * @return array [bool $valido, string $erro]
     */
    function validarTelefone($telefoneSomenteNumeros, $obrigatorio = false) {
        $tel = preg_replace('/\D/', '', (string)$telefoneSomenteNumeros);

        if ($tel === '') {
            return $obrigatorio
                ? [false, 'O telefone é obrigatório.']
                : [true, ''];
        }

        if (!preg_match('/^\d{10,11}$/', $tel)) {
            return [false, 'Telefone inválido. Digite DDD + número (10 ou 11 dígitos, só números).'];
        }

        // Rejeita sequências como 0000000000, 11111111111 etc.
        if (preg_match('/^(\d)\1{9,10}$/', $tel)) {
            return [false, 'Telefone inválido.'];
        }

        return [true, ''];
    }
}
