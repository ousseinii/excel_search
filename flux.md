┌──────────────────────────────────────────────────────────────────────┐
│ DÉBUT DU PROCESSUS │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ INPUT — Fichier Crypté │
│ │
│ ┌─────────────────┐ Décryptage ┌──────────────────────┐ │
│ │ fichier.crypt │ ──────────────────► │ Logiciel tiers │ │
│ │ (format inconnu)│ │ (hors périmètre) │ │
│ └─────────────────┘ └──────────┬───────────┘ │
│ │ │
│ ▼ │
│ OUTPUT : code/ID unique │
│ Exemple : TRX-2024-00123 │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ ÉTAPE MANUELLE — Copie du Code │
│ │
│ 👤 Copie manuelle du code par l'utilisateur │
│ (pas d'intégration API / clipboard automatique) │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ ÉTAPE MANUELLE — Ouverture des fichiers source │
│ │
│ 📂 L'utilisateur ouvre manuellement N fichiers Excel │
│ │
│ source_1.xlsx ──┐ │
│ source_2.xlsx ──┼──► Fichiers ouverts dans Excel │
│ source_N.xlsx ──┘ │
│ │
│ Structure des fichiers : colonnes hétérogènes │
│ Clé commune : colonne ID unique │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ TRAITEMENT — Recherche / Filtrage │
│ │
│ 🔍 Recherche manuelle (CTRL+F ou filtre Excel) │
│ WHERE id_colonne = 'TRX-2024-00123' │
│ sur chaque fichier source │
│ │
│ ┌──────────────────────────┐ │
│ │ nbLignes > 0 ? │ │
│ └────────┬─────────────────┘ │
│ │ │
│ ┌──────────┴──────────┐ │
│ ▼ ▼ │
│ TRUE FALSE │
│ Continuer EXIT — Aucun │
│ vers ETL résultat trouvé │
└──────────────────────────────────┬───────────────────────────────────┘
│ (TRUE)
▼
┌──────────────────────────────────────────────────────────────────────┐
│ ETL — Extract / Transform / Load │
│ │
│ EXTRACT │
│ ───────── │
│ Sélection manuelle + copie des lignes matchées │
│ depuis chaque fichier source │
│ │
│ TRANSFORM │
│ ────────── │
│ Concaténation de colonnes : │
│ col_A & " - " & col_B → champ résultat │
│ (règles métier définies dans le cahier des charges) │
│ │
│ LOAD │
│ ────── │
│ Collage manuel dans brouillon.xlsx (feuille tampon) │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ OUTPUT — Fichier Résultat │
│ │
│ 📄 brouillon.xlsx │
│ └── Feuille "Données" → lignes brutes copiées │
│ └── Feuille "Résultat" → données transformées / concaténées │
└──────────────────────────────────┬───────────────────────────────────┘
│
▼
┌──────────────────────────────────────────────────────────────────────┐
│ FIN DU PROCESSUS │
└──────────────────────────────────────────────────────────────────────┘
