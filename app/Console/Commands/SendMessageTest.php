<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Actions\SendMessage;

class SendMessageTest extends Command
{
    protected $signature = 'sms:test 
                            {recipient? : Phone number (optional)} 
                            {message? : Message content (optional)}';

    protected $description = 'Send a test SMS using the SMS gateway API';

    public function handle()
    {
        // Argument or ask interactively
        $recipient = $this->argument('recipient')
            ?: $this->ask('Enter recipient phone number (e.g. +1234567890)');

        // Validate empty input
        if (! $recipient) {
            $this->error('Recipient is required.');
            return Command::FAILURE;
        }

        // Argument or ask interactively
        $message = $this->argument('message')
            ?: $this->ask('Enter the SMS message');

        if (! $message) {
            $this->error('Message cannot be empty.');
            return Command::FAILURE;
        }

        $this->info("Sending SMS to {$recipient}...");

        try {
            $response = (new SendMessage())->execute($recipient, $message);

            $this->info('SMS sent successfully!');
            $this->line(json_encode($response, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error('Failed to send SMS:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
