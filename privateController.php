<?php 
class privateController {

    private $auth;
    private $path;
    private $dir_backs;
    private $dbconn;
    private $userModel;

    public function __construct($auth, $path, $dir_backs, $dbconn) {
        // We slaan de benodigde variabelen op in de class, zodat we ze in alle functies kunnen gebruiken zonder ze telkens te hoeven doorgeven.
        $this->auth = $auth;
        $this->path = $path;
        $this->dir_backs = $dir_backs;
        $this->dbconn = $dbconn;
    }
    public function overview() {
        require_once "models/overview.php";
    }

    public function prisoners() {
        $search = $_GET['search'] ?? null;
        $sql = "SELECT inmate.id AS real_prisoner_id, inmate.*, cell.vleugel 
                FROM inmate 
                INNER JOIN cell ON inmate.cell_id = cell.id";

        if ($search) {
            $sql .= " WHERE inmate.name LIKE :search";
            $query = $this->dbconn->prepare($sql);
            $query->execute([':search' => "%$search%"]);
            $prisoners = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $prisoners = $this->dbconn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once "models/prisoners.php";
    }

    public function register() {
        // Alleen directeurs en admins kunnen gebruikers aanmaken, dus als je Cipier bent, word je teruggestuurd naar home.
        if (!$this->auth->hasAnyRole(\Delight\Auth\Role::DIRECTOR, \Delight\Auth\Role::ADMIN)) {
            header("location: " . $this->dir_backs . "home");
            exit;
        }
        require_once "models/register.php";
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            try {
                $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username']);
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                die('Invalid email address');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                die('Invalid password');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                die('User already exists');
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                die('Too many requests');
            }
        }
    }

    public function update() {
        // Deze functie is een voorbeeld van hoe je een update zou kunnen verwerken. Je zult deze moeten aanpassen aan jouw specifieke behoeften en database structuur. Deze manier is specifiek naar mijn structuur gemaakt, dus als jouw database of formulier anders is, moet je dit aanpassen.
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validatie (simpel voorbeeld)
            if (!empty($name) && !empty($email) && !empty($password)) {
                $success = $this->userModel->saveUser($name, $email, $password);
                
                if ($success) {
                    echo "Succesvol opgeslagen via MVC!";
                }
            } else {
                echo "Vul alle velden in.";
            }
        }

