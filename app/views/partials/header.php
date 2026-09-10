<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titrePage) ? htmlspecialchars($titrePage) . ' — ' : '' ?>Aéroport Stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
    <div class="container">
                <a class="navbar-brand fw-medium d-flex align-items-center gap-2" href="/">
            <img src="/images/logo-aeroports-mali.png" alt="Aéroports du Mali" style="height: 32px;">
            <span>Aéroport Stages</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto"></ul>
            <ul class="navbar-nav">
                                    <?php if (Security::isLoggedIn()): ?>
                    <?php if (Security::currentUserRole() === 'stagiaire'): ?>
                        <li class="nav-item"><a class="nav-link" href="/stage/suivi">Suivi de ma demande</a></li>
                        <li class="nav-item"><a class="nav-link" href="/stage/journal">Mon journal</a></li>
                        <li class="nav-item"><a class="nav-link" href="/stage/rapport">Mon rapport</a></li>
                                               <li class="nav-item"><a class="nav-link" href="/stage/attestation">Mon attestation</a></li> 
                    <?php elseif (Security::currentUserRole() === 'rh'): ?>
                        <li class="nav-item"><a class="nav-link" href="/rh/tableau-de-bord">Tableau de bord</a></li>
                        <li class="nav-item"><a class="nav-link" href="/rh/agents">Agents</a></li>
                        <li class="nav-item"><a class="nav-link" href="/rh/rapports">Rapports</a></li>
                                                <li class="nav-item"><a class="nav-link" href="/rh/attestations">Attestations</a></li>
                    <?php elseif (Security::currentUserRole() === 'agent'): ?>
                        <li class="nav-item"><a class="nav-link" href="/agent/tableau-de-bord">Mes stagiaires</a></li>
                        <li class="nav-item"><a class="nav-link" href="/agent/rapport">Rapports</a></li>
                    <?php endif; ?>
                    <li class="nav-item"><a class="btn btn-outline-secondary btn-sm ms-2" href="/deconnexion">Déconnexion</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="/connexion">Connexion</a></li>
                    <li class="nav-item"><a class="btn btn-primary btn-sm ms-2" href="/inscription">Créer un compte</a></li>
                <?php endif; ?>                      
            </ul>
        </div>
    </div>
</nav>

<main class="container py-5">