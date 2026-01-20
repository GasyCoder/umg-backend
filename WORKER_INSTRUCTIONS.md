# 📧 Guide de Correction : Délivrabilité Newsletter

Ce guide explique comment corriger les problèmes de timeout et de délivrabilité.

## 🚨 Diagnostic Confirmé
Le système est configuré en mode **SYNCHRONE** (`QUEUE_CONNECTION=sync`).
C'est la SEULE raison pour laquelle vos tâches CRON ne traitaient rien : les emails étaient envoyés "en direct" (et échouaient) au lieu d'aller dans la file d'attente pour le CRON.

---

## ✅ Étape Unique : Activer la File d'Attente
Vos tâches CRON sont déjà **parfaitement configurées**. Ne les touchez pas.
Vous devez simplement dire à Laravel d'utiliser la base de données pour stocker les emails en attente.

1. **Modifiez le fichier `.env`** sur votre serveur :

```bash
nano /home/flbe4406/public_html/api-umg/.env
```
(Adaptez le chemin si nécessaire)

2. Changez cette ligne :

```ini
# AVANT (BUG)
QUEUE_CONNECTION=sync

# APRÈS (CORRECTION)
QUEUE_CONNECTION=database
```

3. **Sauvegardez** et videz le cache :

```bash
cd /home/flbe4406/public_html/api-umg
php artisan config:clear
```

---

## 🚀 Résultat Immédiat
Dès que vous ferez cette modification :
1. L'admin cliquera sur "Envoyer".
2. La page répondra **instantanément** ("Envoi planifié").
3. Votre CRON (`queue:work`) qui tourne chaque minute verra les jobs dans la table `jobs` et commencera à les envoyer un par un (1 email/seconde pour sécurité).
4. Suivi : Vous verrez la barre de progression avancer petit à petit dans mes stats admin.

## 🛡️ Sécurité
J'ai patché le code (`SendNewsletterToSubscriberJob.php`) pour qu'il respecte une pause de **1 seconde** entre chaque email.
Cela garantit que votre hébergeur ne bloquera pas l'envoi pour "spam" ou surcharge.
Temps total pour 513 emails : **~8-9 minutes**.

## 🧪 Vérification
J'ai créé une commande de diagnostic. Vous pouvez la lancer pour vérifier que `QUEUE_CONNECTION` est bien passée à `database` :

```bash
php artisan diagnose:newsletter
```
