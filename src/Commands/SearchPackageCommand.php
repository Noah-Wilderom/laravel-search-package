<?php

namespace NoahWilderom\SearchPackage\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use function Laravel\Prompts\search;

class SearchPackageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:package';

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
        $search = search(
            label: 'Search a package',
            options: fn(string $value) => strlen($value) > 0
                ? array_map(fn(array $item) => $item['name'], $this->searchPackages($value))
                : [],
        );
        exec(sprintf('composer require %s', $search));
    }

    public function searchPackages(string $search): array
    {
        if ($search === '') {
            return [];
        }

        $client = new Client([
            'timeout'         => 0,
            'allow_redirects' => false,
        ]);

        try {
            $request = $client->request("GET", config('laravel-search-package.packagist.url'), [
                'query'   => [
                    'q' => $search,
                ],
                'headers' => [
                    'Accept' => 'application/json'
                ],
            ]);
        } catch (\Exception $e) {
            return [];
        }

        if ($request->getStatusCode() !== 200) {
            return [];
        }

        return json_decode($request->getBody()->getContents(), true)['results'];
    }
}