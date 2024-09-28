let timeoutId;
let canSubmit = true;

function changeStatus(texte, erreur) {
    document.getElementById('code-status').innerHTML = texte;
    if (erreur) {
        document.getElementById('code-status').style.color = "red";
        canSubmit = false;
    }
    else {
        document.getElementById('code-status').style.color = "green";
        canSubmit = true;
    }
}

function verifierCodeValidite(code) {
    const regex = /^[a-zA-Z0-9]+$/;
    fetch(`check/code/${code}`)
        .then(response => response.json())
        .then(data => {
            if (code.length < 8) {
                changeStatus("Code trop court (8 caractères)", true);
            }
            else if (code.length > 8) {
                changeStatus("Code trop long (8 caractères)", true);
            }
            else if (!regex.test(code)) {
                changeStatus("Le code doit contenir que des caractères alphanumériques", true);
            }
            else if (!data.codeLibre) {
                changeStatus("Code déjà utilisé", true);
            }
            else {
                changeStatus("Code disponible", false);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

document.getElementById('inscription_utilisateur_code').addEventListener('input', function (input) {
    clearTimeout(timeoutId);
    const code = input.target.value;
    if (code !== "") {
        timeoutId = setTimeout(function () {
            verifierCodeValidite(code);
        }, 300);
    }
    else {
        document.getElementById('code-status').innerHTML = "";
        canSubmit = true;
    }
});

document.getElementById('form-inscription').addEventListener('submit', function (submit) {
    console.log(canSubmit)
    if (!canSubmit) {
        submit.preventDefault();
        alert("Le code n'est pas valide.");
    }
});