// Custom Javascript

function formatarMoeda(id) {
    var elemento = document.getElementById(id);
    var valor = elemento.value;

    valor = valor + '';
    valor = parseInt(valor.replace(/[\D]+/g, ''));
    valor = valor + '';
    valor = valor.replace(/([0-9]{2})$/g, ",$1");

    if (valor.length > 6) {
        valor = valor.replace(/([0-9]{3}),([0-9]{2}$)/g, ".$1,$2");
    }

    elemento.value = valor;
    if(valor == 'NaN') elemento.value = '';
}

function validation(){
    var validate = true;
    $('input:required').each(function(){
        if($(this).val().trim() === ''){
            validate = false;
        }
    });
    if(validate) {
        showLoading();
    }
}

function acao() {
    showLoading();
    return confirm('Você confirma esta ação?');
}

document.getElementsByClassName('showLoading').addEventListener('click', showLoading());

// Adiciona um event listener global ao documento
document.addEventListener('click', function(event) {
    // Verifica se o elemento clicado é um formulário ou está dentro de um formulário
    const form = event.target.closest('form');

    if (form) {
        acao();
    }
});

// document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM totalmente carregado.");
// });
