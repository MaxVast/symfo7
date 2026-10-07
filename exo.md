# EventHub — Énoncé : suite du projet fil rouge

Vous avez déjà : l'entité `User`, le formulaire et le contrôleur d'inscription, la migration, la connexion. La base de l'authentification est posée.

---

## Étape 1 — Compléter le modèle de données

### À créer

Trois entités supplémentaires :

**`Category`**
- `name` (string, unique)
- `slug` (string, unique)

**`Event`**
- `title` (string)
- `slug` (string, unique)
- `description` (text)
- `startAt` / `endAt` (dates)
- `capacity` (int)
- `status` (un **enum PHP** : `Draft`, `Published`, `Cancelled`)
- `coverImage` (string, optionnel — nom du fichier uploadé)
- une relation vers l'organisateur (`User`), vers `Category`

**`Registration`**
- une relation vers `Event`, une vers `User`
- `status` (un enum : `Confirmed`, `Waitlist`, `Cancelled`)
- `createdAt`

### Contraintes à respecter

- Un `User` peut organiser plusieurs `Event` (one-to-many), et le même `User` peut avoir plusieurs `Registration`.
- Une `Category` peut être utilisée par plusieurs `Event`.
- **Un utilisateur ne doit pouvoir s'inscrire qu'une seule fois au même événement** : trouvez comment l'imposer au niveau de la base (indice : une contrainte d'unicité sur deux colonnes à la fois).
- `Event.endAt` doit toujours être postérieur à `Event.startAt` : ajoutez une règle de validation personnalisée sur l'entité (indice : `#[Assert\Callback]`).

### À faire

1. Créez les entités.
2Générez la migration, relisez le SQL généré avant de la jouer.

### Checklist de fin d'étape

- [ ] `doctrine:schema:validate` ne remonte aucune erreur
- [ ] Les 3 entités existent avec leurs relations
- [ ] La contrainte d'unicité `(event, user)` est en place
- [ ] Les fixtures se chargent sans erreur et peuplent une base réaliste

---
