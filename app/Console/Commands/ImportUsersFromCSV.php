<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Input\InputArgument;

class ImportUsersFromCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-users-from-c-s-v';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file) || !is_readable($file)) {
            $this->error("File not found or not readable.");
            return;
        }

        $data = array_map('str_getcsv', file($file));
        $header = array_map('trim', $data[0]);
        unset($data[0]); // Remove the header row

        foreach ($data as $row) {
            $row = array_combine($header, $row);

            // Criar o usuário com os dados do CSV
            User::create([
                'employee_code' => $row['employee_code'],
                'name' => $row['name'],
                'cpf' => $row['cpf'],
                'password' => Hash::make($row['cpf']), // Hash do CPF como senha
                'secretaria' => $row['Secretaria'],
                'departamento_id' => $row['departamento_id'],
                'local' => $row['Local'],
            ]);

            $this->info("Imported user: {$row['name']}");
        }

        $this->info("All users have been imported successfully.");
    }

    protected function configure()
    {
        $this->setName('app:import-users-from-c-s-v')
            ->setDescription('Import users from a CSV file')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to the CSV file');
    }
}
