<h2>Cellen Overzicht</h2>

<form method="GET" action="">
    <select name="vleugel" id="gevangene">
        <option value="">Alle afdelingen/vleugels</option>
        <option value="A" <?= ($_GET['vleugel'] ?? '') == 'A' ? 'selected' : '' ?>>Vleugel A</option>
        <option value="B" <?= ($_GET['vleugel'] ?? '') == 'B' ? 'selected' : '' ?>>Vleugel B</option>
        <option value="C" <?= ($_GET['vleugel'] ?? '') == 'C' ? 'selected' : '' ?>>Vleugel C</option>
    </select>
    <button type="submit" id="gevangenen1">Filteren</button>
</form>

<table id="cell_tabel">
    <thead>
        <tr>
            <th>Cel Nummer</th>
            <th>Vleugel</th>
            <th>Status</th>
            <th>Acties</th>
        </tr>
    </thead><br>
    <tbody>
        <?php foreach ($cells as $cell): ?>
            <tr>
                <td><strong>Cel <?php echo $cell['id']; ?></strong></td>
                <td><?php echo $cell['vleugel'] ?? 'Onbekend'; ?></td>
                <td>
                    <?php if ($cell['in_use'] == 1): ?>
                        <span style="color: red; font-weight: bold;">● Bezet</span>
                    <?php else: ?>
                        <span style="color: green; font-weight: bold;">● Vrij</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="view_cell_log/<?php echo $cell['id']; ?>" id="bewerken" >
                        Logboek bekijken/bewerken
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>