<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Action;
use App\Models\Ingredient;
use Illuminate\Support\Facades\File;

class ImportActions extends Command
{
    protected $signature = 'import:actions {file=actions.json}';

    protected $description = 'Import actions and ingredients from JSON file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!File::exists($file)) {
            $this->error("File $file not found");
            return 1;
        }

        $json = File::get($file);
        $actions = json_decode($json, true);

        foreach ($actions as $data) {
            $action = Action::updateOrCreate(
                ['name' => $data['name']],
                [
                    'image' => $data['image'] ?? null,
                    'level' => $data['level'] ?? null,
                    'xp' => $data['xp'] ?? null,
                    'quantity' => $data['quantity'] ?? null,
                    'buy' => $data['costs']['buy'] ?? null,
                    'sell' => $data['costs']['sell'] ?? null,
                    'margin' => $data['costs']['margin'] ?? null,
                    'margin_percent' => $data['costs']['margin_percent'] ?? null,
                    'members_only' => $data['members_only'] ?? false,
                ]
            );

            if (!empty($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredientData) {
                    $action->ingredients()->updateOrCreate(
                        ['name' => $ingredientData['name']],
                        [
                            'image' => $ingredientData['image'] ?? null,
                            'quantity' => $ingredientData['quantity'] ?? null,
                        ]
                    );
                }
            }
        }

        $this->info("Imported ".count($actions)." actions.");
        return 0;
    }
}
