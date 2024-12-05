<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;

class MakeTrait extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:trait {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new trait';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Get the name of the trait from the command argument
        $name = $this->argument('name');
        
        // Define the path where the trait will be created
        $traitPath = app_path("Traits/{$name}.php");

        // Check if the trait already exists
        if (file_exists($traitPath)) {
            $this->error("Trait {$name} already exists!");
            return;
        }

        // Define the content for the trait (a basic template)
        $stub = "<?php\n\nnamespace App\Traits;\n\ntrait {$name}\n{\n    // Add your methods here\n}";

        // Create the trait file
        file_put_contents($traitPath, $stub);

        // Inform the user that the trait was created successfully
        $this->info("Trait {$name} created successfully at {$traitPath}");
    }

}
