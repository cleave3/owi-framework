<?php
$controllername = readline("Enter controller name e.g TestController: ");
$contollers = explode(" ", $controllername);

$output = "";

foreach ($contollers as $name) {
    if (!preg_match('/^[A-Za-z]+$/', $name)) {
        $output .= "\e[31m$name is invalid. operation unsuccessfull\n";
    } else {
        $name = trim(preg_replace('/controller/', 'Controller', ucwords($name)));

        if (!preg_match('/controller/i', $name)) {
            $name .= "Controller";
        }

        $file = fopen("./controllers/{$name}.php", "w");

        $code = "<?php\n\nnamespace App\controllers;\n\nclass {$name} extends Controller\n{\n\n\tpublic function index()\n\t{\n\t}\n\n\tpublic function add()\n\t{\n\t}\n\n\tpublic function edit()\n\t{\n\t}\n\n\tpublic function delete()\n\t{\n\t}\n}\n";
        fwrite($file, $code);
        fclose($file);

        $output .= "\e[92m controllers/{$name}.php created successfully\n";
    }
}
print $output;
