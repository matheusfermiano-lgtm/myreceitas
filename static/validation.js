/* static/validation.js
 * MyReceitas - validação frontend:
 * - Nome: bloqueia números, emojis e caracteres especiais (só letras, espaço, hífen, apóstrofo)
 * - Telefone: aceita/envia apenas números (10 ou 11 dígitos)
 */
(function () {
  'use strict';

  var NOME_PERMITIDO = /[^A-Za-zÀ-ÖØ-öø-ÿ\s'\-’]/gu;
  var NOME_VALIDO = /^[\p{L}\s'\-’]+$/u;

  function soLetras(str) {
    return (str || '').replace(/[^\p{L}]/gu, '');
  }

  function validarNomeValor(valor) {
    var v = (valor || '').trim();
    if (v.length < 3) return false;
    if (v.length > 100) return false;
    if (/[0-9]/.test(v)) return false;
    if (!NOME_VALIDO.test(v)) return false;
    if (soLetras(v).length < 3) return false;
    return true;
  }

  function limparNomeValor(valor) {
    // Remove números, emojis e especiais em tempo real
    return (valor || '').replace(NOME_PERMITIDO, '').replace(/[0-9]/g, '');
  }

  function validarTelefoneValor(valor) {
    var digitos = (valor || '').replace(/\D/g, '');
    if (digitos === '') return true; // opcional
    if (!/^\d{10,11}$/.test(digitos)) return false;
    if (/^(\d)\1{9,10}$/.test(digitos)) return false;
    return true;
  }

  function mostrarErro(input, mostrar) {
    var group = input.closest('.form-group') || input.parentElement;
    if (!group) return;
    var hint = group.querySelector('.field-error');
    if (hint) hint.style.display = mostrar ? 'block' : 'none';
    input.style.borderColor = mostrar ? '#c0392b' : '';
  }

  function bindNome(input) {
    input.addEventListener('input', function () {
      var antes = input.value;
      var limpo = limparNomeValor(antes);
      if (limpo !== antes) {
        input.value = limpo;
      }
      // esconde erro enquanto digita se ficou válido
      if (validarNomeValor(input.value)) mostrarErro(input, false);
    });
    input.addEventListener('blur', function () {
      input.value = limparNomeValor(input.value).replace(/\s{2,}/g, ' ').trimStart();
      mostrarErro(input, !validarNomeValor(input.value) && input.value.trim() !== '');
    });
  }

  function bindTelefone(input) {
    input.setAttribute('inputmode', 'numeric');
    input.addEventListener('input', function () {
      var digitos = input.value.replace(/\D/g, '').slice(0, 11);
      if (input.value !== digitos) input.value = digitos;
      if (validarTelefoneValor(input.value)) mostrarErro(input, false);
    });
    input.addEventListener('blur', function () {
      mostrarErro(input, !validarTelefoneValor(input.value));
    });
  }

  function bindForm(form) {
    form.addEventListener('submit', function (e) {
      var ok = true;

      form.querySelectorAll('[data-validate-nome]').forEach(function (input) {
        // limpeza final: garante que só vai nome válido
        input.value = limparNomeValor(input.value).replace(/\s{2,}/g, ' ').trim();
        if (!validarNomeValor(input.value)) {
          mostrarErro(input, true);
          ok = false;
        }
      });

      form.querySelectorAll('[data-validate-telefone]').forEach(function (input) {
        // Telefone: envia APENAS números
        input.value = (input.value || '').replace(/\D/g, '').slice(0, 11);
        if (!validarTelefoneValor(input.value)) {
          mostrarErro(input, true);
          ok = false;
        }
      });

      if (!ok) {
        e.preventDefault();
        var primeiroErro = form.querySelector('[data-validate-nome],[data-validate-telefone]');
        // foca no primeiro campo inválido
        var invalido = Array.from(form.querySelectorAll('[data-validate-nome],[data-validate-telefone]'))
          .find(function (i) {
            return (i.style.borderColor === 'rgb(192, 57, 43)' || i.style.borderColor === '#c0392b');
          });
        if (invalido) invalido.focus();
        alert('Verifique os campos: nome só com letras e espaços (sem números, emojis ou caracteres especiais) e telefone só com números (DDD + número).');
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-validate-nome]').forEach(bindNome);
    document.querySelectorAll('[data-validate-telefone]').forEach(bindTelefone);
    document.querySelectorAll('form').forEach(function (f) {
      if (f.querySelector('[data-validate-nome],[data-validate-telefone]')) bindForm(f);
    });
  });
})();
