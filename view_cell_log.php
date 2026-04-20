<?php
/**
 * View: view_cell_log.php
 * Toont het invoerveld EN de volledige geschiedenis van alle berichten.
 */
?>

<div class="container">
    <a href="<?php echo $this->dir_backs; ?>cells"  id="submit">
        &nbsp;&nbsp;⬅ Terug naar de lijst met cellen&nbsp;&nbsp;&nbsp;
    </a>

    <h2>Logboek beheren: Cel <?php echo htmlspecialchars($cell['id']); ?></h2>

    <form method="POST" action="">
        <div>
            <label for="log_entry">
                Nieuwe notitie toevoegen:
            </label><br>
            <textarea 
                name="log_entry" 
                id="log_entry" 
                rows="5" 
                placeholder="Wat is er gebeurd? Typ het hier..."
            ></textarea><br>
            <button type="submit">
                Bericht Opslaan
            </button>
        </div><br>
    </form>

    <hr><br>

    <h3>Geschiedenis van logboeknotities</h3>

    <?php if (!empty($all_logs)): ?>
        <?php foreach ($all_logs as $entry): ?>
            <div>
                <div>
                    <strong>Datum:</strong> <?php echo date('d-m-Y H:i', strtotime($entry['updated_at'])); ?> | 
                    <strong>Door Cipier met ID:</strong> <?php echo htmlspecialchars($entry['cipier_id']); ?>
                </div>
                <p>
                    <?php echo htmlspecialchars($entry['entry']); ?>
                </p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Cel.</p>
    <?php endif; ?>

</div>