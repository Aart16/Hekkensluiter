 <table>
    <tr>
        <th>naam</th>
        <th>aanpassen</th>
    </tr>
    <?php foreach($users as $user): ?>
        <tr>
            <td id="namen"><?= $user['username'] ?></td>
            <td id="edit_roles"><a href="editroles/<?= $user['id'] ?>"id="edit_roles">edit</a></td>
        </tr>
    <?php endforeach; ?>
 </table>