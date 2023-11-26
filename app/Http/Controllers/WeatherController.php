<?php

namespace App\Http\Controllers;

use GuzzleHttp\Psr7\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use SoapVar;

class WeatherController extends Controller
{
    private $apikey;
    private $url;
    private $location;
    private $backend_url;

    public function __construct()
    {
       $this->apikey = env('API_KEY');
       $this->url = env('API_URL');
       $this->location = env('LOCATION_URL');
       $this->backend_url = env('BACKEND_URL');
    }

    // Get semua list lokasi
    private function getAllLocation(){
        $file = File::get(base_path('city.list.json'));
        $data = json_decode($file);

        return $data;
    }

    // Menampilkan page
    public function show($location = "Malang")
    {
        // dd($this->getLocation());
        dd($this->fetchData($this->getLocation($location)->lat,$this->getLocation($location)->lon));
        // dd(base_path('city.list.json'));
        // dd($this->getAllLocation());

        return view('weather-index')->with([
            'key' => $this->apikey,
            'data' => $this->fetchData($this->getLocation($location)->lat,$this->getLocation($location)->lon),
            'location' => $this->getLocation("location"),
        ]);
    }

    // Get lan dan lon dari input query Lokasi
    private function getLocation($location) {
        $response = Http::get($this->location, [
            'q' => $location,
            'limit' => '1',
            'appid' => $this->apikey
        ]);

        $result = json_decode($response->body())[0];

        return $result;

    }

    private function fetchData($lat, $lon){
        //lat = latitude
        //lon = longitude

        $response = Http::get($this->url, [
            'lat' => $lat,
            'lon' => $lon,
            'appid' => $this->apikey
        ]);

        $result = json_decode($response->body());

        return $result;

    }

    public function fetchDataFromBackend($msg = "malang") {
        $response = Http::get($this->backend_url."/get-data"."/".$msg, [
            'message' => $msg
        ]);

        // dd($response);

        $result = json_decode($response->body());

        dd($result);

        return $result;
    }
}
