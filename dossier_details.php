<div id="dossier">
    <h2>DOSSIER: <?= htmlspecialchars($prisoner['name']) ?></h2>
    
    <p><strong>Persoonlijke gegevens:</strong></p>
    <ul id="lijst">
        <li><strong>BSN:</strong> <?= htmlspecialchars($prisoner['bsn']) ?></li>
        <li><strong>Geboortedatum:</strong> <?= htmlspecialchars($prisoner['date_of_birth']) ?></li>
        <li><strong>Geslacht:</strong> <?= htmlspecialchars($prisoner['gender']) ?></li>
        <li><strong>Nationaliteit:</strong> <?= htmlspecialchars($prisoner['nationality']) ?></li>
    </ul><br><hr><br>

    <p><strong>Gevangen informatie:</strong></p>
    <ul id="lijst">
        <li><strong>Huidige Cel:</strong> <?= htmlspecialchars($prisoner['cell_id']) ?> (Vleugel: <?= htmlspecialchars($prisoner['vleugel'] ?? 'Onbekend') ?>)</li>
        <li><strong>Reden van laatste verplaatsing:</strong> 
        <?php 
            // We gebruiken htmlspecialchars voor veiligheid en nl2br om enters te tonen
            echo !empty($prisoner['reason']) ? nl2br(htmlspecialchars($prisoner['reden'])) : 'Geen reden opgegeven'; 
        ?>
        </li>
        <li><strong>Datum van opsluiting:</strong> <?= htmlspecialchars($prisoner['time_jailed']) ?></li>
        <li><strong>Reden van gevangenneming:</strong><br> 
            <i><?= nl2br(htmlspecialchars($prisoner['reason'])) ?></i>
        </li>
        <li><strong>Vrijlatingsdatum:</strong> <?= htmlspecialchars($prisoner['time_to_release']) ?></li>
    </ul><br>

    <h3>Documenten</h3>

    <ul id="lijst">
        <?php
        // Je moet in de controller $files ophalen uit inmate_files
        if (!empty($files)): 
            foreach ($files as $f): ?>
                <li>
                    <a href="<?= htmlspecialchars($f['file_path']) ?>" target="_blank">
                        <?= htmlspecialchars($f['file_name']) ?>
                    </a> (<?= $f['uploaded_at'] ?>)
                </li>
            <?php endforeach; 
        else: ?>
            <li>Geen bestanden geüpload.</li>
        <?php endif; ?>
    </ul>

    <hr>

    <form action="upload_file/<?= $prisoner['id'] ?>" method="post" enctype="multipart/form-data" id="lijst_1">
        <label>Bestand selecteren:</label>
        <input type="file" name="dossier_file" required>
        <input type="submit" value="Bestand uploaden">
    </form><br>

    <hr><br>
    <a href="<?= $this->dir_backs ?>prisoners" id="back">&nbsp;⬅ Terug naar de lijst&nbsp;&nbsp;</a>  
</div>