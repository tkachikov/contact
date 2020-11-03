<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Illuminate\Support\Carbon;


class Contact extends Seeder
{
    public $max_contacts = null;
    public $max_chunk    = null;

    public function __construct()
    {
        $this->max_contacts = (int)1e6;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date_start = Carbon::now();

        $this->max_chunk = $this->getMaxChunk();

        $db = DB::table('contacts');

        $output = new ConsoleOutput();

        $output->writeln('<info>Generate random data...</info>');

        $progress = new ProgressBar($output, $this->max_contacts);
        $progress->start();

        for ($i = 0; $i < $this->max_contacts; $i++) {
            $contacts[] = [
                'first_name' => Str::random(rand(1, 50)),
                'last_name' => Str::random(rand(1, 50)),
                'country' => Str::random(rand(1, 50)),
                'phone' => $this->getPhone()
            ];

            $progress->advance();
        }

        $progress->finish();

        $output->writeln("\r\n<info>Insert into database...</info>");

        $progress->setMaxSteps((int)($this->max_contacts / $this->max_chunk) ?? 1);
        $progress->start();

        foreach (collect($contacts)->chunk($this->max_chunk) as $contacts_chunk) {
            $db->insert($contacts_chunk->toArray());
            $date_between = Carbon::now()->diffInSeconds($date_start);
            $output->writeln("\r\n<info>Time: {$date_between}</info>");
            $progress->advance();
        }

        $progress->finish();
        $output->writeln("\r\n<info>Done!</info>");

        return 0;
    }

    public function getMaxChunk()
    {
        $driver = env('DB_CONNECTION') ?? Config::get('database.default');

        if (! method_exists($this, ($method = 'getMaxChunkFor' . Str::ucfirst($driver)))) {
            return (int)1e4;
        }

        return $this->{$method}();
    }

    public function getMaxChunkForMysql()
    {
        $pdo = [
            PDO::ATTR_EMULATE_PREPARES => true,
        ];

        $options_key = 'database.connections.mysql.options';
        $options = Config::get($options_key);

        foreach ($pdo as $key => $val) {
            if (! array_key_exists($key, $options)) {
                Config::set("{$options_key}.{$key}", $val);
            }
        }

        try {
            DB::statement("set global max_prepared_stmt_count = {$this->max_contacts}");
            return $this->max_contacts;
        } catch (\Exception $e) {
            return (int)DB::select("show variables like 'max_prepared_stmt_count'")[0]->Value;
        }
    }

    public function getPhone()
    {
        $max_length = rand(5, 50);

        for ($i = 0; $i < $max_length; $i++) {
            $phone[] = rand(0, 9);
        }

        return implode($phone);
    }
}
