# Symfony P11

Ce repo contient une application de gestion de formation.
Il s'agit d'un projet pédagogique pour la promo 11.

## Prérequis

- Linux, MacOS ou Windows
- Bash
- PHP 8
- Composer
- symfony-CLI
- Mariadb 10
- Docker (optionnel)

## Installation

```
git clone https://github.com/hugo-michel/symfony-p11
cd symfony-p11
composer install
```

Créez une base de données et un utilisateur dédié pour cette base de données.

## Configuration

Créez un fochier `.env` à la racine du projet :

```
APP_ENV=dev
App_DEBUG=true
APP_SECRET=98aeb0581fe939d58566d1eff95851ee
DATABASE_URL="mysql://symfony_p11:123@127.0.0.1:3306/symfony_p11?serverVersion=mariadb-10.6.12&charset=utf8mb4"
```

Pensez à changer la variable `APP_SECRET` et les codes d'accès dans la variable `DATABASE_URL`.

**ATTENTION : `APP_SECRET` doit être une chaine de caractère de 32 caractères en hexadecimal.**

## Migration et fixtures

Pour que l'application soit utilisable, vous devez créer le schéma de BDD et charger les données :

```
bin/dofilo.sh
```

## Utilisation

Lancez le serveur web de developpement

```
symfony serve
```

Puis ouvrez la page suivante : [https://localhost:8000](https://localhost:8000).

## Mentions légales

Ce projet est sous licence MIT.

La licence est disponible ici [MIT LICENCE](LICENCE).
