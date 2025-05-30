#!/bin/bash

echo "📂 Branch base disponibili: main, dev, hot-fix"
read -p "👉 Da quale branch vuoi partire? " BASE_BRANCH

# Verifica esistenza del branch di partenza
if ! git show-ref --verify --quiet refs/heads/$BASE_BRANCH; then
  echo "❌ Il branch '$BASE_BRANCH' non esiste localmente. Provo a fare fetch..."
  git fetch origin $BASE_BRANCH:$BASE_BRANCH 2>/dev/null || {
    echo "❌ Il branch '$BASE_BRANCH' non esiste nemmeno su remoto."
    exit 1
  }
fi

# Aggiorna il branch di partenza
git checkout $BASE_BRANCH
git pull origin $BASE_BRANCH

echo "📌 Tipi di branch: feature, bugfix, hotfix"
read -p "👉 Tipo di branch? " TYPE

read -p "📝 Nome del branch (es. login-form): " NAME

BRANCH_NAME="${TYPE}/${NAME}"

# Crea e passa al nuovo branch
git checkout -b $BRANCH_NAME

# Push con tracking
git push -u origin $BRANCH_NAME

echo "✅ Branch '$BRANCH_NAME' creato da '$BASE_BRANCH' e pubblicato."
