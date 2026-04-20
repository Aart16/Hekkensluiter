<form method="post" class="add_prisoner_form">
    <label for="name">Naam:</label>
    <input type="text" name="name" required><br>
    <label for="bsn">BSN-nummer:</label>
    <input type="number" name="bsn" minlength="9" maxlength="9" required><br>
    <label for="date_of_birth">Geboortedatum:</label>
    <input type="date" name="date_of_birth" required><br>
    <label for="cell_id">Cel:</label>
    <select name="cell_id" required>
        <option value="">--Selecteer een vrije cel--</option>
        <?php foreach($cells as $cell): ?> 
            <option value="<?= $cell['id'] ?>">
                <?= 'Vleugel: ' . $cell['vleugel'] . ' | Cel: ' . $cell['id'] ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    <label for="gender">Geslacht:</label>
    <input type="text" name="gender"><br>
    <label for="lengte_cm">Lengte (cm):</label>
    <input type="number" name="lengte_cm" maxlength="3"><br>
    <label for="nationality">Nationaliteit:</label>
    <input type="text" name="nationality"><br>
    <label for="reason">Reden van opsluiting:</label>
    <input type="text" name="reason"><br>
    <label for="time_jailed">Datum van opsluiting:</label>
    <input type="date" name="time_jailed"><br>
    <label for="time_to_release">Wordt vrijgelaten op:</label>
    <input type="date" name="time_to_release"><br>
    <input type="submit" value="voeg gevangene toe" id="wijzigingen">
    <a href="<?= $this->dir_backs ?>prisoners"><input type="button" value="terug" id="wijzigingen"></a>
</form>