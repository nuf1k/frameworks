<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Csv;
use App\Models\Framework;


class UploadCsvController extends Controller
{
    public static function postFrameworksFromFile($csv)
    {
        $path = $csv->store('public/csvs');
        $name = $csv->getClientOriginalName();

        $save = new Csv();
        $save->name = $name;
        $save->path = $path;
        $save->save();

        $rawData = array_map('str_getcsv', file($csv));
        array_shift($rawData);
        $parsedData = array();

        foreach ($rawData as $line) {
            print_r($line);
            $frameworkEntry = [
                'name' => $line[0],
                'repository' => $line[1],
                'description' => $line[2],
                'language' => $line[3],
                'licence' => $line[4],
                'tags' => $line[5],
                'stars' => $line[6]
            ];
            array_push($parsedData, $frameworkEntry);
        }

        Framework::insert($parsedData);

        return response()->json([
            "success" => true,
            "message" => "File successfully uploaded",
            "file" => $name,
        ]);
    }
}
