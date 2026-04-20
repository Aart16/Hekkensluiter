<h2>Gevangene bewerken: <?= $prisoner['name'] ?></h2>

<form method="post" class="add_prisoner_form">
    <label>Nieuwe cel:</label>
    <select name="cell_id">
        <?php foreach($cells as $cell): ?>
            <option value="<?= $cell['id'] ?>" <?= ($cell['id'] == $prisoner['cell_id']) ? 'selected' : '' ?>>
                Vleugel: <?= $cell['vleugel'] ?> - Cel: <?= $cell['id'] ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <label>Reden voor verplaatsing:</label>
    <input type="text" name="reason">
    <br>

    <label>Nieuwe release datum:</label>
    <input type="date" name="time_to_release" value="<?= $prisoner['time_to_release'] ?>">
    <br>

    <input type="submit" value="Wijzigingen opslaan" onclick="confirm('de gevangene is succesvol bijgewerkt!')">
</form>