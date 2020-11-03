<?php

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;


class Contact extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db = DB::table('contacts');

        $max_contacts = 1e6;
        $max_chunk = 1e4;

        $output = new ConsoleOutput();

        $output->writeln('<info>Generate random data...</info>');

        $progress = new ProgressBar($output, $max_contacts);
        $progress->start();

        for ($i = 0; $i < $max_contacts; $i++) {
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

        $progress->setMaxSteps((int)($max_contacts / $max_chunk));
        $progress->start();

        foreach (collect($contacts)->chunk($max_chunk) as $contacts_chunk) {
            $db->insert($contacts_chunk->toArray());
            $progress->advance();
        }

        $progress->finish();
        $output->writeln("\r\n<info>Done!</info>");

        return 0;
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
