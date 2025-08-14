<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\User;
use App\Notifications\UserInvited;

class InviteUsers extends Command
{
    protected $signature = 'lotsen:invite
        {--name= : Name des Nutzers}
        {--email= : E-Mail des Nutzers}
        {--csv= : Pfad zu CSV (Spalten: name,email)}
        {--no-mail : Keine E-Mails senden, nur anlegen}';

    protected $description = 'Legt Nutzer mit GUID an und versendet Einladungslinks (einzeln oder via CSV).';

    public function handle()
    {
        $csv   = $this->option('csv');
        $name  = $this->option('name');
        $email = $this->option('email');
        $sendMail = !$this->option('no-mail');

        $created = [];

        if ($csv) {
            if (!file_exists($csv)) {
                $this->error('CSV nicht gefunden: '.$csv);
                return self::FAILURE;
            }
            $this->info('Importiere CSV: '.$csv);
            $fh = fopen($csv, 'r');
            $header = fgetcsv($fh);
            if (!$header or count($header) < 1) {
                $this->error('CSV-Header fehlt oder leer. Erwartet: name,email');
                return self::FAILURE;
            }
            $map = array_flip(array_map('trim', $header));
            while (($row = fgetcsv($fh)) !== false) {
                $n = $row[$map['name']]  ?? null;
                $e = $row[$map['email']] ?? null;
                if (!$n) { $this->warn('Übersprungen (kein Name)'); continue; }
                $created[] = $this->createOrFetchUser($n, $e, $sendMail);
            }
            fclose($fh);
        } else {
            if (!$name) {
                $this->error('Bitte --name angeben oder --csv verwenden.');
                return self::FAILURE;
            }
            $created[] = $this->createOrFetchUser($name, $email, $sendMail);
        }

        $this->table(['Name','E-Mail','GUID','Link'], array_map(function($u){
            $link = url('/dashboard/'.$u->guid);
            return [$u->name, $u->email, $u->guid, $link];
        }, array_filter($created)));

        $this->info('Fertig.');
        return self::SUCCESS;
    }

    protected function createOrFetchUser(string $name, ?string $email, bool $sendMail): User
    {
        $query = User::query()->where('name', $name);
        if ($email) $query->where('email', $email);
        $user = $query->first();

        if (!$user) {
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->guid = (string) Str::uuid();
            $user->save();
            $this->info("Angelegt: {$user->name} ({$user->email})");
        } else {
            if (!$user->guid) {
                $user->guid = (string) Str::uuid();
                $user->save();
            }
            $this->info("Vorhanden: {$user->name} ({$user->email})");
        }

        if ($sendMail && $user->email) {
            $user->notify(new UserInvited($user));
            $this->line("Einladungs‑Mail gesendet an {$user->email}");
        } elseif ($sendMail && !$user->email) {
            $this->warn("Keine E-Mail hinterlegt für {$user->name} – Mail übersprungen.");
        }

        return $user;
    }
}
