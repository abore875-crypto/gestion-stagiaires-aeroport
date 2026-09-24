<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($titrePage) ? htmlspecialchars($titrePage) . ' — ' : '' ?>Aéroport Stages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="app-body">

<?php
    $routeActuelle = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    $role = Security::currentUserRole();

    $navItems = [];
    if ($role === 'stagiaire') {
        $navItems = [
            ['route' => 'stage/suivi',        'icon' => 'bi-search',              'label' => 'Suivi de ma demande'],
            ['route' => 'stage/journal',      'icon' => 'bi-journal-text',        'label' => 'Mon journal'],
            ['route' => 'stage/rapport',      'icon' => 'bi-file-earmark-text',   'label' => 'Mon rapport'],
            ['route' => 'stage/attestation',  'icon' => 'bi-mortarboard',         'label' => 'Mon attestation'],
        ];
    } elseif ($role === 'rh') {
        $navItems = [
            ['route' => 'rh/tableau-de-bord', 'icon' => 'bi-clipboard-data',      'label' => 'Tableau de bord'],
            ['route' => 'rh/agents',          'icon' => 'bi-person-badge',        'label' => 'Agents'],
            ['route' => 'rh/rapports',        'icon' => 'bi-file-earmark-check',  'label' => 'Rapports'],
            ['route' => 'rh/notes-service',   'icon' => 'bi-file-earmark-text',   'label' => 'Notes de service'],
            ['route' => 'rh/attestations',    'icon' => 'bi-mortarboard',         'label' => 'Attestations'],
        ];
    } elseif ($role === 'agent') {
        $navItems = [
            ['route' => 'agent/tableau-de-bord', 'icon' => 'bi-people', 'label' => 'Mes stagiaires'],
        ];
    }
?>

<div class="app-layout">
    <aside class="app-sidebar offcanvas-md offcanvas-start" tabindex="-1" id="appSidebar">
        <a href="/" class="app-sidebar-brand">
            <img src="/images/logo-aeroports-mali.png" alt="Aéroports du Mali">
        </a>
        <nav class="app-sidebar-nav">
            <?php foreach ($navItems as $item): ?>
                <a href="/<?= $item['route'] ?>" class="app-nav-link <?= $routeActuelle === $item['route'] ? 'active' : '' ?>">
                    <i class="bi <?= $item['icon'] ?>"></i>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <a href="/deconnexion" class="app-nav-link app-nav-logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Déconnexion</span>
        </a>
    </aside>

    <div class="app-main">
        <header class="app-topbar">
            <button class="btn btn-outline-secondary btn-sm d-md-none" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
            <span class="app-topbar-title"><?= isset($titrePage) ? htmlspecialchars($titrePage) : '' ?></span>
            <span class="app-topbar-role badge text-uppercase">
                <?= htmlspecialchars($role ?? '') ?>
            </span>
        </header>
        <main class="app-content">