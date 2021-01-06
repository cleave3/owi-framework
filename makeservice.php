<?php
$servicename = readline("Enter service name e.g TestService: ");
$services = explode(" ", $servicename);

$output = "";

foreach ($services as $name) {
    if (!preg_match('/^[A-Za-z]+$/', $name)) {
        $output .= "\e[31m$name is invalid. operation unsuccessfull\n";
    } else {

        $name = trim(preg_replace('/service/', 'Service', ucwords($servicename)));
        if (!preg_match('/service/i', $name)) {
            $name .= "Service";
        }

        $file = fopen("./src/services/{$name}.php", "w");

        $code = "<?php\n\nnamespace App\services;\n\n\nclass {$name}\n{\n}\n";
        fwrite($file, $code);
        fclose($file);
        $output .= "\e[92m src/services/{$name}.php created successfully\n";
    }
}
print $output;
