<div id="titel">gevangenen</div><br>

<?php if($this->auth->hasAnyRole(\Delight\Auth\Role::DIRECTOR, \Delight\Auth\Role::ADMIN)): ?>
    <div id="gevangen">voeg gevangene toe
        <a class="a" href="addprisoner" id="nieuwe_gevangene">nieuwe gevangene</a>
    </div><br><br>
<?php endif; ?>

<form method="GET" action="">
    <input type="text" name="search" placeholder="Zoek op naam..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" id="gevangene">
    <button type="submit" id="gevangenen1">Zoeken</button>
</form><br>
 
<table id="tafel">
    <tr>
        <th>naam</th>
        <th>afdeling</th>
        <th>cel</th>
        <th>vrijlating</th>
        <th>gender</th>
        <th>aanpassen</th>
        <th>dossier</th>
        <?php if($this->auth->hasRole(\Delight\Auth\Role::DIRECTOR)): ?>
        <th>verwijderen</th>
        <?php endif; ?>

    </tr>
    <?php foreach($prisoners as $prisoner): ?>
        <tr>
            <td><?= $prisoner['name'] ?></td>
            <td><?= $prisoner['vleugel'] ?></td>
            <td><?= $prisoner['cell_id'] ?></td>
            <td><?= $prisoner['time_to_release'] ?></td>
            <td><?= $prisoner['gender'] ?></td>
            <td class="bijna_weekend"><a class="bijna_weekend" href="editprisoners/<?= $prisoner['real_prisoner_id'] ?>">edit</a></td>
            <td class="bijna_weekend"><a class="bijna_weekend" href="view_dossier/<?= $prisoner['real_prisoner_id'] ?>">dossier</a></td>
            <?php if($this->auth->hasRole(\Delight\Auth\Role::DIRECTOR)): ?>
            <td class="bijna_weekend"><a class="bijna_weekend" href="deleteprisoner/<?= $prisoner['real_prisoner_id'] ?>" 
            onclick="return confirm('Weet je zeker dat je deze gevangene wilt vrijlaten? De cel komt dan weer vrij.'), confirm('de gevangene is vrijgelaten en de gevangenis is weer vrij.')">delete</a></td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>

    <div id="archief">
        <a href="history" class="dossiers">
            &nbsp;Bekijk dossiers van vrijgelaten gevangenen
        </a>
    </div>

