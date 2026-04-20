<form method="post" id="verander_rollen">
    verander gebruikersrol "<?= $user['username'] ?? "wawa" ?>"<br>
    <?php foreach($roles as $rolename => $role): ?>
        <?php 
            
        try {
            if ($this->auth->admin()->doesUserHaveRole($id, $role)) {
                $check = "checked";
            }
            else {
                $check = "";
            }
        }    
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown user ID');
        }
        ?>
        <label class="floater" for="<?= $rolename ?>"><?= $rolename ?></label>
        <input class="floater" type="checkbox" name="<?= $rolename ?>" <?= $check ?>><br>
    <?php endforeach; ?>
    <input type="submit" value="submit" id="submit_roles">
    <div id="submit"><a href="<?= $this->dir_backs ?>users" id="back">⬅ Terug naar de lijst</a></div> 
</form>

