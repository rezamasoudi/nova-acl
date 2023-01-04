<?php

namespace Masoudi\NovaAcl\Console;

use Illuminate\Console\Command;

class Translate extends Command
{
    protected $signature = "acl:translate {lang=en}";
    protected $description = "Translate ACL language";

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lang = $this->argument('lang');
        $langPath = __DIR__ . "/../../export/resources/lang/$lang.json";

        if (!file_exists($langPath)) {
            $this->error("translate is not available for `$lang` language!");
            return 1;
        }

        $existLangPath = lang_path("$lang.json");
        $data = [];

        if (file_exists($existLangPath)) {
            $decodedLang = json_decode(file_get_contents($existLangPath), true);
            if (is_array($decodedLang)) {
                $data = $decodedLang;
            }
        }

        $decodedTranslate = json_decode(file_get_contents($langPath), true);
        if (!is_array($decodedTranslate)) {
            $this->error("Error on decode language file. Please report bug.");
            return 1;
        }

        $data = array_merge($data, $decodedTranslate);

        file_put_contents($existLangPath, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $this->info("ACL translated.");

        return 0;
    }
}