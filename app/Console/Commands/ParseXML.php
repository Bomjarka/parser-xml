<?php

namespace App\Console\Commands;

use App\Models\Offer;
use App\Services\XMLService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ParseXML extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse-xml {--path=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parsing XML file and put data to table Cars';

    public const DEFAULT_NAME = 'data_light.xml';

    /**
     * Execute the console command.
     *
     */
    public function handle(XMLService $XMLService): void
    {
        if ($this->option('path') == 'default') {
            $path = app_path() . '/../' . self::DEFAULT_NAME;
        } else {
            $path = $this->option('path');
        }

        if (!File::exists($path)) {
            $this->warn('File not found at: ' . $path);
        } else {
            $databaseOffers = Offer::all();
            $xmlOffers = $XMLService->getDataFromXMLAsArray($path)['offers'];

            if (!array_key_exists('id', $xmlOffers['offer'])) {
                $xmlOffers = $xmlOffers['offer'];
            }

            if ($databaseOffers->isEmpty()) {
                $this->firstDataInsert($xmlOffers);
            } else {
                $this->processOffers($xmlOffers, $databaseOffers);
            }
        }

        $this->info('Done!');
    }

    /**
     * @param array $xmlOffer
     * @return array
     */
    private function prepareData(array $xmlOffer): array
    {
        $data = [];

        foreach ($xmlOffer as $key => $value) {
            if (!empty($value)) {
                $data[$key] = $value;
            } else {
                $data[$key] = null;
            }
            $data['external_id'] = $data['id'];
        }

        return Arr::except($data, ['id']);
    }

    /**
     * @param array $xmlOffers
     * @return void
     */
    private function firstDataInsert(array $xmlOffers): void
    {
        $this->info('Make first insert, total rows: ' . count($xmlOffers));

        foreach ($xmlOffers as $xmlOffer) {
            $data = $this->prepareData($xmlOffer);
            $data['created_at'] = Carbon::now();
            $data['updated_at'] = Carbon::now();
            DB::table('offers')->insert($data);
        }
    }

    /**
     * @param array $xmlOffers
     * @param Collection $databaseOffers
     * @return void
     */
    private function processOffers(array $xmlOffers, Collection $databaseOffers): void
    {
        $this->info('Processing offers, rows at XML:' . count($xmlOffers));
        $idsFromXML = [];
        foreach ($xmlOffers as $xmlOffer) {
            $offer = $databaseOffers->where('external_id', '=', $xmlOffer['id'])->first();
            $data = $this->prepareData($xmlOffer);
            if ($offer) {
                $this->info('Updating offer: ' . $offer->id);
                $offer->update($data);
            } elseif (is_null($offer)) {
                $this->info('Creating offer: ' . $data['external_id']);
                Offer::create($data);
            }
            $idsFromXML[] = $xmlOffer['id'];
        }

        $this->removeOffers($idsFromXML);
    }

    /**
     * @param array $idsFromXML
     * @return void
     */
    private function removeOffers(array $idsFromXML): void
    {

        $offers = Offer::whereNotIn('external_id', $idsFromXML);
        $this->info('Deleting offers, total count: ' . $offers->count());
        $offers->delete();
    }
}
