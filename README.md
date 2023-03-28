# symfony_procost_mfritsch

- Matthieu FRITSCH
- [_matthieu.fritsch06@gmail.com_](mailto:mathieu.fritsch06@gmail.com)

## Instructions

Executer à la suite ces commandes pour lancer le projet.

1. `composer install`
2. ``php bin/console doctrine:database:create``
3. ``php bin/console doctrine:migrations:diff``
4. ``php bin/console doctrine:migrations:migrate``
5. ``php bin/console doctrine:fixtures:load``
6. ``php -S localhost:3000 -t public``

## Précision

### Fixtures

Les fixtures génèrent une liste de projet non livré.

### Bundles utilisés

- [twig/intl-extra](https://packagist.org/packages/twig/intl-extra) : Gérer le format de date dans les fichiers Twigs
- [knplabs/knp-paginator-bundle](https://github.com/KnpLabs/KnpPaginatorBundle): Gérer la pagination des tableaux d'éléments
