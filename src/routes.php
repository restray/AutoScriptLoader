<?php

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

Route::get('javascript', function () {
    // Init the paths values
    $views_path = config('view.paths')[0];
    $hash_parameters = Input::get('generate');

    try {
        // Import the encrypter and setup the key
        $crypt = new Encrypter(env('APP_JS_KEY'));

        // Transform the encrypted key into a readable name
        $decrypted = $crypt->decrypt($hash_parameters);

        // Transform the name into path
        $name_path = str_replace('.', '/', implode('/', $decrypted));

    } catch (Illuminate\Contracts\Encryption\DecryptException $e) {
        return config('distribution.error');
    }

    // Create the file path
    $file_path = $views_path.'/'.$name_path.'/script.js';

    // If the file exist
    if (file_exists($file_path)) {
        // Open it
        $file = fopen($file_path, 'r');

        // Read the content
        $contents = fread($file, filesize($file_path));

        // Close the file
        fclose($file);

        // Create the header for the JS and stock it as header on the view
        $headers = ['Content-Type'=>'application/javascript'];
        View::share('headers', $headers);

        // Return the content
        return $contents;
    }
    // If the file doens't exist
    else {
        return config('distribution.error');
    }
})->name('js');
