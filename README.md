# Agora

Agora est une application web permettant de jouer à des jeux de société en ligne. Il s'agit d'une bibliothèque de jeux contenant pour l'instant les jeux suivants :
- 6 qui prend
- Splendor
- Glenmore
- Myrmes

Des lobbys pour chaque jeux peuvent être créés, permettant de jouer et d'inviter ses amis ou d'affronter des inconnus.

## Configuration technique

Agora est une application Symfony utilisant les technologies suivantes :
- PHP en version 8.3
- Symfony en version 6.4
- MariaDB en version 10.11.2
- Traefik en version 2.10

De nombreux outils sont également configurés pour permettre de facilement monitorer l'application, comme prometheus et une stack ELK.

## Déploiement de l'application

Agora est une application Dockerisée, son lancement en local est donc simple et peut être réalisé en une seule commande :
```
docker compose up
```

Si nécessaire, il peut être utile de rajouter l'option --build à cette commande pour forcer le rebuild de l'image Agora (si jamais le Dockerfile a été modifié)

De plus, de nombreux fichiers compose sont disponibles en fonction des environnements et des outils à lancer. Par défaut, la commande lance l'application avec les fichiers compose.yaml et compose.override.yaml
Il est ainsi possible d'utiliser l'option -f pour choisir les fichiers compose à exécuter.

Les fichiers compose "de base", définissant, en plus d'Agora, les différents outils à lancer sont:
- compose.yaml : lance l'application de base
- compose_monitor_light.yaml : lance Prometheus, Grafana et Jaeger en plus d'Agora pour monitorer l'application
- compose_monitor.yaml : lance la stack ELK pour analyser les logs et l'état du serveur, en plus des outils précédents

Les fichiers compose définissant les environnements sont les suivants :
- compose.override.yaml : définit l'environnement par défaut d'Agora, qui est ici un environnement de développement
- compose.dev.yaml : définit l'environnement de développement
- compose.staging.yaml : définit l'environnement de test
- compose.prod.yaml : définit l'environnement de prod

Ainsi, pour lancer l'application Agora avec tous les outils en environnement de test, on peut utiliser la commande suivante :
```
docker compose -f compose_monitor.yaml -f compose.staging.yaml up
```

## Résolution des problèmes

En cas d'ajout de dépendances, l'application ne voudra possiblement plus démarrer, les dépendances étant mise en cache directement dans des volumes.
Pour resynchroniser ces volumes avec les nouvelles dépendances, il est nécessaire de supprimer les volumes avec la commande suivante :
```
docker volume rm agora_vendor_agora && docker volume rm agora_var_agora
```