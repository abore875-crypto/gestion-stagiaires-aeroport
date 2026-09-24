<?php $titrePage = 'Accueil'; require __DIR__ . '/partials/header.php'; ?>

<div class="row align-items-center g-5 rounded-4 p-4 p-md-5 mb-5"
     style="background: linear-gradient(rgba(0,20,10,0.72), rgba(0,20,10,0.72)), url('/images/hero-aeroport.jpg') center/cover no-repeat; color: #fff;">
    <div class="col-lg-7">
              <span class="badge mb-3" style="background: #14B53A; color: #fff;">Aéroport International Président Modibo Keïta</span>
        <h1 class="fw-medium mb-3">Gérez votre stage à l'aéroport, du dépôt à l'attestation</h1>
        <p class="mb-4" style="color: #e0e0e0;">
            Déposez votre demande en ligne, suivez son traitement en temps réel,
            remplissez votre journal de bord et téléchargez votre attestation
            dès la fin du stage.
        </p>
        <div class="d-flex gap-2">
         <a href="/demande/deposer" class="btn btn-primary">Déposer une demande</a>
         <a href="/demande/suivi" class="btn btn-outline-secondary">Suivre ma demande</a>  
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-medium">Ma demande de stage</span>
                    <span class="badge bg-warning-subtle text-warning-emphasis">En attente</span>
                </div>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">✅ Demande soumise</li>
                    <li class="mb-2">🕓 En cours d'examen par le RH</li>
                    <li class="mb-2 text-muted">Affectation à un encadrant</li>
                    <li class="text-muted">Attestation de fin de stage</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<hr class="my-5">

<h2 class="h4 fw-medium text-center mb-4">Comment ça marche</h2>
<div class="row g-3">
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <p class="fw-medium mb-1">1. Déposez votre demande</p>
            <p class="small text-secondary mb-0">CV, lettre de motivation et informations personnelles.</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <p class="fw-medium mb-1">2. Suivez le traitement</p>
            <p class="small text-secondary mb-0">Consultez le statut de votre dossier à tout moment.</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <p class="fw-medium mb-1">3. Remplissez votre journal</p>
            <p class="small text-secondary mb-0">Activités, difficultés et compétences validées par votre encadrant.</p>
        </div></div>
    </div>
    <div class="col-md-3">
        <div class="card h-100"><div class="card-body">
            <p class="fw-medium mb-1">4. Téléchargez l'attestation</p>
            <p class="small text-secondary mb-0">Générée après validation de votre rapport.</p>
        </div></div>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>