# Annuaire

## Commande pour la création d'utilisateur

Pour créer un utilisateur dans le terminal, il faut rentrer la commande suivante : create:user

![Alt text](image.png)

Comme le montre la commande --help de la commande, cette commande peut prendre différentes options pour paramétrer les différentes informations de base de l'utilisateur

- Login

L'option --login permet de renseigner le login de l'utilisateur à créer, ce login sera vérifier pour qu'il ne soit pas déjà pris par un utilisateur

- Email

L'option --email permet de renseigner le mail de l'utilisateur à créer, celui-ci est validé s'il l'addresse est valide et pas déjà utilisé par un autre utilisateur

- Mot de Passe

L'option --

- Code

L'option --code permet de renseigner le code à utiliser pour créer l'utilisateur. Si le code n'est pas renseigné ou s'il est invalide, il sera de nouveau demandé de manière interactive. Si l'utilisateur ne renseigne rien à ce moment, le code sera alors généré aléatoirement

- Visibilité

L'option --visible et --no-visible 

- Role admin

l'option --admin ou --no-admin permet de déterminer si l'utilisateur à créer aura le rôle admin ou non. 

Toutes les options qui ne seront pas renseigné dans l'éxécution de la commande seront demandé de façon interactive pour que tous les champs soient renseignés. De même, les valeurs d'options qui ne seront pas valide seront à renseigner de nouveau.