        require_once "models/userModel.php";
    }

    // De informatie van één specifieke gevangene
    public function view_dossier($id) {
        // 1. Haal gevangene op inclusief de tekst van de reden (met een JOIN)
        $query = $this->dbconn->prepare("SELECT inmate.*, verplaatsings_reden.reden FROM inmate 
            LEFT JOIN verplaatsings_reden ON inmate.verplaatsings_reden_id = verplaatsings_reden.id 
            WHERE inmate.id = :id
        ");
        $query->execute([':id' => $id]);
        $prisoner = $query->fetch(PDO::FETCH_ASSOC);

        if (!$prisoner) {
            die("Gevangene niet gevonden.");
        }

        // 2. Haal de bestanden op die bij deze gevangene horen
        $fileQuery = $this->dbconn->prepare("SELECT * FROM inmate_files WHERE inmate_id = :id");
        $fileQuery->execute([':id' => $id]);
        $files = $fileQuery->fetchAll(PDO::FETCH_ASSOC);

        // 3. Laad de view (zonder de 'header' redirect die de boel blokkeerde)
        require_once "models/dossier_details.php";
    }

    public function upload_file($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['dossier_file'])) {
            $file = $_FILES['dossier_file'];
            
            // 1. Map bepalen (zorg dat deze map bestaat in je project!)
            $uploadDir = 'uploads/inmate_' . $id . '/'; // Bijvoorbeeld: uploads/inmate_5/ voor gevangene met ID 5
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true); // Maak de map aan als deze nog niet bestaat
            }

            $fileName = basename($file['name']);
            // Voeg een timestamp toe om dubbele bestandsnamen te voorkomen
            $dbFilePath = $uploadDir . time() . '_' . $fileName; 

            // 2. Probeer het bestand te verplaatsen
            if (move_uploaded_file($file['tmp_name'], $dbFilePath)) {
                // 3. Alleen als het verplaatsen lukt, schrijven we naar de database
                $stmt = $this->dbconn->prepare("INSERT INTO inmate_files (inmate_id, file_name, file_path) VALUES (?, ?, ?)");
                $success = $stmt->execute([$id, $fileName, $dbFilePath]);
                
                if (!$success) {
                    die("Database fout: Kon de bestandsgegevens niet opslaan.");
                }
            } else {
                // Als dit verschijnt, heeft PHP geen rechten om in de map 'uploads' te schrijven
                die("Upload fout: Kon het bestand niet verplaatsen naar " . $uploadDir . ". Controleer je maprechten.");
            }
        }
        header("Location: " . $this->dir_backs . "prisoners" . $id);
        exit;
    }

    // De informatie van één specifieke vrijgelaten gevangene
    public function view_history_dossier($id) {
        // Haal de specifieke vrijgelaten gevangene op uit de historie
        $query = $this->dbconn->prepare("SELECT * FROM inmate_history WHERE id = :id");
        $query->execute([':id' => $id]);
        $prisoner = $query->fetch(PDO::FETCH_ASSOC);

        // Als de gevangene niet wordt gevonden, stuur terug naar de geschiedenislijst
        if (!$prisoner) {
            header("Location: " . $this->dir_backs . "history");
            exit;
        }

         // 2. HAAL HIER DE BESTANDEN OP
        $fileQuery = $this->dbconn->prepare("SELECT * FROM inmate_files WHERE inmate_id = :id");
        $fileQuery->execute([':id' => $prisoner['id']]);
        $files = $fileQuery->fetchAll(PDO::FETCH_ASSOC); // Deze variabele gebruik je in de view

        require_once "models/history_detail.php";
    }

    public function getHistory($search = null) {
    // Haal alle vrijgelaten gevangenen op, eventueel gefilterd op zoekterm
        $history = $this->dbconn->getHistory($search);
        require_once "models/history.php";   
    }

    public function history() {
        $search = $_GET['search'] ?? null;
        
        // We bouwen de query op basis van of er gezocht wordt of niet
        if ($search) {
            // Gebruik prepared statements om SQL-injectie te voorkomen
            $query = $this->dbconn->prepare("SELECT * FROM inmate_history WHERE name LIKE :search ORDER BY id DESC");
            $query->execute([':search' => "%$search%"]);
            $history = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Gewone query als er geen zoekterm is
            $history = $this->dbconn->query("SELECT * FROM inmate_history ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
        }

        require_once "models/history.php"; 
    }

    // Voeg deze functies toe aan de privateController class

    public function cells() {
        $search = $_GET['vleugel'] ?? null;
        
        if ($search) {
            $stmt = $this->dbconn->prepare("SELECT * FROM cell WHERE vleugel LIKE :vleugel ORDER BY id ASC");
            $stmt->execute([':vleugel' => "%$search%"]);
            $cells = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $cells = $this->dbconn->query("SELECT * FROM cell ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
        }
        
        require_once "models/cells.php";
    }

    public function view_cell_log($cell_id) {
        // 1. Haal de cel-informatie op (voor de titel en status in de view)
        $stmt = $this->dbconn->prepare("SELECT * FROM cell WHERE id = ?");
        $stmt->execute([$cell_id]);
        $cell = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cell) {
            die("Cel niet gevonden.");
        }

        // 2. Verwerk het formulier als er een nieuwe notitie wordt gepost
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['log_entry'])) {
            $entry = $_POST['log_entry'];
            $cipier_id = $this->auth->getUserId(); // Het ID van de ingelogde cipier

            // We gebruiken INSERT in plaats van UPDATE om een geschiedenis op te bouwen
            $insert = $this->dbconn->prepare("INSERT INTO cell_logs (cell_id, entry, cipier_id) VALUES (?, ?, ?)");
            $success = $insert->execute([$cell_id, $entry, $cipier_id]);

            if ($success) {
                // Redirect naar dezelfde pagina om 'dubbel posten' bij vernieuwen te voorkomen
                header("Location: " . $this->dir_backs . "view_cell_log/" . $cell_id);
                exit;
            } else {
                die("Fout bij het opslaan van de logboeknotitie.");
            }
        }

        // 3. Haal de VOLLEDIGE geschiedenis op van deze cel (nieuwste berichten eerst)
        $logsQuery = $this->dbconn->prepare("SELECT * FROM cell_logs WHERE cell_id = ? ORDER BY updated_at DESC");
        $logsQuery->execute([$cell_id]);
        $all_logs = $logsQuery->fetchAll(PDO::FETCH_ASSOC); 

        // 4. Laad de view en geef de variabelen ($cell en $all_logs) door
        require_once "models/view_cell_log.php";
    }

    public function my_account() {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // We gebruiken de namen uit de database die gebruikt wordt: username, email, password
            $newName     = $_POST['username'] ?? ''; 
            $newEmail    = $_POST['email'] ?? '';
            $newPassword = $_POST['password'] ?? '';

                if ($_SERVER["REQUEST_METHOD"] === "POST") {
                    try {
                        $userId = $this->auth->getUserId();

                        // 1. Naam aanpassen
                        if (!empty($newName)) {
                            $stmt = $this->dbconn->prepare("UPDATE users SET username = ? WHERE id = ?");
                            $stmt->execute([$newName, $userId]);
                        }

                        // 2. E-mail aanpassen (Nu via SQL ipv de niet-bestaande methode)
                        if (!empty($newEmail)) {
                            $stmt = $this->dbconn->prepare("UPDATE users SET email = ? WHERE id = ?");
                            $stmt->execute([$newEmail, $userId]);
                        }

                        // 3. Wachtwoord aanpassen (Deze methode bestaat WEL in Delight\Auth)
                        if (!empty($newPassword)) {
                            $this->auth->admin()->changePasswordForUserById($userId, $newPassword);
                        }

                        // Redirect na succes
                        header("Location: " . $this->dir_backs . "my_account");
                        exit;

                    } catch (Exception $e) {
                        die("Er is een fout opgetreden: " . $e->getMessage());
                    }
                }
            }

        // Gegevens ophalen voor de weergave
        $name = $this->auth->getUsername();
        $email = $this->auth->getEmail();

        require_once "models/my_account.php";
    }

    public function logout(){
        // log de gebruiker uit met de auth library en redirect naar home
        $this->auth->logOut();
        header("location: home");
        exit;
    }

    public function addprisoner() {
        // Deze functie is een voorbeeld van hoe je een formulier voor het toevoegen van een gevangene zou kunnen verwerken. Je zult deze moeten aanpassen aan jouw specifieke behoeften en database structuur. Deze manier is specifiek naar mijn structuur gemaakt, dus als jouw database of formulier anders is, moet je dit aanpassen.
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (!empty($_POST['bsn']) && !empty($_POST['date_of_birth'])) {
                
                // 1. Voeg de gevangene toe
                $query = $this->dbconn->prepare("INSERT INTO inmate (name, bsn, date_of_birth, cell_id, reason, gender, time_jailed, time_to_release, lengte_cm, nationality) VALUES (:name, :bsn, :date_of_birth, :cell_id, :reason, :gender, :time_jailed, :time_to_release, :lengte_cm, :nationality)");
                
                $query->bindParam(":name", $_POST["name"]);
                $query->bindParam(":bsn", $_POST["bsn"]);
                $query->bindParam(":date_of_birth", $_POST["date_of_birth"]);
                $query->bindParam(":cell_id", $_POST["cell_id"]);
                $query->bindParam(":reason", $_POST["reason"]);
                $query->bindParam(":gender", $_POST["gender"]);
                $query->bindParam(":time_jailed", $_POST["time_jailed"]);
                $query->bindParam(":time_to_release", $_POST["time_to_release"]);
                $query->bindParam(":lengte_cm", $_POST["lengte_cm"]);
                $query->bindParam(":nationality", $_POST["nationality"]);

                if ($query->execute()) {
                    // 2. BELANGRIJK: Zet de gekozen cel op 'bezet' (in_use = 1)
                    $updateCell = $this->dbconn->prepare("UPDATE cell SET in_use = 1 WHERE id = :cell_id");
                    $updateCell->bindParam(":cell_id", $_POST["cell_id"]);
                    $updateCell->execute();

                    // 3. Redirect naar het overzicht
                    header("Location: " . $this->dir_backs . "prisoners");
                    exit;
                }
            } else {
                echo "BSN en geboortedatum zijn verplicht!";
            }
        } 

        // Alleen vrije cellen ophalen voor de weergave in het formulier
        $query = $this->dbconn->prepare("SELECT * FROM cell WHERE in_use = 0");
        $query->execute();
        $cells = $query->fetchAll(PDO::FETCH_ASSOC);

        require_once "models/addprisoner.php";
    }

    public function deleteprisoner($id) {
        // 1. Haal de gevangene op
        $query = $this->dbconn->prepare("SELECT * FROM inmate WHERE id = :id");
        $query->execute([':id' => $id]);
        $prisoner = $query->fetch(PDO::FETCH_ASSOC);

        if ($prisoner) {
            try {
                // 2. Kopieer naar history
                $history = $this->dbconn->prepare("INSERT INTO inmate_history (id, name, bsn, date_of_birth, cell_id, gender, time_to_release) VALUES (:id, :name, :bsn, :dob, :cell_id, :gender, :ttr)");
                $history->execute([
                    ':id'      => $prisoner['id'],
                    ':name'    => $prisoner['name'],
                    ':bsn'     => $prisoner['bsn'],
                    ':dob'     => $prisoner['date_of_birth'],
                    ':cell_id' => $prisoner['cell_id'],
                    ':gender'  => $prisoner['gender'],
                    ':ttr'     => $prisoner['time_to_release']
                ]);

                // 3. Zet de cel op vrij (UPDATE cell tabel)
                $updateCell = $this->dbconn->prepare("UPDATE cell SET in_use = 0 WHERE id = :cell_id");
                $updateCell->execute([':cell_id' => $prisoner['cell_id']]);

                // 4. VERWIJDER de gevangene (DELETE uit inmate tabel)
                // We gebruiken hier specifiek het ID dat we uit de database hebben gehaald
                $delete = $this->dbconn->prepare("DELETE FROM inmate WHERE id = :id");
                $delete->execute([':id' => $prisoner['id']]);

                // 5. Redirect naar de pagina waar je de lijst ziet
                header("Location: " . $this->dir_backs . "prisoners");
                exit;

            } catch (PDOException $e) {
                die("Fout bij het verwijderen: " . $e->getMessage());
            }
        } else {
            die("Gevangene met ID $id niet gevonden.");
        }
    }

    public function editprisoners($id) {
            // 1. Haal de gevangene op via zijn eigen ID (niet cell_id)
            $query = $this->dbconn->prepare("SELECT * FROM inmate WHERE id = :id");
            $query->execute([':id' => $id]);
            $prisoner = $query->fetch(PDO::FETCH_ASSOC);

            if (!$prisoner) {
                die("Gevangene niet gevonden");
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $oldCellId = $prisoner['cell_id'];
            $newCellId = $_POST['cell_id'];
            $newRelease = $_POST['time_to_release'];
            $reasonId = $_POST['verplaatsings_reden_id']; // ID uit de dropdown

            $update = $this->dbconn->prepare("UPDATE inmate SET cell_id = :cell_id, time_to_release = :release, verplaatsings_reden_id = :reason_id WHERE id = :id");
            $success = $update->execute([
                ':cell_id'  => $newCellId,
                ':release'  => $newRelease,
                ':reason_id' => $reasonId,
                ':id'       => $id
            ]);

            if ($success) {
                // 3. Cel status bijwerken als de gevangene is verhuisd
                if ($oldCellId != $newCellId) {
                    // Maak de oude cel weer beschikbaar
                    $this->dbconn->prepare("UPDATE cell SET in_use = 0 WHERE id = ?")->execute([$oldCellId]);
                    // Zet de nieuwe cel op bezet
                    $this->dbconn->prepare("UPDATE cell SET in_use = 1 WHERE id = ?")->execute([$newCellId]);
                }

                // 4. Update de lokale variabele zodat de view de nieuwe data direct ziet
                $prisoner['cell_id'] = $newCellId;
                $prisoner['time_to_release'] = $newRelease;

                // 5. Redirect naar het overzicht om dubbele inzendingen te voorkomen
                header("Location: " . $this->dir_backs . "prisoners");
                exit;
            }
        }

        // 6. Haal beschikbare cellen op (inclusief de cel waar de gevangene nu in zit)
        $query = $this->dbconn->prepare("SELECT * FROM cell WHERE in_use = 0 OR id = :current_cell");
        $query->execute([':current_cell' => $prisoner['cell_id']]);
        $cells = $query->fetchAll(PDO::FETCH_ASSOC);
        
        require_once "models/editprisoners.php";
    }

     public function editroles($id = null) {
        // var_dump($id);
        //De functie wordt vóór de controle uitgevoerd, maar dat maakt niet uit, aangezien deze na de rolcontrole wordt aangeroepen.
        function addOrRemoveRole($auth, $id, $role, $remove) {
            if ($remove == TRUE) {
                try {
                    $auth->admin()->removeRoleForUserById($id, $role);
                }
                catch (\Delight\Auth\UnknownIdException $e) {
                    die('Unknown user ID');
                }
            } else {
                try {
                    $auth->admin()->addRoleForUserById($id, $role);
                }
                catch (\Delight\Auth\UnknownIdException $e) {
                    die('Unknown user ID');
                }
            }
        }
        $roles = [
            "director" => \Delight\Auth\Role::DIRECTOR,
            "admin" => \Delight\Auth\Role::ADMIN,
            "cipier" => \Delight\Auth\Role::EMPLOYEE,
        ];
        
        //alleen directeurs en admins kunnen rollen aanpassen, dus als je geen van beide bent, word je teruggestuurd naar home.
        if (!$this->auth->hasAnyRole(\Delight\Auth\Role::DIRECTOR, \Delight\Auth\Role::ADMIN)) {
            header("location: " . $this->dir_backs . "home");
            exit;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            foreach($roles as $role => $delightrole) {
                $remove = TRUE;
                if (array_key_exists($role, $_POST)) {
                    $remove = FALSE;
                }
                addOrRemoveRole($this->auth, $id, $delightrole, $remove);
            }    
            header("location: " . $this->dir_backs . "users");
            exit;
        }
        $query = $this->dbconn->prepare("SELECT id, username FROM users WHERE id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        require_once "models/editroles.php";
    }

    public function users() {
        if (!$this->auth->hasAnyRole(\Delight\Auth\Role::DIRECTOR, \Delight\Auth\Role::ADMIN)) {
            header("location: " . $this->dir_backs . "home");
            exit;
        }
        $query = $this->dbconn->prepare("SELECT id, username FROM users");
        $query->execute();
        $users = $query->fetchAll(PDO::FETCH_ASSOC);
        require_once "models/users.php";
    }
}