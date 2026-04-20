<div id="dossier">
    <h2>DOSSIER: <?= htmlspecialchars($prisoner['name']) ?></h2>
    
    <p><strong>Persoonlijke gegevens:</strong></p>
    <ul id="lijst">
        <li><strong>BSN:</strong> <?= htmlspecialchars($prisoner['bsn']) ?></li>
        <li><strong>Geboortedatum:</strong> <?= htmlspecialchars($prisoner['date_of_birth']) ?></li>
        <li><strong>Geslacht:</strong> <?= htmlspecialchars($prisoner['gender']) ?></li>
        <li><strong>Nationaliteit:</strong> <?= htmlspecialchars($prisoner['nationality']) ?></li>
    </ul><br><hr><br>

    <p><strong>Detentie informatie:</strong></p>
    <ul id="lijst">
        <li><strong>Huidige Cel:</strong> <?= htmlspecialchars($prisoner['cell_id']) ?> (Vleugel: <?= htmlspecialchars($prisoner['vleugel'] ?? 'Onbekend') ?>)</li>
        <li><strong>Datum van opsluiting:</strong> <?= htmlspecialchars($prisoner['time_jailed']) ?></li>
        <li><strong>Reden van detentie:</strong><br> 
            <i><?= nl2br(htmlspecialchars($prisoner['reason'])) ?></i>
        </li>
        <li><strong>Vrijlatingsdatum:</strong> <?= htmlspecialchars($prisoner['time_to_release'])?></li>
    </ul><br>

    <hr><br>
    <a href="history" id="back">&nbsp;&nbsp;⬅ Terug naar de lijst&nbsp;&nbsp;</a>
</div>