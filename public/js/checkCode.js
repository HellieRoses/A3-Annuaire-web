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

function verifierCodeValide(code) {
    const regex = /^[a-zA-Z0-9]+$/;
    const URL = Routing.generate("checkCode", {"code": code})
    fetch(URL, {method: "POST"})
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

document.getElementsByClassName('utilisateur-code')[0].addEventListener('input', function (input) {
    clearTimeout(timeoutId);
    const code = input.target.value;
    if (code !== "") {
        timeoutId = setTimeout(function () {
            verifierCodeValide(code);
        }, 300);
    }
    else {
        document.getElementById('code-status').innerHTML = "";
        canSubmit = true;
    }
});

document.getElementsByClassName("utilisateur-form")[0].addEventListener('submit', function (submit) {
    if (!canSubmit) {
        submit.preventDefault();
        alert("Le code n'est pas valide.");
    }
});