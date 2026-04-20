<h2>Geschiedenis van vrijgelaten gevangenen</h2><br>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Zoek op naam..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" id="gevangene">
    <button type="submit" id="gevangenen1">Zoeken</button>
</form><br>

<table id="tafel">
    <thead>
        <tr>
            <th>Naam</th>
            <th>BSN</th>
            <th>Geslacht</th>
            <th>Oude Cel</th>
            <th>Vrijlatingsdatum</th>

        </tr>
    </thead>
    <tbody>
        <?php if (!empty($history)): ?>
            <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['bsn']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['cell_id']) ?></td>
                    <td><?= htmlspecialchars($row['time_to_release']) ?></td>
                    <td>
                        <a href="view_history_dossier/<?= $row['id'] ?>" class="dossier">
                            Bekijk Dossier
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">Er zijn nog geen gearchiveerde gevangenen gevonden.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<br>
<a href="prisoners" id="back">&nbsp;&nbsp;Terug naar actieve gevangenen&nbsp;&nbsp;</a>