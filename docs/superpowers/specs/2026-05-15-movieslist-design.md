# MoviesList — Design Spec
*Date : 2026-05-15*

## Vue d'ensemble

Application mobile Android (NativePHP) permettant de gérer une liste personnelle de films et séries. Les données sont enrichies automatiquement depuis l'API TMDB en français. Tout est stocké localement sur l'appareil, sans compte ni synchronisation réseau.

---

## Stack technique

| Couche | Technologie |
|---|---|
| Runtime mobile | NativePHP Mobile (dernière version) |
| Backend | Laravel 11 |
| UI réactive | Livewire 3 + Alpine.js |
| Design system | Preline UI (Tailwind CSS v4) |
| Base de données | SQLite (locale) |
| API films | TMDB API v3 (`language=fr-FR`) |

---

## Architecture de données

### Modèles

**`profiles`**
- `id`, `name` (prénom de l'utilisateur)
- Un seul enregistrement. Créé à l'onboarding.

**`movies`** — cache local des données TMDB
- `id`, `tmdb_id` (unique), `type` (`movie` | `tv`)
- `title`, `original_title`, `poster_path`, `backdrop_path`
- `synopsis`, `release_date`, `duration` (minutes — durée du film, ou durée moyenne d'un épisode pour les séries)
- `genres` (JSON array), `tmdb_data` (JSON complet, pour usage futur)

**`watchlist_entries`**
- `id`, `movie_id` (FK → movies)
- `status` (`to_watch` | `watched`)
- `rating` (integer 1–10, nullable)
- `comment` (text, nullable)
- `watched_at` (date, nullable)
- `is_favorite` (boolean, default false)

**`co_watchers`**
- `id`, `name`

**`watchlist_entry_co_watcher`** (pivot)
- `watchlist_entry_id`, `co_watcher_id`

### Relations
- Un `WatchlistEntry` appartient à un `Movie`
- Un `WatchlistEntry` a plusieurs `CoWatcher` (many-to-many)
- Un `Movie` peut apparaître une seule fois dans la watchlist (unique sur `movie_id`)

---

## Navigation

**Structure :**
- 2 tabs principaux : **Accueil** (Dashboard) et **Mes films** (Listing)
- Avatar cliquable en haut à droite → **Paramètres**
- Onboarding affiché une seule fois au premier lancement (flag `profile` inexistant)

**Flux utilisateur :**
```
[Premier lancement]
  └─ Onboarding (saisie prénom) → Dashboard

[Lancement suivant]
  └─ Dashboard (tab actif par défaut)
       ├─ Tab "Mes films" → Listing
       └─ Avatar → Paramètres
            └─ Retour ← vers le tab précédent
```

---

## Écrans

### 1. Onboarding
- Affiché uniquement si aucun profil n'existe en base
- Logo de l'app + message de bienvenue
- Champ texte "Ton prénom"
- Bouton "C'est parti" → crée le profil et redirige vers le Dashboard

### 2. Dashboard (Tab 1)

Sections verticales scrollables :

**Favoris** (`is_favorite = true`)
- Carrousel horizontal de cartes affiches
- Vide : message d'invitation à marquer un favori

**Vus récemment** (`status = watched`, triés par `watched_at` DESC, limite 10)
- Carrousel horizontal de cartes affiches

**Pioche du jour** (`status = to_watch`)
- Une seule carte tirée aléatoirement parmi les titres "À voir"
- Bouton "Repioche" pour en avoir un autre (sans rechargement de page, Livewire)
- Vide : masqué si la liste "À voir" est vide

**Tu pourrais aimer** (TMDB)
- Basé sur les 3 derniers titres vus (`watched_at` DESC)
- Appel TMDB `/movie/{id}/recommendations` ou `/tv/{id}/recommendations`
- Affiche 6 suggestions maximum, excluant les titres déjà dans la watchlist
- Vide : masqué si aucun titre vu

### 3. Listing (Tab 2)

**Filtres rapides** (chips en haut) :
- Tous / À voir / Vus

**Filtres avancés** (panneau dépliable via bouton "⚙ Filtres") :
- Type : Film / Série
- Genre : liste des genres présents en base
- Durée : plage via slider (ex. 0–60 min, 60–120 min, 120+ min)

**Affichage** :
- Grille 3 colonnes d'affiches (poster_path TMDB)
- Badge statut sur chaque affiche (vu ✓ / à voir)
- Tap sur une affiche → page Détail

**Bouton flottant (+)** (FAB) en bas à droite → ouvre la modal de recherche TMDB

### 4. Recherche & Ajout (Modal)

**Étape 1 — Recherche TMDB**
- Champ de recherche avec debounce 300 ms
- Appel `TmdbService::search($query)` → résultats en français
- Liste de résultats : affiche miniature + titre + année + type
- Tap → Étape 2

**Étape 2 — Formulaire d'ajout**
- Affiche, titre, synopsis (pré-remplis depuis TMDB, non éditables)
- Statut : radio "À voir" / "Vu"
- Si "Vu" : champ date de visionnage, note /10 (stepper), commentaire (texte libre), sélection co-watchers (checkboxes parmi la liste existante)
- Bouton "Ajouter à ma liste" → sauvegarde `Movie` + `WatchlistEntry` → ferme la modal → rafraîchit le listing

### 5. Détail film (Page)

- Backdrop + affiche TMDB en header
- Titre, année, durée, genres (badges)
- Synopsis complet
- Section "Mon avis" : note, commentaire, date de visionnage, co-watchers
- Bouton favori (étoile toggle)
- Bouton "Modifier" → ouvre le formulaire d'édition (même composant que l'ajout, pré-rempli)

### 6. Paramètres (Page via avatar)

- **Profil** : affichage du prénom avec bouton "Modifier" (inline edit)
- **Co-watchers** : liste des noms avec bouton suppression (✕) + bouton "Ajouter" (champ texte inline)

---

## Services

### `TmdbService`

Responsabilités :
- `search(string $query): array` — recherche films + séries
- `getMovie(int $tmdbId): array` — détail d'un film
- `getTvShow(int $tmdbId): array` — détail d'une série
- `getRecommendations(int $tmdbId, string $type): array` — recommandations

Configuration :
- Clé API stockée dans `.env` (`TMDB_API_KEY`)
- Langue systématiquement `fr-FR`, région `FR`
- Timeout 5 secondes
- Les résultats de recherche ne sont pas mis en cache (toujours frais)
- Les données d'un titre ajouté à la watchlist sont persistées dans la table `movies` (cache SQLite)

---

## Composants Livewire

| Composant | Rôle |
|---|---|
| `Onboarding` | Saisie du prénom, création du profil |
| `Dashboard` | Assemblage des 4 sections du dashboard |
| `MovieList` | Listing avec filtres réactifs |
| `MovieSearch` | Modal recherche TMDB + formulaire ajout |
| `MovieDetail` | Page détail avec édition |
| `Settings` | Gestion profil + co-watchers |

---

## Hors scope (v1)

- Export de liste
- Synchronisation entre appareils
- Comptes utilisateurs
- Notifications
- Mode hors-ligne pour TMDB (les affiches nécessitent une connexion)
