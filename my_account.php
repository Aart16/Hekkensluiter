<div id="account_info">
    <div><strong>Huidige naam:</strong> <?= $name ?></div>
    <div><strong>Huidige email:</strong> <?= $email ?></div><br>

    <form action="my_account" method="POST">
        <label for="name" id="gebruikersnaam_label">Nieuwe gebruikersnaam:</label>
        <input type="text" name="username" id="gebruikersnaam"><br>
        
        <label for="email" id="e-mail_label">Nieuwe e-mail:</label>
        <input type="email" name="email" id="e-mail"><br>
        
        <label for="password" id="wachtwoord_label">Nieuwe wachtwoord:</label>
        <input type="password" name="password" id="wachtwoord"><br>
        
        <input type="submit" name="submit_update" value="Wijzigingen opslaan" id="wijzigingen">
    </form>
</div>