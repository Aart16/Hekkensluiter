<?php
require_once 'vendor/autoload.php';

// Database instellingen
$host = '127.0.0.1';
$db   = 'Hekkensluiter';
$user = 'root'; // Pas dit aan
$pass = '';     // Pas dit aan
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "Verbonden met de database...\n";

    $faker = Faker\Factory::create('nl_NL');

    // 1. Cellen vullen
    echo "Cellen genereren...\n";
    $cellIds = range(1, 20);
    $vleugels = ['A1', 'A2', 'B1', 'B2', 'C1'];
    
    foreach ($cellIds as $id) {
        $stmt = $pdo->prepare("INSERT INTO cell (id, in_use, vleugel) VALUES (?, ?, ?)");
        $stmt->execute([$id, $faker->numberBetween(0, 1), $faker->randomElement($vleugels)]);
    }

    // 2. Users vullen
    echo "Users genereren...\n";
    $userIds = [];
    for ($i = 1; $i <= 10; $i++) {
        $stmt = $pdo->prepare("INSERT INTO users (email, password, username, status, verified, registered) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $faker->unique()->safeEmail,
            password_hash('password123', PASSWORD_BCRYPT),
            $faker->userName,
            1, // status
            1, // verified
            time() - $faker->numberBetween(1000, 100000)
        ]);
        $userIds[] = $pdo->lastInsertId();
    }

    // 3. Inmates vullen
    echo "Inmates genereren...\n";
    for ($i = 1; $i <= 15; $i++) {
        $timeJailed = $faker->dateTimeBetween('-1 year', 'now');
        $timeToRelease = (clone $timeJailed)->modify('+' . $faker->numberBetween(30, 1000) . ' days');
        
        $stmt = $pdo->prepare("INSERT INTO inmate (id, cell_id, name, reason, time_jailed, time_to_release, `bsn-number`, nationality, gender, lengte_cm, date_of_birth) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $i,
            $faker->randomElement($cellIds),
            $faker->name,
            $faker->sentence(6),
            $timeJailed->format('Y-m-d H:i:s'),
            $timeToRelease->format('Y-m-d H:i:s'),
            $faker->unique()->numberBetween(100000000, 999999999), // Gesimuleerd BSN
            'Nederlands',
            $faker->randomElement(['man', 'vrouw']),
            $faker->numberBetween(160, 205),
            $faker->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d H:i:s')
        ]);
    }

    // 4. Audit Logs vullen voor de eerste paar users
    echo "Audit logs genereren...\n";
    foreach ($userIds as $uid) {
        for ($j = 0; $j < 3; $j++) {
            $stmt = $pdo->prepare("INSERT INTO users_audit_log (user_id, event_at, event_type, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $uid,
                time() - $faker->numberBetween(0, 50000),
                $faker->randomElement(['login', 'logout', 'password_change', 'view_inmate']),
                $faker->ipv4,
                $faker->userAgent
            ]);
        }
    }

    // 5. Throttling data (voorbeeld)
    echo "Throttling data genereren...\n";
    $stmt = $pdo->prepare("INSERT INTO users_throttling (bucket, tokens, replenished_at, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        bin2hex(random_bytes(22)),
        10.0,
        time(),
        time() + 3600
    ]);

    echo "Klaar! De database is succesvol gevuld.\n";

} catch (\PDOException $e) {
    echo "Fout: " . $e->getMessage();
}